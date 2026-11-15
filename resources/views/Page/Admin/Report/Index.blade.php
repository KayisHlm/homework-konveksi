@extends('Layout.Admin._Layout')
@section('Content')
@php
    $period = $recap['period'];
    $rows = $recap['rows'];
    $totals = $recap['totals'];
    $firstLabel = $rows[0]['label'];
    $lastLabel = $rows[count($rows) - 1]['label'];
    $rangeLabel = count($rows) === 1 ? $firstLabel : "$firstLabel – $lastLabel";
    $selectedFrom = $recap['from']->toDateString();
    $selectedTo = \App\Services\RecapService::startOf($period, $recap['to'])->toDateString();
@endphp
<div class="page-content report-page">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Rekap Keuangan</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Rekap</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card d-print-none">
            <div class="card-body">
                <form method="GET" action="{{ route('report') }}" class="row g-3 align-items-end" id="report-filter">
                    <div class="col-md-3">
                        <label class="form-label" for="periode">Jenis rekap</label>
                        <select name="periode" id="periode" class="form-select">
                            @foreach ($periods as $option)
                                <option value="{{ $option }}" @selected($period === $option)>{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @foreach (['dari' => [$selectedFrom, 'Dari'], 'sampai' => [$selectedTo, 'Sampai']] as $field => [$value, $label])
                        <div class="col-md-3">
                            <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                            @if ($options === null)
                                <input type="date" name="{{ $field }}" id="{{ $field }}" class="form-control @error($field) is-invalid @enderror" value="{{ $value }}" max="{{ now()->toDateString() }}" />
                            @else
                                <select name="{{ $field }}" id="{{ $field }}" class="form-select @error($field) is-invalid @enderror">
                                    @foreach ($options as $key => $optionLabel)
                                        <option value="{{ $key }}" @selected($key === $value)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Tampilkan</button>
                        <button type="button" class="btn btn-outline-primary flex-fill" onclick="window.print()">
                            <i class="bx bx-printer me-1"></i>Cetak
                        </button>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">
                            Pilih periode yang sama pada "Dari" dan "Sampai" untuk merekap satu {{ ['harian' => 'hari', 'mingguan' => 'minggu (Senin–Minggu)', 'bulanan' => 'bulan', 'tahunan' => 'tahun'][$period] }} saja.
                        </small>
                    </div>
                </form>
            </div>
        </div>

        <div class="card report-sheet">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 border-bottom pb-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('build/assets/images/homework-konveksi.png') }}" alt="Homework Konveksi" class="rounded-circle border" style="width: 64px; height: 64px; object-fit: cover;" />
                        <div>
                            <div class="fw-bold fs-4">Homework Konveksi</div>
                            <div class="text-muted">Rekap Keuangan {{ ucfirst($period) }}</div>
                        </div>
                    </div>
                    <div class="text-md-end">
                        <div class="fw-semibold fs-5">{{ $rangeLabel }}</div>
                        <div class="text-muted small">{{ $recap['from']->format('d M Y') }} s.d. {{ $recap['to']->format('d M Y') }}</div>
                        <div class="text-muted small">Dicetak {{ now()->format('d M Y H:i') }} oleh {{ auth()->user()->name }}</div>
                    </div>
                </div>

                <div class="row g-3 mb-4 report-summary">
                    @foreach ([
                        ['Pesanan baru', $totals['orders'] . ' pesanan', 'Nilai ' . Status::rupiah($totals['order_value'])],
                        ['Pemasukan', Status::rupiah($totals['income']), 'Pembayaran diterima'],
                        ['Pengeluaran', Status::rupiah($totals['expense']), 'Bahan, produksi & operasional'],
                        ['Laba', Status::rupiah($totals['profit']), 'Pemasukan − pengeluaran'],
                    ] as [$title, $value, $hint])
                        <div class="col-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="text-muted small">{{ $title }}</div>
                                <div class="fw-bold fs-5 {{ $title === 'Laba' && $totals['profit'] < 0 ? 'text-danger' : '' }}">{{ $value }}</div>
                                <div class="text-muted small">{{ $hint }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <h5 class="mb-2">Ringkasan per {{ ['harian' => 'hari', 'mingguan' => 'minggu', 'bulanan' => 'bulan', 'tahunan' => 'tahun'][$period] }}</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0 report-table">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th>Periode</th>
                                <th class="text-end">Pesanan Baru</th>
                                <th class="text-end">Nilai Pesanan</th>
                                <th class="text-end">Pemasukan</th>
                                <th class="text-end">Pengeluaran</th>
                                <th class="text-end">Laba</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="text-end">{{ $row['orders'] }}</td>
                                    <td class="text-end">{{ Status::rupiah($row['order_value']) }}</td>
                                    <td class="text-end">{{ Status::rupiah($row['income']) }}</td>
                                    <td class="text-end">{{ Status::rupiah($row['expense']) }}</td>
                                    <td class="text-end {{ $row['profit'] < 0 ? 'text-danger' : '' }}">{{ Status::rupiah($row['profit']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-semibold">
                            <tr>
                                <td>Total</td>
                                <td class="text-end">{{ $totals['orders'] }}</td>
                                <td class="text-end">{{ Status::rupiah($totals['order_value']) }}</td>
                                <td class="text-end">{{ Status::rupiah($totals['income']) }}</td>
                                <td class="text-end">{{ Status::rupiah($totals['expense']) }}</td>
                                <td class="text-end {{ $totals['profit'] < 0 ? 'text-danger' : '' }}">{{ Status::rupiah($totals['profit']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <h5 class="mb-2">Pengeluaran per kategori</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0 report-table" style="max-width: 480px;">
                        <tbody>
                            @forelse ($recap['expensesByCategory'] as $category => $amount)
                                <tr>
                                    <td>{{ Status::label($category) }}</td>
                                    <td class="text-end">{{ Status::rupiah($amount) }}</td>
                                </tr>
                            @empty
                                <tr><td class="text-muted">Tidak ada pengeluaran pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h5 class="mb-2">Rincian pemasukan</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0 report-table">
                        <thead class="bg-light bg-opacity-50">
                            <tr><th>Tanggal Bayar</th><th>No. Invoice</th><th>No. Pesanan</th><th>Pelanggan</th><th>Jenis</th><th>Metode</th><th class="text-end">Nominal</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($recap['payments'] as $invoice)
                                <tr>
                                    <td>{{ $invoice->paid_at->format('d M Y') }}</td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->order->order_number }}</td>
                                    <td>{{ $invoice->order->customer?->name ?? '-' }}</td>
                                    <td>{{ Status::label($invoice->type) }}</td>
                                    <td>{{ Status::label($invoice->payment_method) }}</td>
                                    <td class="text-end">{{ Status::rupiah($invoice->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-muted">Belum ada pembayaran yang diterima pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h5 class="mb-2">Rincian pengeluaran</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0 report-table">
                        <thead class="bg-light bg-opacity-50">
                            <tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>No. Pesanan</th><th class="text-end">Nominal</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($recap['expenses'] as $expense)
                                <tr>
                                    <td>{{ $expense->spent_at->format('d M Y') }}</td>
                                    <td>{{ Status::label($expense->category) }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ $expense->order?->order_number ?? 'Operasional' }}</td>
                                    <td class="text-end">{{ Status::rupiah($expense->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">Tidak ada pengeluaran pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h5 class="mb-2">Pesanan baru</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0 report-table">
                        <thead class="bg-light bg-opacity-50">
                            <tr><th>Tanggal</th><th>No. Pesanan</th><th>Pelanggan</th><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Total</th><th>Pembayaran</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($recap['orders'] as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer?->name ?? '-' }}</td>
                                    <td>{{ $order->productSummary() }}</td>
                                    <td class="text-end">{{ $order->totalQuantity() }}</td>
                                    <td class="text-end">{{ Status::rupiah($order->total_price) }}</td>
                                    <td>{{ Status::label($order->payment_status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-muted">Tidak ada pesanan baru pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p class="text-muted small mb-0">
                    Pemasukan dihitung dari invoice berstatus lunas menurut tanggal bayar. Pengeluaran menurut tanggal pengeluaran, termasuk pengadaan bahan baku. Pesanan baru menurut tanggal pesanan dicatat.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Ganti jenis rekap: muat ulang dengan rentang bawaan jenis tersebut (Dari/Sampai lama tidak dibawa).
    document.getElementById('periode').addEventListener('change', (event) => {
        window.location = `{{ route('report') }}?periode=${encodeURIComponent(event.target.value)}`
    });
</script>
<style>
    @page {
        size: A4;
        margin: 14mm;
    }

    @media print {
        .topbar,
        .main-nav,
        .footer,
        .page-title-box,
        .d-print-none,
        #theme-settings-offcanvas {
            display: none !important;
        }

        body,
        .wrapper,
        .page-content,
        .container-xxl,
        .report-page {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .report-sheet {
            border: 0 !important;
            box-shadow: none !important;
        }

        .report-sheet .card-body {
            padding: 0 !important;
        }

        .report-table tr,
        .report-summary > div {
            break-inside: avoid;
        }

        .report-table thead {
            display: table-header-group;
        }

        .table-responsive {
            overflow: visible !important;
        }
    }
</style>
@endpush
