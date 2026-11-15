@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Invoice</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="GET" class="d-flex flex-wrap justify-content-between gap-3">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="search-bar">
                            <span><i class="bx bx-search-alt"></i></span>
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari invoice, pesanan, pelanggan..." />
                        </div>
                        <select name="status" class="form-select" style="width: 180px;">
                            <option value="">Semua status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ Status::label($status) }}</option>
                            @endforeach
                        </select>
                        <select name="type" class="form-select" style="width: 180px;">
                            <option value="">Semua jenis</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" @selected(request('type') === $type)>{{ Status::label($type) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('invoice') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>

                    <a href="{{ route('invoice.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Buat Invoice
                    </a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive table-centered">
                <table class="table text-nowrap mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th>No. Invoice</th>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Jatuh Tempo</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoice-detail', $invoice) }}" class="fw-medium">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td><a href="{{ route('order-detail', $invoice->order_id) }}">{{ $invoice->order->order_number }}</a></td>
                                <td>{{ $invoice->order->customer?->name ?? '-' }}</td>
                                <td>{{ Status::label($invoice->type) }}</td>
                                <td>{{ $invoice->issued_at?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $invoice->due_date?->format('d M Y') ?? '-' }}</td>
                                <td class="fw-semibold">{{ Status::rupiah($invoice->amount) }}</td>
                                <td><span class="badge badge-soft-{{ Status::badge($invoice->status) }}">{{ Status::label($invoice->status) }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('invoice-detail', $invoice) }}" class="btn btn-sm btn-soft-secondary me-1">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-sm btn-soft-primary me-1">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('invoice.destroy', $invoice) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Hapus invoice ini?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada invoice.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
