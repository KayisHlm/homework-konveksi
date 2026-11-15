<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request): View
    {
        $ordersQuery = Orders::query()->with(['customer', 'items']);

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $ordersQuery->where(function ($query) use ($q) {
                $query
                    ->where('order_number', 'like', "%{$q}%")
                    ->orWhereHas('items', fn ($itemQuery) => $itemQuery->where('product_name', 'like', "%{$q}%"))
                    ->orWhereHas('customer', function ($customerQuery) use ($q) {
                        $customerQuery->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $productionStatus = $request->queryEnum('production_status', Orders::productionStatuses());
        if ($productionStatus !== null) {
            $ordersQuery->where('production_status', $productionStatus);
        }

        $paymentStatus = $request->queryEnum('payment_status', Orders::paymentStatuses());
        if ($paymentStatus !== null) {
            $ordersQuery->where('payment_status', $paymentStatus);
        }

        match ($request->queryEnum('sort', ['oldest', 'due_date', 'customer_asc', 'customer_desc', 'total_desc', 'total_asc'])) {
            'oldest' => $ordersQuery->orderBy('created_at'),
            'due_date' => $ordersQuery->orderBy('due_date')->orderByDesc('created_at'),
            'customer_asc' => $ordersQuery->orderBy(Customer::select('name')->whereColumn('customers.id', 'orders.customer_id')),
            'customer_desc' => $ordersQuery->orderByDesc(Customer::select('name')->whereColumn('customers.id', 'orders.customer_id')),
            'total_asc' => $ordersQuery->orderBy('total_price'),
            'total_desc' => $ordersQuery->orderByDesc('total_price'),
            default => $ordersQuery->orderByDesc('created_at')->orderByDesc('id'),
        };

        return view('Page.Admin.Order.Index', [
            'orders' => $ordersQuery->paginate(10)->withQueryString(),
            'productionStatuses' => Orders::productionStatuses(),
            'paymentStatuses' => Orders::paymentStatuses(),
        ]);
    }

    public function create(): View
    {
        return view('Page.Admin.Order.Create', $this->formOptions());
    }

    public function store(OrderRequest $request): RedirectResponse
    {
        $order = $this->orders->save($request->validated());

        return redirect()->route('order-detail', $order)->with('success', 'Pesanan berhasil dibuat.');
    }

    public function edit(Orders $order): View
    {
        return view('Page.Admin.Order.Edit', [
            ...$this->formOptions(),
            'order' => $order->loadMissing(['customer', 'items.sizes']),
        ]);
    }

    public function update(OrderRequest $request, Orders $order): RedirectResponse
    {
        $this->orders->save($request->validated(), $order);

        return redirect()->route('order-detail', $order)->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(Orders $order): RedirectResponse
    {
        if ($order->invoices()->exists()) {
            return back()->with('error', 'Pesanan yang sudah memiliki invoice tidak dapat dihapus.');
        }

        $order->delete();

        return redirect()->route('order')->with('success', 'Pesanan berhasil dihapus.');
    }

    /** Status produksi hanya boleh maju satu langkah (SRS-HK-008). */
    public function updateStatus(Request $request, Orders $order): RedirectResponse
    {
        $request->validate([
            'production_status' => ['required', Rule::in(array_filter([$order->nextProductionStatus()]))],
        ], [
            'production_status.in' => 'Status produksi harus diperbarui secara berurutan.',
        ]);

        $order->forceFill(['production_status' => $request->input('production_status')])->save();

        return back()->with('success', 'Status produksi berhasil diperbarui.');
    }

    protected function formOptions(): array
    {
        $customers = Customer::query()->orderBy('name')->get(['id', 'name', 'email', 'phone', 'address', 'institute']);

        return [
            'customers' => $customers,
            'customersForJs' => $customers->values(),
            'sizes' => OrderItem::SIZES,
            'paymentMethods' => Invoice::paymentMethods(),
        ];
    }
}
