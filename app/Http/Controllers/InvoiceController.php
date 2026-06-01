<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    //
    public function index(Request $request)
    {
        $invoicesQuery = Invoice::query()->with('customer');

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $invoicesQuery->where(function ($query) use ($q) {
                $query->where('invoice_number', 'like', "%{$q}%")->orWhereHas('customer', function ($customerQuery) use ($q) {
                    $customerQuery->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            });
        }

        $status = $request->queryEnum('status', ['draft', 'sent', 'unpaid', 'paid', 'cancelled']);
        if ($status !== null) {
            $invoicesQuery->where('payment_status', $status);
        }

        $invoices = $invoicesQuery->orderByDesc('issued_at')->orderByDesc('id')->get();

        return view('Page.Admin.Invoice.Index', compact('invoices'));
    }
}
