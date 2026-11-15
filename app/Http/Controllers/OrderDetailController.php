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
            'items.sizes',
            'procurements.supplier',
            'expenses.procurement',
            'invoices' => fn ($query) => $query->orderBy('issued_at')->orderBy('id'),
        ]);

        $amountPaid = $order->amountPaid();
        $totalCosts = (float) $order->expenses->sum('amount');

        return view('Page.Admin.OrderDetail.Index', [
            'order' => $order,
            'amountPaid' => $amountPaid,
            'remainingBalance' => $order->remainingBalance(),
            'billableBalance' => $order->billableBalance(),
            // Ringkasan keuangan per pesanan (SRS-HK-017), hanya ditampilkan untuk pemilik.
            'totalCosts' => $totalCosts,
            'profit' => (float) $order->total_price - $totalCosts,
            'productionStatuses' => Orders::productionStatuses(),
        ]);
    }
}
