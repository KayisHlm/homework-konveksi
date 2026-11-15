<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Orders;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Rekap keuangan per periode (SRS-HK-003): pemasukan = invoice lunas menurut tanggal bayar (basis kas),
 * pengeluaran menurut tanggal keluar, pesanan menurut tanggal dibuat.
 * Dipakai oleh dashboard dan halaman cetak rekap.
 */
class RecapService
{
    /** periode => [satuan Carbon, batas jumlah baris] */
    public const PERIODS = [
        'harian' => ['day', 93],
        'mingguan' => ['week', 104],
        'bulanan' => ['month', 60],
        'tahunan' => ['year', 20],
    ];

    public static function periods(): array
    {
        return array_keys(self::PERIODS);
    }

    public static function maxRows(string $period): int
    {
        return self::PERIODS[$period][1];
    }

    /** Awal periode yang memuat $date. Minggu selalu Senin–Minggu, tidak bergantung locale. */
    public static function startOf(string $period, CarbonInterface $date): CarbonImmutable
    {
        $date = CarbonImmutable::instance($date);

        return $period === 'mingguan'
            ? $date->startOfWeek(CarbonInterface::MONDAY)
            : $date->startOf(self::PERIODS[$period][0]);
    }

    public static function endOf(string $period, CarbonInterface $date): CarbonImmutable
    {
        $date = CarbonImmutable::instance($date);

        return $period === 'mingguan'
            ? $date->endOfWeek(CarbonInterface::SUNDAY)
            : $date->endOf(self::PERIODS[$period][0]);
    }

    public static function label(string $period, CarbonInterface $start, bool $short = false): string
    {
        return match ($period) {
            'harian' => $start->format($short ? 'd M' : 'd M Y'),
            'mingguan' => $short ? 'Mg '.$start->isoWeek() : sprintf('Mg %d (%s)', $start->isoWeek(), self::weekSpan($start)),
            'bulanan' => $start->format('M Y'),
            'tahunan' => $start->format('Y'),
        };
    }

    /** "9–15 Nov 2026", "26 Okt–1 Nov 2026", atau "29 Des 2025–4 Jan 2026". */
    private static function weekSpan(CarbonImmutable $start): string
    {
        $end = self::endOf('mingguan', $start);
        $startFormat = match (true) {
            $start->year !== $end->year => 'j M Y',
            $start->month !== $end->month => 'j M',
            default => 'j',
        };

        return $start->format($startFormat).'–'.$end->format('j M Y');
    }

    /** Daftar awal periode dari $from sampai $to (inklusif). */
    public static function buckets(string $period, CarbonInterface $from, CarbonInterface $to): Collection
    {
        $unit = self::PERIODS[$period][0];
        $buckets = collect();
        for ($cursor = self::startOf($period, $from); $cursor->lte($to); $cursor = $cursor->add($unit, 1)) {
            $buckets->push($cursor);
        }

        return $buckets;
    }

    /** Pilihan periode untuk dropdown (terbaru di atas), dari $since sampai hari ini. */
    public static function options(string $period, CarbonInterface $since): Collection
    {
        return self::buckets($period, $since, now())
            ->reverse()
            ->mapWithKeys(fn (CarbonImmutable $start) => [$start->toDateString() => self::label($period, $start)]);
    }

    /**
     * @return array{period: string, from: CarbonImmutable, to: CarbonImmutable, rows: list<array>, totals: array,
     *               payments: Collection, expenses: Collection, expensesByCategory: Collection, orders: Collection}
     */
    public function build(string $period, CarbonInterface $from, CarbonInterface $to): array
    {
        $from = self::startOf($period, $from);
        $to = self::endOf($period, $to);
        // whereDate (bukan whereBetween string) agar kolom tanggal cocok di MySQL maupun SQLite.
        $inRange = fn ($query, string $column) => $query
            ->whereDate($column, '>=', $from->toDateString())
            ->whereDate($column, '<=', $to->toDateString());

        $payments = Invoice::query()
            ->with('order.customer')
            ->where('status', Invoice::STATUS_LUNAS)
            ->where(fn ($query) => $inRange($query, 'paid_at'))
            ->orderBy('paid_at')->orderBy('id')
            ->get();
        $expenses = Expense::query()->with('order')->where(fn ($query) => $inRange($query, 'spent_at'))->orderBy('spent_at')->orderBy('id')->get();
        $orders = Orders::query()
            ->with(['customer', 'items'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $key = fn (CarbonInterface $date) => self::startOf($period, $date)->toDateString();
        $incomeBy = $payments->groupBy(fn (Invoice $invoice) => $key($invoice->paid_at));
        $expenseBy = $expenses->groupBy(fn (Expense $expense) => $key($expense->spent_at));
        $ordersBy = $orders->groupBy(fn (Orders $order) => $key($order->created_at));

        $rows = self::buckets($period, $from, $to)->map(function (CarbonImmutable $start) use ($period, $incomeBy, $expenseBy, $ordersBy) {
            $k = $start->toDateString();
            $income = round((float) ($incomeBy[$k] ?? collect())->sum('amount'), 2);
            $expense = round((float) ($expenseBy[$k] ?? collect())->sum('amount'), 2);

            return [
                'start' => $start,
                'label' => self::label($period, $start),
                'short_label' => self::label($period, $start, true),
                'orders' => ($ordersBy[$k] ?? collect())->count(),
                'order_value' => round((float) ($ordersBy[$k] ?? collect())->sum('total_price'), 2),
                'income' => $income,
                'expense' => $expense,
                'profit' => round($income - $expense, 2),
            ];
        })->values()->all();

        $rowsCollection = collect($rows);

        return [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
            'totals' => [
                'orders' => $orders->count(),
                'order_value' => round((float) $orders->sum('total_price'), 2),
                'income' => round((float) $rowsCollection->sum('income'), 2),
                'expense' => round((float) $rowsCollection->sum('expense'), 2),
                'profit' => round((float) $rowsCollection->sum('profit'), 2),
            ],
            'payments' => $payments,
            'expenses' => $expenses,
            'expensesByCategory' => $expenses->groupBy('category')
                ->map(fn ($group) => round((float) $group->sum('amount'), 2))
                ->sortDesc(),
            'orders' => $orders,
        ];
    }
}
