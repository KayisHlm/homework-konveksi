<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\View\View;

class OrderDetailController extends Controller
{
    public function index(Orders $order): View
    {
        $order->loadMissing([
            'customer',
            'procurements.supplier',
            'expenses.procurement',
            'invoices.items',
        ]);

        $revenue = (float) ($order->invoices->sum('total_amount') ?: $order->total_price);
        $totalCosts = (float) $order->expenses->sum('amount');
        $profit = $revenue - $totalCosts;

        return view('Page.Admin.OrderDetail.Index', [
            'order' => $order,
            'revenue' => $revenue,
            'totalCosts' => $totalCosts,
            'profit' => $profit,
            'productionStatuses' => Orders::productionStatuses(),
            'paymentStatuses' => ['pending', 'paid', 'cancelled'],
        ]);
    }
}
