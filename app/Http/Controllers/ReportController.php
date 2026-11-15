<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Orders;
use App\Services\RecapService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman rekap keuangan yang dapat dicetak (khusus pemilik, SRS-HK-003).
 * Rentang "dari–sampai" dipilih per jenis periode; Dari = Sampai berarti satu periode.
 */
class ReportController extends Controller
{
    /** Rentang bawaan tiap jenis rekap bila Dari/Sampai belum dipilih: jumlah periode terakhir. */
    private const DEFAULT_SPAN = ['harian' => 7, 'mingguan' => 4, 'bulanan' => 6, 'tahunan' => 2];

    public function __construct(private readonly RecapService $recaps) {}

    public function index(Request $request): View|RedirectResponse
    {
        $period = $request->queryEnum('periode', RecapService::periods()) ?? 'bulanan';
        $to = $this->parseDate($request->query('sampai')) ?? now()->toImmutable();
        $from = $this->parseDate($request->query('dari'))
            ?? RecapService::startOf($period, $to)->sub(RecapService::PERIODS[$period][0], self::DEFAULT_SPAN[$period] - 1);
        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $rowCount = RecapService::buckets($period, $from, $to)->count();
        if ($rowCount > RecapService::maxRows($period)) {
            return redirect()->route('report')->withErrors([
                'sampai' => sprintf('Rentang terlalu panjang: maksimal %d periode %s dalam satu rekap.', RecapService::maxRows($period), $period),
            ]);
        }

        return view('Page.Admin.Report.Index', [
            'recap' => $this->recaps->build($period, $from, $to),
            'periods' => RecapService::periods(),
            'options' => $period === 'harian' ? null : RecapService::options($period, $this->earliestActivity()),
        ]);
    }

    protected function parseDate(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('!Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    /** Tanggal transaksi paling awal, untuk membatasi pilihan periode di dropdown (minimal 2 tahun ke belakang). */
    protected function earliestActivity(): CarbonImmutable
    {
        $dates = array_filter([
            Orders::query()->min('created_at'),
            Invoice::query()->min('paid_at'),
            Expense::query()->min('spent_at'),
        ]);
        $earliest = $dates ? CarbonImmutable::parse(min($dates)) : now();

        return $earliest->min(now()->subYears(2))->toImmutable();
    }
}
