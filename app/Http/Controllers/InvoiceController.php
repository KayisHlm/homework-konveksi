<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Orders;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoicesQuery = Invoice::query()->with('order.customer');

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $invoicesQuery->where(function ($query) use ($q) {
                $query
                    ->where('invoice_number', 'like', "%{$q}%")
                    ->orWhereHas('order', function ($orderQuery) use ($q) {
                        $orderQuery
                            ->where('order_number', 'like', "%{$q}%")
                            ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$q}%"));
                    });
            });
        }

        $status = $request->queryEnum('status', Invoice::statuses());
        if ($status !== null) {
            $invoicesQuery->where('status', $status);
        }

        $type = $request->queryEnum('type', Invoice::types());
        if ($type !== null) {
            $invoicesQuery->where('type', $type);
        }

        return view('Page.Admin.Invoice.Index', [
            'invoices' => $invoicesQuery->orderByDesc('issued_at')->orderByDesc('id')->paginate(10)->withQueryString(),
            'statuses' => Invoice::statuses(),
            'types' => Invoice::types(),
        ]);
    }

    public function create(Request $request): View
    {
        $orderId = $request->query('order_id');
        $order = is_numeric($orderId) ? Orders::query()->find((int) $orderId) : null;
        $billable = $order?->billableBalance() ?? 0;

        $invoice = new Invoice([
            'order_id' => $order?->id,
            'type' => $order && $order->amountInvoiced() > 0 ? Invoice::TYPE_PELUNASAN : Invoice::TYPE_UANG_MUKA,
            'amount' => $billable ?: null,
            'issued_at' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => Invoice::STATUS_BELUM_DIBAYAR,
        ]);

        return view('Page.Admin.Invoice.Create', ['invoice' => $invoice, ...$this->formOptions()]);
    }

    public function store(InvoiceRequest $request): RedirectResponse
    {
        $invoice = Invoice::create($request->validated());

        return redirect()->route('invoice-detail', $invoice)->with('success', 'Invoice berhasil dibuat.');
    }

    public function edit(Invoice $invoice): View
    {
        return view('Page.Admin.Invoice.Edit', ['invoice' => $invoice, ...$this->formOptions()]);
    }

    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $previousOrder = $invoice->order;

        $invoice->update($request->validated());

        if ($previousOrder->id !== $invoice->order_id) {
            $previousOrder->syncPaymentStatus();
        }

        return redirect()->route('invoice-detail', $invoice)->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === Invoice::STATUS_LUNAS) {
            return back()->with('error', 'Invoice yang sudah lunas tidak dapat dihapus. Batalkan terlebih dahulu bila perlu.');
        }

        $invoice->delete();

        return redirect()->route('invoice')->with('success', 'Invoice berhasil dihapus.');
    }

    protected function formOptions(): array
    {
        $orders = Orders::query()->with(['customer', 'invoices'])->orderByDesc('created_at')->get();

        return [
            'orders' => $orders,
            // Dipakai form untuk menampilkan total & sisa tagihan pesanan terpilih.
            'ordersForJs' => $orders->mapWithKeys(fn (Orders $order) => [$order->id => [
                'total' => (float) $order->total_price,
                'invoiced' => (float) $order->invoices->where('status', '!=', Invoice::STATUS_DIBATALKAN)->sum('amount'),
                'paid' => (float) $order->invoices->where('status', Invoice::STATUS_LUNAS)->sum('amount'),
            ]]),
            'types' => Invoice::types(),
            'statuses' => Invoice::statuses(),
            'paymentMethods' => Invoice::paymentMethods(),
        ];
    }
}
