<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Orders;
use App\Models\Suppliers;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $totalCustomers = Customer::query()->count();
        $totalOrders = Orders::query()->count();
        $totalSuppliers = Suppliers::query()->count();
        $totalContacts = Contact::query()->count();
        $totalInvoices = Invoice::query()->count();

        $totalSalesPaid = (float) Invoice::query()->where('payment_status', 'paid')->sum('total_amount');
        $totalOutstanding = (float) Invoice::query()->whereIn('payment_status', ['sent', 'unpaid'])->sum('total_amount');

        $recentOrders = Orders::query()->with('customer')->orderByDesc('id')->limit(5)->get();

        return view('Page.Admin.Dashboard.Index', compact(
            'totalCustomers',
            'totalOrders',
            'totalSuppliers',
            'totalContacts',
            'totalInvoices',
            'totalSalesPaid',
            'totalOutstanding',
            'recentOrders',
        ));
    }
}
