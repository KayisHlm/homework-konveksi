<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Orders;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoicesQuery = Invoice::query()->with(['customer', 'order']);

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $invoicesQuery->where(function ($query) use ($q) {
                $query
                    ->where('invoice_number', 'like', "%{$q}%")
                    ->orWhereHas('order', function ($orderQuery) use ($q) {
                        $orderQuery
                            ->where('order_number', 'like', "%{$q}%")
                            ->orWhere('product_name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('customer', function ($customerQuery) use ($q) {
                        $customerQuery->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $status = $request->queryEnum('status', ['draft', 'sent', 'unpaid', 'paid', 'cancelled']);
        if ($status !== null) {
            $invoicesQuery->where('payment_status', $status);
        }

        $invoices = $invoicesQuery->orderByDesc('issued_at')->orderByDesc('id')->paginate(10)->withQueryString();

        return view('Page.Admin.Invoice.Index', compact('invoices'));
    }

    public function create(Request $request): View
    {
        $order = $request->filled('order_id')
            ? Orders::query()->with('customer')->find($request->query('order_id'))
            : null;

        $invoice = new Invoice([
            'order_id' => $order?->id,
            'customer_id' => $order?->customer_id,
            'issued_at' => now(),
            'due_date' => now()->addDays(14)->toDateString(),
            'payment_status' => 'draft',
            'discount_amount' => 0,
            'items' => $order ? [[
                'product_name' => $order->product_name,
                'quantity' => $order->quantity,
                'unit_price' => $order->unit_price,
            ]] : [[
                'product_name' => '',
                'quantity' => 1,
                'unit_price' => 0,
            ]],
        ]);

        return view('Page.Admin.Invoice.Create', [
            'invoice' => $invoice,
            ...$this->formOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateInvoice($request);
        $invoice = DB::transaction(function () use ($validated) {
            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $validated['customer_id'],
                'order_id' => $validated['order_id'] ?? null,
                'issued_at' => $validated['issued_at'],
                'due_date' => $validated['due_date'] ?? null,
                'payment_status' => $validated['payment_status'],
                'payment_via' => $validated['payment_via'] ?? null,
                'subtotal_amount' => $validated['subtotal_amount'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $invoice->items()->createMany($validated['items']);

            $this->syncOrderPaymentStatus($invoice->order);

            return $invoice;
        });

        return redirect()->route('invoice-detail', $invoice)->with('success', 'Invoice berhasil dibuat.');
    }

    public function edit(Invoice $invoice): View
    {
        $invoice->loadMissing('items');
        $invoice->setAttribute('items', $invoice->items->map(fn ($item) => [
            'product_name' => $item->product_name,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ])->values()->all());

        return view('Page.Admin.Invoice.Edit', [
            'invoice' => $invoice,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $this->validateInvoice($request);

        DB::transaction(function () use ($validated, $invoice) {
            $previousOrder = $invoice->order;

            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'order_id' => $validated['order_id'] ?? null,
                'issued_at' => $validated['issued_at'],
                'due_date' => $validated['due_date'] ?? null,
                'payment_status' => $validated['payment_status'],
                'payment_via' => $validated['payment_via'] ?? null,
                'subtotal_amount' => $validated['subtotal_amount'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($validated['items']);

            $this->syncOrderPaymentStatus($previousOrder);
            $this->syncOrderPaymentStatus($invoice->fresh('order')->order);
        });

        return redirect()->route('invoice-detail', $invoice)->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $order = $invoice->order;
        $invoice->delete();
        $this->syncOrderPaymentStatus($order);

        return redirect()->route('invoice')->with('success', 'Invoice berhasil dihapus.');
    }

    protected function formOptions(): array
    {
        return [
            'customers' => Customer::query()->orderBy('name')->get(),
            'orders' => Orders::query()->with('customer')->orderByDesc('created_at')->get(),
            'invoiceStatuses' => ['draft', 'sent', 'unpaid', 'paid', 'cancelled'],
        ];
    }

    protected function validateInvoice(Request $request): array
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer|exists:customers,id',
            'order_id' => 'nullable|integer|exists:orders,id',
            'issued_at' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issued_at',
            'payment_status' => 'required|in:draft,sent,unpaid,paid,cancelled',
            'payment_via' => 'nullable|string|max:255',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $items = collect($validated['items'])
            ->map(function (array $item) {
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                return [
                    'product_name' => $item['product_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $quantity * $unitPrice,
                ];
            })
            ->values()
            ->all();

        $subtotal = collect($items)->sum('line_total');
        $discount = (float) ($validated['discount_amount'] ?? 0);

        $validated['items'] = $items;
        $validated['subtotal_amount'] = $subtotal;
        $validated['discount_amount'] = $discount;
        $validated['total_amount'] = max(0, $subtotal - $discount);

        if (!empty($validated['order_id']) && empty($request->input('customer_id'))) {
            $order = Orders::query()->findOrFail($validated['order_id']);
            $validated['customer_id'] = $order->customer_id;
        }

        if (empty($validated['customer_id'])) {
            throw ValidationException::withMessages([
                'customer_id' => 'Customer wajib dipilih atau diambil dari pesanan.',
            ]);
        }

        return $validated;
    }

    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('ym') . '-';
        $latest = Invoice::query()
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $lastNumber = $latest ? (int) substr($latest, -4) : 0;

        return $prefix . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    protected function syncOrderPaymentStatus(?Orders $order): void
    {
        if (!$order) {
            return;
        }

        $order->refresh();
        $invoices = $order->invoices()->get();

        $status = 'pending';
        if ($invoices->contains(fn (Invoice $invoice) => $invoice->payment_status === 'paid')) {
            $status = 'paid';
        } elseif ($invoices->isNotEmpty() && $invoices->every(fn (Invoice $invoice) => $invoice->payment_status === 'cancelled')) {
            $status = 'cancelled';
        }

        $order->update(['payment_status' => $status]);
    }
}
