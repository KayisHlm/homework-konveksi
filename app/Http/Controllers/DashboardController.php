<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Orders;
use App\Models\Suppliers;
use Carbon\Carbon;

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
        $paidInvoiceCount = Invoice::query()->where('payment_status', 'paid')->count();
        $outstandingInvoiceCount = Invoice::query()->whereIn('payment_status', ['sent', 'unpaid'])->count();
        $pendingOrderCount = Orders::query()->where('payment_status', 'pending')->count();
        $newCustomersThisMonth = Customer::query()->where('created_at', '>=', now()->startOfMonth())->count();
        $newInvoicesThisMonth = Invoice::query()->where('created_at', '>=', now()->startOfMonth())->count();
        $newOrdersThisMonth = Orders::query()->where('created_at', '>=', now()->startOfMonth())->count();

        $recentOrders = Orders::query()->with('customer')->orderByDesc('id')->limit(5)->get();

        $startMonth = now()->startOfMonth()->subMonths(11);
        $months = collect(range(0, 11))->map(
            fn (int $index) => $startMonth->copy()->addMonths($index)
        );

        $invoices = Invoice::query()
            ->select('issued_at', 'payment_status', 'total_amount')
            ->where('issued_at', '>=', $startMonth)
            ->get();

        $orders = Orders::query()
            ->select('created_at', 'product_name')
            ->where('created_at', '>=', $startMonth)
            ->get();

        $overviewLabels = $months
            ->map(fn (Carbon $month) => $month->format('M Y'))
            ->values();

        $paidSalesSeries = $months
            ->map(fn (Carbon $month) => round(
                $invoices
                    ->filter(
                        fn (Invoice $invoice) => $invoice->payment_status === 'paid'
                            && $invoice->issued_at?->format('Y-m') === $month->format('Y-m')
                    )
                    ->sum('total_amount'),
                2
            ))
            ->values();

        $outstandingSalesSeries = $months
            ->map(fn (Carbon $month) => round(
                $invoices
                    ->filter(
                        fn (Invoice $invoice) => \in_array($invoice->payment_status, ['sent', 'unpaid'], true)
                            && $invoice->issued_at?->format('Y-m') === $month->format('Y-m')
                    )
                    ->sum('total_amount'),
                2
            ))
            ->values();

        $orderSeries = $months
            ->map(fn (Carbon $month) => $orders
                    ->filter(fn (Orders $order) => $order->created_at?->format('Y-m') === $month->format('Y-m'))
                    ->count())
            ->values();

        $salesByProduct = Orders::query()
            ->select('product_name')
            ->whereNotNull('product_name')
            ->get()
            ->groupBy(fn (Orders $order) => trim((string) $order->product_name))
            ->map(fn ($group, string $productName) => [
                'name' => $productName !== '' ? $productName : 'Tanpa Nama',
                'orders' => $group->count(),
            ])
            ->sortByDesc('orders')
            ->take(5)
            ->values();

        return view('Page.Admin.Dashboard.Index', compact(
            'totalCustomers',
            'totalOrders',
            'totalSuppliers',
            'totalContacts',
            'totalInvoices',
            'totalSalesPaid',
            'totalOutstanding',
            'paidInvoiceCount',
            'outstandingInvoiceCount',
            'pendingOrderCount',
            'newCustomersThisMonth',
            'newInvoicesThisMonth',
            'newOrdersThisMonth',
            'recentOrders',
            'overviewLabels',
            'paidSalesSeries',
            'outstandingSalesSeries',
            'orderSeries',
            'salesByProduct',
        ));
    }
}
