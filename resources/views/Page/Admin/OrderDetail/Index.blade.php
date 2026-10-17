@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Order Detail</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('order') }}">Orders</a></li>
                        <li class="breadcrumb-item active">{{ $order->order_number ?? 'Order' }}</li>
                    </ol>
                </div>
            </div>
        </div>

        @php
            $steps = [
                'pesanan_diterima' => 'Pesanan diterima',
                'menunggu_bahan' => 'Menunggu bahan',
                'proses_produksi' => 'Proses produksi',
                'quality_control' => 'Quality control',
                'selesai' => 'Selesai',
            ];
            $statusKeys = array_keys($steps);
            $currentStep = array_search($order->production_status, $statusKeys, true);
            $currentStep = $currentStep === false ? 0 : $currentStep;
        @endphp

        <div class="row g-3 align-items-stretch">
            <div class="col-xl-8">
                <div class="card h-100 order-hero-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge badge-soft-primary">{{ $order->order_number ?? '-' }}</span>
                                    <span class="badge badge-soft-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst((string) $order->payment_status) }}
                                    </span>
                                </div>
                                <h4 class="mb-1">{{ $order->product_name }}</h4>
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
                                        <th>Bahan</th>
                                        <th>Qty</th>
                                        <th>Harga Satuan</th>
                                        <th>Total</th>
                                        <th>Tanggal Jadi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $order->product_name }}</td>
                                        <td>{{ $order->material }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>Rp {{ number_format((float) $order->unit_price, 0, ',', '.') }}</td>
                                        <td class="fw-semibold">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                        <td>{{ $order->due_date?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card h-100 order-hero-card">
                    <div class="card-body order-summary-card-body">
                        <h5 class="card-title mb-3">Order Summary</h5>
                        <div class="table-responsive text-nowrap table-centered">
                            <table class="table table-sm mb-0 order-summary-table">
                                <tbody>
                                    <tr>
                                        <td>Customer</td>
                                        <td class="text-end">{{ $order->customer?->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td class="text-end">{{ $order->customer?->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td class="text-end">{{ $order->customer?->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status Produksi</td>
                                        <td class="text-end">{{ ucwords(str_replace('_', ' ', (string) $order->production_status)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status Pembayaran</td>
                                        <td class="text-end">{{ ucfirst((string) $order->payment_status) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Created</td>
                                        <td class="text-end">{{ $order->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                    </tr>
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
                                @foreach ($steps as $status => $label)
                                    <li class="{{ array_search($status, $statusKeys, true) <= $currentStep ? 'active' : '' }}">
                                        <span>{{ $label }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <form method="POST" action="{{ route('order.status', $order) }}" class="card h-100">
                    @csrf
                    @method('PATCH')
                    <div class="card-body">
                        <h5 class="card-title mb-3">Update Status</h5>
                        <div class="mb-3">
                            <label class="form-label">Status Produksi</label>
                            <select name="production_status" class="form-select">
                                @foreach ($productionStatuses as $status)
                                    <option value="{{ $status }}" @selected($order->production_status === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Pembayaran</label>
                            <select name="payment_status" class="form-select">
                                @foreach ($paymentStatuses as $status)
                                    <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Status</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-lg-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Detail Pesanan</h5>
                        <p class="mb-2"><strong>Bahan:</strong> {{ $order->material }}</p>
                        <p class="mb-2"><strong>Qty:</strong> {{ $order->quantity }}</p>
                        <p class="mb-2"><strong>Rincian Ukuran:</strong> {{ $order->size_details ?: '-' }}</p>
                        <p class="mb-2"><strong>Uang Muka:</strong> Rp {{ number_format((float) $order->down_payment, 0, ',', '.') }}</p>
                        <p class="mb-0"><strong>Sisa Tagihan:</strong> Rp {{ number_format((float) $order->remaining_balance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Billing Information</h5>
                        <p class="mb-2"><strong>Payment Type:</strong> {{ ucfirst((string) $order->payment_type) }}</p>
                        <p class="mb-2"><strong>Status Pembayaran:</strong> {{ ucfirst((string) $order->payment_status) }}</p>
                        <p class="mb-2"><strong>Pendapatan:</strong> Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                        <p class="mb-2"><strong>Total Biaya:</strong> Rp {{ number_format($totalCosts, 0, ',', '.') }}</p>
                        <p class="mb-0"><strong>Laba:</strong> Rp {{ number_format($profit, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Customer Information</h5>
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
                                    <th>Supplier</th>
                                    <th>Qty</th>
                                    <th>Total</th>
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
                                        <td>Rp {{ number_format((float) $procurement->total_cost, 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($procurement->status) }}</td>
                                        <td class="text-end">
                                            @if ($procurement->status !== 'diterima')
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
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada pengadaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
                                        <td>{{ ucwords(str_replace('_', ' ', $expense->category)) }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>Rp {{ number_format((float) $expense->amount, 0, ',', '.') }}</td>
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

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Invoice</h5>
                        <a href="{{ route('invoice.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-primary">Buat Invoice</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ ucfirst((string) $invoice->payment_status) }}</td>
                                        <td>Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('invoice-detail', $invoice) }}" class="btn btn-sm btn-soft-secondary">Lihat</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada invoice.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
