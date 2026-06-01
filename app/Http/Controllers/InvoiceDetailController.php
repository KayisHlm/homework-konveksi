<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceDetailController extends Controller
{
    //
    public function index(Invoice $invoice)
    {
        $invoice->loadMissing(['customer', 'items']);

        return view('Page.Admin.InvoiceDetail.Index', compact('invoice'));
    }
}
