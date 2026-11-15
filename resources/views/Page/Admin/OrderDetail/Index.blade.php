@extends('Layout.Admin._Layout')
@section('Content')
@php
    $isPemilik = auth()->user()->isPemilik();
    $currentStep = array_search($order->production_status, $productionStatuses, true) ?: 0;
    $nextStatus = $order->nextProductionStatus();
@endphp
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Detail Pesanan</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('order') }}">Pesanan</a></li>
                        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row g-3 align-items-stretch">
            <div class="col-xl-8">
                <div class="card h-100 order-hero-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge badge-soft-primary">{{ $order->order_number }}</span>
                                    <span class="badge badge-soft-{{ Status::badge($order->payment_status) }}">{{ Status::label($order->payment_status) }}</span>
                                </div>
                                <h4 class="mb-1">{{ $order->productSummary() }}</h4>
                                <div class="text-muted">{{ $order->customer?->name ?? '-' }} • {{ $order->customer?->institute ?? '-' }}</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('order.edit', $order) }}" class="btn btn-outline-primary">Edit Pesanan</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-dashed mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Bahan / Warna</th>
                                        <th>Rincian Ukuran</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Harga Dasar</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td class="fw-medium">{{ $item->product_name }}</td>
                                            <td>{{ $item->material }}{{ $item->color ? ' / ' . $item->color : '' }}</td>
                                            <td class="small">{{ $item->sizeSummary() }}</td>
                                            <td class="text-end">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ Status::rupiah($item->unit_price) }}</td>
                                            <td class="text-end">{{ Status::rupiah($item->subtotal) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @if ((float) $order->discount_amount > 0)
                                        <tr>
                                            <td colspan="5" class="text-end">Diskon</td>
                                            <td class="text-end">- {{ Status::rupiah($order->discount_amount) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="4" class="fw-semibold">Total ({{ $order->totalQuantity() }} pcs)</td>
                                        <td colspan="2" class="text-end fw-semibold">{{ Status::rupiah($order->total_price) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card h-100 order-hero-card">
                    <div class="card-body order-summary-card-body">
                        <h5 class="card-title mb-3">Ringkasan Pesanan</h5>
                        <div class="table-responsive text-nowrap table-centered">
                            <table class="table table-sm mb-0 order-summary-table">
                                <tbody>
                                    <tr><td>Tanggal Pesan</td><td class="text-end">{{ $order->created_at?->format('d M Y') }}</td></tr>
                                    <tr><td>Tanggal Jadi</td><td class="text-end">{{ $order->due_date?->format('d M Y') ?? '-' }}</td></tr>
                                    <tr><td>Status Produksi</td><td class="text-end">{{ Status::label($order->production_status) }}</td></tr>
                                    <tr><td>Total Pesanan</td><td class="text-end">{{ Status::rupiah($order->total_price) }}</td></tr>
                                    <tr><td>Sudah Dibayar</td><td class="text-end text-success">{{ Status::rupiah($amountPaid) }}</td></tr>
                                    <tr><td>Sisa Tagihan</td><td class="text-end fw-semibold text-danger">{{ Status::rupiah($remainingBalance) }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1 align-items-stretch">
            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-body progress-card-body">
                        <h5 class="card-title mb-0">Progres Pesanan</h5>
                        <div class="progress-card-center">
                            <ul class="progressbar progressbar-five progressbar-compact ps-0 mb-0">
                                @foreach ($productionStatuses as $index => $status)
                                    <li class="{{ $index <= $currentStep ? 'active' : '' }}">
                                        <span>{{ Status::label($status) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Perbarui Status Produksi</h5>
                        <p class="text-muted mb-3">
                            Status saat ini: <strong>{{ Status::label($order->production_status) }}</strong>.
                            Status diperbarui secara berurutan.
                        </p>
                        @if ($nextStatus)
                            <form method="POST" action="{{ route('order.status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="production_status" value="{{ $nextStatus }}" />
                                <button type="submit" class="btn btn-primary w-100">Lanjutkan ke {{ Status::label($nextStatus) }}</button>
                            </form>
                        @else
                            <div class="alert alert-success mb-0">Pesanan sudah selesai diproduksi.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-lg-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Desain & Catatan</h5>
                        <p class="mb-2"><strong>Desain:</strong> {{ $order->design_notes ?: '-' }}</p>
                        <p class="mb-0"><strong>Catatan:</strong> {{ $order->notes ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Ringkasan Keuangan</h5>
                        @if ($isPemilik)
                            <p class="mb-2"><strong>Pendapatan:</strong> {{ Status::rupiah($order->total_price) }}</p>
                            <p class="mb-2"><strong>Total Biaya:</strong> {{ Status::rupiah($totalCosts) }}</p>
                            <p class="mb-2"><strong>Laba:</strong> <span class="{{ $profit < 0 ? 'text-danger' : 'text-success' }}">{{ Status::rupiah($profit) }}</span></p>
                        @endif
                        <p class="mb-0"><strong>Belum Ditagihkan:</strong> {{ Status::rupiah($billableBalance) }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Informasi Pelanggan</h5>
                        <div class="text-center">
                            <iconify-icon icon="iconamoon:profile-circle-duotone" class="h1 text-primary"></iconify-icon>
                            <h5 class="mb-1">{{ $order->customer?->name ?? '-' }}</h5>
                            <p class="mb-1">{{ $order->customer?->email ?? '-' }}</p>
                            <p class="mb-1">{{ $order->customer?->phone ?? '-' }}</p>
                            <p class="mb-0">{{ $order->customer?->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Pengadaan Bahan</h5>
                        <a href="{{ route('procurement.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-primary">Tambah Pengadaan</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th>Bahan</th>
                                    <th>Pemasok</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Tgl Pesan / Datang</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->procurements as $procurement)
                                    <tr>
                                        <td>{{ $procurement->material_name }}</td>
                                        <td>{{ $procurement->supplier?->store_name ?? '-' }}</td>
                                        <td>{{ rtrim(rtrim(number_format((float) $procurement->quantity, 2, ',', '.'), '0'), ',') }} {{ $procurement->unit }}</td>
                                        <td>{{ Status::rupiah($procurement->total_cost) }}</td>
                                        <td>{{ $procurement->ordered_at?->format('d M') }} / {{ $procurement->received_at?->format('d M Y') ?? ($procurement->expected_at ? 'est. ' . $procurement->expected_at->format('d M Y') : '-') }}</td>
                                        <td><span class="badge badge-soft-{{ Status::badge($procurement->status) }}">{{ Status::label($procurement->status) }}</span></td>
                                        <td class="text-end">
                                            @if ($procurement->status === \App\Models\Procurement::STATUS_DIPESAN)
                                                <form method="POST" action="{{ route('procurement.receive', $procurement) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-soft-success">Bahan Diterima</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('procurement.edit', $procurement) }}" class="btn btn-sm btn-soft-primary">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada pengadaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="{{ $isPemilik ? 'col-lg-6' : 'col-12' }}">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Invoice & Pembayaran</h5>
                        @if ($billableBalance > 0)
                            <a href="{{ route('invoice.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-primary">Buat Invoice</a>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Jenis</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ Status::label($invoice->type) }}</td>
                                        <td>{{ Status::rupiah($invoice->amount) }}</td>
                                        <td><span class="badge badge-soft-{{ Status::badge($invoice->status) }}">{{ Status::label($invoice->status) }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('invoice-detail', $invoice) }}" class="btn btn-sm btn-soft-secondary">Lihat</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada invoice.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($isPemilik)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Pengeluaran</h5>
                            <a href="{{ route('expense.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-primary">Tambah Pengeluaran</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-centered mb-0">
                                <thead class="bg-light bg-opacity-50">
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($order->expenses as $expense)
                                        <tr>
                                            <td>{{ Status::label($expense->category) }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td>{{ Status::rupiah($expense->amount) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">Belum ada pengeluaran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .order-hero-card {
        min-height: 250px;
    }

    .order-summary-card-body {
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
    }

    .order-summary-table td {
        padding-top: 0.85rem;
        padding-bottom: 0.85rem;
        vertical-align: middle;
    }

    .progressbar {
        counter-reset: step;
        display: flex;
        justify-content: space-between;
        gap: 0;
        list-style: none;
    }

    .progressbar li {
        position: relative;
        flex: 1;
        text-align: center;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: .02em;
        padding: 0 8px;
    }

    .progressbar li span {
        display: block;
        margin-top: 16px;
        font-size: 13px;
        text-transform: none;
        letter-spacing: normal;
    }

    .progressbar li::before {
        counter-increment: step;
        content: counter(step);
        width: 58px;
        height: 58px;
        line-height: 54px;
        border: 4px solid #dee2e6;
        display: block;
        text-align: center;
        margin: 0 auto;
        border-radius: 50%;
        background-color: #fff;
        font-size: 26px;
        font-weight: 700;
        color: #64748b;
        position: relative;
        z-index: 2;
    }

    .progressbar li::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 4px;
        background-color: #dee2e6;
        top: 29px;
        left: -50%;
        z-index: 1;
    }

    .progressbar li:first-child::after {
        content: none;
    }

    .progressbar li.active {
        color: #2c7be5;
    }

    .progressbar li.active::before {
        border-color: #2c7be5;
        background-color: #2c7be5;
        color: #fff;
    }

    .progressbar li.active::after {
        background-color: #2c7be5;
    }

    .progressbar-five li {
        flex: 0 0 20%;
        max-width: 20%;
    }

    .progressbar-compact {
        width: 100%;
        margin-top: 8px;
    }

    .progressbar-compact li {
        font-size: 12px;
    }

    .progressbar-compact li::before {
        width: 56px;
        height: 56px;
        line-height: 52px;
        font-size: 24px;
    }

    .progressbar-compact li::after {
        top: 28px;
    }

    .progressbar-compact li span {
        margin-top: 12px;
        font-size: 13px;
    }

    .progress-card-body {
        padding-top: 1.5rem;
        padding-bottom: 1.25rem;
        min-height: 230px;
        display: flex;
        flex-direction: column;
    }

    .progress-card-center {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: stretch;
        padding-top: 0.5rem;
        width: 100%;
    }

    @media (max-width: 991.98px) {
        .order-hero-card {
            min-height: auto;
        }

        .progress-card-body {
            min-height: auto;
        }

        .progress-card-center {
            display: block;
            padding-top: 1rem;
        }

        .progressbar {
            gap: 12px;
            flex-direction: column;
        }

        .progressbar li {
            text-align: left;
            padding-left: 80px;
            max-width: 100%;
        }

        .progressbar li span {
            margin-top: 6px;
        }

        .progressbar li::before {
            position: absolute;
            left: 0;
            top: 0;
            margin: 0;
        }

        .progressbar li::after {
            width: 4px;
            height: calc(100% + 12px);
            top: 58px;
            left: 27px;
        }

        .progressbar li:first-child::after {
            content: '';
        }

        .progressbar li:last-child::after {
            display: none;
        }

        .progressbar-compact li::before {
            width: 44px;
            height: 44px;
            line-height: 40px;
            font-size: 20px;
        }
    }
</style>
@endpush
