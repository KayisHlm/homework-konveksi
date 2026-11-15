@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content invoice-detail-page">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Detail Invoice</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('invoice') }}">Invoice</a></li>
                        <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card invoice-sheet">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-start g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <img
                                src="{{ asset('build/assets/images/homework-konveksi.png') }}"
                                alt="Homework Konveksi"
                                class="rounded-circle border"
                                style="width: 72px; height: 72px; object-fit: cover;"
                            />
                            <div>
                                <div class="fw-bold fs-4">Homework Konveksi</div>
                                <div class="text-muted">Invoice Penagihan Pesanan Konveksi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge badge-soft-{{ Status::badge($invoice->status) }} fs-13 px-3 py-2">
                            {{ strtoupper(Status::label($invoice->status)) }}
                        </span>
                        <h3 class="fw-bold mt-3 mb-1">{{ $invoice->invoice_number }}</h3>
                        <div class="text-muted">Jenis: {{ Status::label($invoice->type) }}</div>
                        <div class="text-muted">Terbit: {{ $invoice->issued_at?->format('d M Y') ?? '-' }}</div>
                        <div class="text-muted">Jatuh tempo: {{ $invoice->due_date?->format('d M Y') ?? '-' }}</div>
                        @if ($invoice->paid_at)
                            <div class="text-muted">Dibayar: {{ $invoice->paid_at->format('d M Y') }} ({{ Status::label($invoice->payment_method) }})</div>
                        @endif
                    </div>
                </div>

                <div class="row mt-4 g-3">
                    <div class="col-lg-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-uppercase text-muted fs-12 mb-2">Ditagihkan Kepada</div>
                            <div class="fw-semibold fs-18">{{ $order->customer?->name ?? '-' }}</div>
                            <div class="text-muted">{{ $order->customer?->institute ?? '-' }}</div>
                            <div class="text-muted">{{ $order->customer?->address ?? '-' }}</div>
                            <div class="text-muted">Telepon: {{ $order->customer?->phone ?? '-' }}</div>
                            <div class="text-muted">Email: {{ $order->customer?->email ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="border rounded-3 p-3 h-100 bg-light bg-opacity-50">
                            <div class="text-uppercase text-muted fs-12 mb-2">Ringkasan Pesanan</div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>No. Pesanan</span>
                                <span class="fw-medium">
                                    <a href="{{ route('order-detail', $order) }}">{{ $order->order_number }}</a>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Item</span>
                                <span class="fw-medium">{{ $order->items->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Qty</span>
                                <span class="fw-medium">{{ number_format($order->totalQuantity(), 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Catatan</span>
                                <span class="fw-medium text-end ms-3">{{ $invoice->notes ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive text-nowrap mt-4 table-centered">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="border-0 py-2">Produk</th>
                                <th class="border-0 py-2">Bahan / Warna</th>
                                <th class="border-0 py-2">Rincian Ukuran</th>
                                <th class="border-0 py-2 text-end">Qty</th>
                                <th class="border-0 py-2 text-end">Harga Dasar</th>
                                <th class="border-0 py-2 text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $item->product_name }}</div>
                                    </td>
                                    <td>{{ $item->material }}{{ $item->color ? ' / ' . $item->color : '' }}</td>
                                    <td class="text-wrap small">{{ $item->sizeSummary() }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ Status::rupiah($item->unit_price) }}</td>
                                    <td class="text-end fw-semibold">{{ Status::rupiah($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end mt-4">
                    <div class="col-lg-5">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted">Subtotal</td>
                                        <td class="text-end fw-medium">{{ Status::rupiah($subtotal) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Diskon</td>
                                        <td class="text-end fw-medium">- {{ Status::rupiah($order->discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Total Pesanan</td>
                                        <td class="text-end fw-medium">{{ Status::rupiah($order->total_price) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pembayaran Sebelumnya</td>
                                        <td class="text-end fw-medium">- {{ Status::rupiah($paidBefore) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted border-top pt-2"><strong>Tagihan Ini ({{ Status::label($invoice->type) }})</strong></td>
                                        <td class="text-end fw-bold fs-4 border-top pt-2">{{ Status::rupiah($invoice->amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sisa Setelah Tagihan Ini</td>
                                        <td class="text-end fw-medium">{{ Status::rupiah($remainingAfter) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-5 mb-1 d-print-none">
                    <div class="text-end d-flex justify-content-end gap-2">
                        <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-outline-primary">Edit Invoice</a>
                        @if ($invoice->status !== \App\Models\Invoice::STATUS_LUNAS)
                            <form method="POST" action="{{ route('invoice.destroy', $invoice) }}" onsubmit="return confirm('Hapus invoice ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Hapus</button>
                            </form>
                        @endif
                        <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    @media print {
        .topbar,
        .main-nav,
        .leftbar-tab-menu,
        .leftbar-menu,
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
        .invoice-detail-page {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .invoice-sheet {
            border: 0 !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
