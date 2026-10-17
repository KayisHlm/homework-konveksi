<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Orders;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $ordersQuery = Orders::query()->with('customer');

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $ordersQuery->where(function ($query) use ($q) {
                $query
                    ->where('product_name', 'like', "%{$q}%")
                    ->orWhere('order_number', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($q) {
                        $customerQuery->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $productionStatus = $request->queryEnum('production_status', Orders::productionStatuses());
        if ($productionStatus !== null) {
            $ordersQuery->where('production_status', $productionStatus);
        }

        $paymentStatus = $request->queryEnum('payment_status', ['pending', 'paid', 'cancelled']);
        if ($paymentStatus !== null) {
            $ordersQuery->where('payment_status', $paymentStatus);
        }

        $paymentType = $request->queryEnum('payment_type', ['cash', 'transfer', 'instalment']);
        if ($paymentType !== null) {
            $ordersQuery->where('payment_type', $paymentType);
        }

        $sort = $request->queryEnum('sort', ['latest', 'oldest', 'due_date', 'product_asc', 'product_desc', 'customer_asc', 'customer_desc', 'total_desc', 'total_asc']);
        if ($sort === 'oldest') {
            $ordersQuery->orderBy('created_at');
        } elseif ($sort === 'due_date') {
            $ordersQuery->orderBy('due_date')->orderByDesc('created_at');
        } elseif ($sort === 'product_asc') {
            $ordersQuery->orderBy('product_name');
        } elseif ($sort === 'product_desc') {
            $ordersQuery->orderByDesc('product_name');
        } elseif ($sort === 'customer_asc') {
            $ordersQuery->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')->orderBy('customers.name')->select('orders.*');
        } elseif ($sort === 'customer_desc') {
            $ordersQuery->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')->orderByDesc('customers.name')->select('orders.*');
        } elseif ($sort === 'total_asc') {
            $ordersQuery->orderBy('total_price')->orderByDesc('created_at');
        } elseif ($sort === 'total_desc') {
            $ordersQuery->orderByDesc('total_price')->orderByDesc('created_at');
        } elseif ($sort === 'date_asc') {
            $ordersQuery->orderByDesc('created_at');
        } else {
            $ordersQuery->orderByDesc('created_at')->orderByDesc('id');
        }

        $orders = $ordersQuery->paginate(10)->withQueryString();
        $productionStatuses = Orders::productionStatuses();

        return view('Page.Admin.Order.Index', compact('orders', 'productionStatuses'));
    }

    public function create(): View
    {
        return view('Page.Admin.Order.Create', $this->formOptions());
    }

    public function store(OrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $customer = $this->storeCustomerFromOrder($validated);

        $order = Orders::create($this->orderPayload($validated, $customer->id));

        return redirect()->route('order-detail', $order)->with('success', 'Pesanan berhasil dibuat.');
    }

    public function edit(Orders $order): View
    {
        return view('Page.Admin.Order.Edit', array_merge(
            $this->formOptions(),
            ['order' => $order->loadMissing('customer')],
        ));
    }

    public function update(OrderRequest $request, Orders $order): RedirectResponse
    {
        $validated = $request->validated();
        $customer = $this->storeCustomerFromOrder($validated);

        $order->update($this->orderPayload($validated, $customer->id));

        return redirect()->route('order-detail', $order)->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(Orders $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('order')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function updateStatus(Request $request, Orders $order): RedirectResponse
    {
        $validated = $request->validate([
            'production_status' => 'nullable|in:' . implode(',', Orders::productionStatuses()),
            'payment_status' => 'nullable|in:pending,paid,cancelled',
        ]);

        $order->fill(array_filter($validated, fn ($value) => $value !== null));
        $order->save();

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    protected function formOptions(): array
    {
        $customers = Customer::query()->orderBy('name')->get();

        return [
            'customers' => $customers,
            'customersForJs' => $customers
                ->map(fn (Customer $customer) => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'institute' => $customer->institute,
                ])
                ->values(),
            'paymentTypes' => ['cash', 'transfer', 'instalment'],
            'paymentStatuses' => ['pending', 'paid', 'cancelled'],
            'productionStatuses' => Orders::productionStatuses(),
        ];
    }

    protected function storeCustomerFromOrder(array $validated): Customer
    {
        if (!empty($validated['customer_id'])) {
            $customer = Customer::query()->findOrFail($validated['customer_id']);
            $customer->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'institute' => $validated['institute'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            return $customer;
        }

        return Customer::query()->updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'institute' => $validated['institute'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ],
        );
    }

    protected function orderPayload(array $validated, int $customerId): array
    {
        return [
            'customer_id' => $customerId,
            'product_name' => $validated['product_name'],
            'material' => $validated['material'],
            'quantity' => $validated['quantity'],
            'size_details' => $validated['size_details'] ?? null,
            'unit_price' => $validated['unit_price'],
            'total_price' => $validated['total_price'],
            'down_payment' => $validated['down_payment'] ?? 0,
            'due_date' => $validated['due_date'] ?? null,
            'production_status' => $validated['production_status'],
            'notes' => $validated['notes'] ?? null,
            'payment_type' => $validated['payment_type'],
            'payment_status' => $validated['payment_status'],
        ];
    }
}
