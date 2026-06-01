<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Orders;
use App\Http\Requests\OrderRequest;
class OrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $ordersQuery = Orders::query()->with('customer');

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $ordersQuery->where(function ($query) use ($q) {
                $query
                    ->where('product_name', 'like', "%{$q}%")
                    ->orWhere('id', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($q) {
                        $customerQuery->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $paymentStatus = $request->queryEnum('payment_status', ['pending', 'paid', 'cancelled']);
        if ($paymentStatus !== null) {
            $ordersQuery->where('payment_status', $paymentStatus);
        }

        $paymentType = $request->queryEnum('payment_type', ['cash', 'transfer', 'instalment']);
        if ($paymentType !== null) {
            $ordersQuery->where('payment_type', $paymentType);
        }

        $sort = $request->queryEnum('sort', ['id_desc', 'id_asc', 'date_desc', 'date_asc', 'product_asc', 'product_desc', 'customer_asc', 'customer_desc']);
        if ($sort === 'id_asc') {
            $ordersQuery->orderBy('id');
        } elseif ($sort === 'date_asc') {
            $ordersQuery->orderBy('created_at');
        } elseif ($sort === 'date_desc') {
            $ordersQuery->orderByDesc('created_at');
        } elseif ($sort === 'product_asc') {
            $ordersQuery->orderBy('product_name');
        } elseif ($sort === 'product_desc') {
            $ordersQuery->orderByDesc('product_name');
        } elseif ($sort === 'customer_asc') {
            $ordersQuery->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')->orderBy('customers.name')->select('orders.*');
        } elseif ($sort === 'customer_desc') {
            $ordersQuery->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')->orderByDesc('customers.name')->select('orders.*');
        } else {
            $ordersQuery->orderByDesc('id');
        }

        $orders = $ordersQuery->paginate(10)->withQueryString();

        return view('Page.Admin.Order.Index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::query()->orderBy('name')->get();
        $customersForJs = $customers
            ->map(function (Customer $customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'institute' => $customer->institute,
                ];
            })
            ->values();
        $paymentTypes = ['cash', 'transfer', 'instalment'];
        $paymentStatuses = ['pending', 'paid', 'cancelled'];

        return view('Page.Admin.Order.Create', compact('customers', 'customersForJs', 'paymentTypes', 'paymentStatuses'));
    }

    public function store(OrderRequest $request)
    {
        $validated = $request->validated();

        $customerId = $validated['customer_id'] ?? null;
        if (!$customerId) {
            $existingCustomer = Customer::query()->where('email', $validated['email'])->first();
            if ($existingCustomer) {
                $customerId = $existingCustomer->id;
            } else {
                $newCustomer = Customer::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'institute' => $validated['institute'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]);
                $customerId = $newCustomer->id;
            }
        }

        Orders::create([
            'customer_id' => $customerId,
            'product_name' => $validated['product_name'],
            'payment_type' => $validated['payment_type'],
            'payment_status' => $validated['payment_status'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        return redirect()
            ->route('order')
            ->with('success', 'Order created successfully');
    }
}
