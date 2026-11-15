<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Services\RecapService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Dashboard pemilik (SRS-HK-003): pemasukan, pengeluaran, laba, piutang, dan rekap per periode.
 * Pemasukan dihitung dari invoice lunas berdasarkan tanggal bayar (basis kas).
 */
class DashboardController extends Controller
{
    /** Jumlah periode terakhir yang ditampilkan di grafik untuk tiap jenis rekap. */
    private const CHART_BUCKETS = ['harian' => 14, 'mingguan' => 12, 'bulanan' => 12, 'tahunan' => 5];

    public function index(Request $request, RecapService $recaps): View
    {
        $period = $request->queryEnum('periode', RecapService::periods()) ?? 'bulanan';
        $unit = RecapService::PERIODS[$period][0];
        $from = RecapService::startOf($period, now())->sub($unit, self::CHART_BUCKETS[$period] - 1);
        $recap = $recaps->build($period, $from, now());
        $rows = collect($recap['rows']);

        $activeOrders = Orders::query()->where('payment_status', '!=', Orders::PAYMENT_STATUS_LUNAS)->withSum(
            ['invoices as paid_sum' => fn ($query) => $query->where('status', Invoice::STATUS_LUNAS)],
            'amount',
        )->get(['id', 'total_price']);

        $statusCounts = Orders::query()->selectRaw('production_status, count(*) as total')->groupBy('production_status')->pluck('total', 'production_status');

        return view('Page.Admin.Dashboard.Index', [
            'period' => $period,
            'periods' => RecapService::periods(),
            // Parameter untuk tombol "Cetak Rekap" agar membuka rentang yang sama dengan grafik.
            'reportQuery' => ['periode' => $period, 'dari' => $recap['from']->toDateString(), 'sampai' => now()->toDateString()],
            'totalSalesPaid' => $recap['totals']['income'],
            'paidInvoiceCount' => $recap['payments']->count(),
            'totalExpenses' => $recap['totals']['expense'],
            'profit' => $recap['totals']['profit'],
            // Piutang: sisa tagihan seluruh pesanan yang belum lunas (tidak bergantung periode).
            'totalOutstanding' => (float) $activeOrders->sum(fn (Orders $order) => max(0, (float) $order->total_price - (float) $order->paid_sum)),
            'outstandingInvoiceCount' => $activeOrders->count(),
            'totalOrders' => Orders::query()->count(),
            'newOrdersThisMonth' => Orders::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            'ordersByStatus' => collect(Orders::productionStatuses())->mapWithKeys(fn (string $status) => [$status => (int) ($statusCounts[$status] ?? 0)]),
            'recentOrders' => Orders::query()->with(['customer', 'items'])->latest('id')->limit(5)->get(),
            'overviewLabels' => $rows->pluck('short_label'),
            'paidSalesSeries' => $rows->pluck('income'),
            'expenseSeries' => $rows->pluck('expense'),
            'orderSeries' => $rows->pluck('orders'),
            // Produk terlaris berdasarkan jumlah pcs pada item pesanan.
            'salesByProduct' => OrderItem::query()
                ->selectRaw('product_name as name, sum(quantity) as orders')
                ->groupBy('product_name')
                ->orderByDesc('orders')
                ->limit(5)
                ->get()
                ->map(fn ($row) => ['name' => $row->name, 'orders' => (int) $row->orders]),
        ]);
    }
}
