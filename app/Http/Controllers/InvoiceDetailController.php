<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\View\View;

class InvoiceDetailController extends Controller
{
    public function index(Invoice $invoice): View
    {
        $invoice->loadMissing(['order.customer', 'order.items.sizes']);
        $order = $invoice->order;

        // Pembayaran sebelumnya = invoice lunas yang terbit lebih dulu dari invoice ini
        // (tanggal terbit lebih awal, atau tanggal sama dengan nomor urut lebih kecil).
        $paidBefore = (float) $order->invoices()
            ->where('status', Invoice::STATUS_LUNAS)
            ->where(fn ($query) => $query
                ->whereDate('issued_at', '<', $invoice->issued_at)
                ->orWhere(fn ($same) => $same->whereDate('issued_at', $invoice->issued_at)->where('id', '<', $invoice->id)))
            ->sum('amount');
        $thisAmount = $invoice->status === Invoice::STATUS_DIBATALKAN ? 0 : (float) $invoice->amount;

        return view('Page.Admin.InvoiceDetail.Index', [
            'invoice' => $invoice,
            'order' => $order,
            'subtotal' => (float) $order->items->sum('subtotal'),
            'paidBefore' => $paidBefore,
            'remainingAfter' => max(0, (float) $order->total_price - $paidBefore - $thisAmount),
        ]);
    }
}
