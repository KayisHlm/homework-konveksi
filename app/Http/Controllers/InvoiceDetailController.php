<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\View\View;

class InvoiceDetailController extends Controller
{
    public function index(Invoice $invoice): View
    {
        $invoice->loadMissing(['customer', 'items', 'order.customer']);

        return view('Page.Admin.InvoiceDetail.Index', compact('invoice'));
    }
}
