<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    public function index(Request $request)
    {
        $customersQuery = Customer::query()->withCount('orders');

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $customersQuery->where(function ($query) use ($q) {
                $query
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('institute', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $hasOrders = $request->queryEnum('has_orders', ['0', '1']);
        if ($hasOrders === '1') {
            $customersQuery->has('orders');
        } elseif ($hasOrders === '0') {
            $customersQuery->doesntHave('orders');
        }

        $sort = $request->queryEnum('sort', ['name', 'created_at', 'phone', 'orders']);
        if ($sort === 'name') {
            $customersQuery->orderBy('name');
        } elseif ($sort === 'created_at') {
            $customersQuery->orderByDesc('created_at');
        } elseif ($sort === 'phone') {
            $customersQuery->orderBy('phone');
        } elseif ($sort === 'orders') {
            $customersQuery->orderByDesc('orders_count')->orderBy('name');
        } else {
            $customersQuery->orderByDesc('id');
        }

        $customers = $customersQuery->paginate(10)->withQueryString();

        return view('Page.Admin.Customer.Index', compact('customers'));
    }

    public function create()
    {
        return view('Page.Admin.Customer.Create');
    }

    public function store(CustomerRequest $request)
    {
        Customer::create($request->validated());

        return redirect()
            ->route('customer')
            ->with('success', 'Customer created successfully');
    }

    public function edit(Customer $customer)
    {
        return view('Page.Admin.Customer.Edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customer')
            ->with('success', 'Customer updated successfully');
    }

    /** Pelanggan yang masih memiliki pesanan tidak dapat dihapus agar riwayat pesanan tetap utuh. */
    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'Pelanggan masih memiliki pesanan sehingga tidak dapat dihapus.');
        }

        $customer->delete();

        return redirect()
            ->route('customer')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}
