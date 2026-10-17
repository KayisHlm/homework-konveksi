@extends('Layout.Admin._Layout')

@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Orders</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="d-flex flex-wrap gap-2 justify-content-between">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="search-bar">
                            <span><i class="bx bx-search-alt"></i></span>
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nomor pesanan, produk, customer..." />
                        </div>
                        <select name="production_status" class="form-select" style="width: 220px;">
                            <option value="">Semua status produksi</option>
                            @foreach ($productionStatuses as $status)
                                <option value="{{ $status }}" @selected(request('production_status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                        <select name="payment_status" class="form-select" style="width: 180px;">
                            <option value="">Semua pembayaran</option>
                            @foreach (['pending', 'paid', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('order') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>

                    <a href="{{ route('order.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Create Order
                    </a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-centered text-nowrap mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Pelanggan</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Tanggal Jadi</th>
                            <th>Status Produksi</th>
                            <th>Pembayaran</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td><a href="{{ route('order-detail', $order) }}">{{ $order->order_number ?? '-' }}</a></td>
                                <td>{{ $order->created_at?->format('d M Y') }}</td>
                                <td>{{ $order->product_name }}</td>
                                <td>{{ $order->customer?->name ?? '-' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                <td>{{ $order->due_date?->format('d M Y') ?? '-' }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', (string) $order->production_status)) }}</td>
                                <td>{{ ucfirst((string) $order->payment_status) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('order-detail', $order) }}" class="btn btn-sm btn-soft-secondary me-1"><i class="bx bx-show"></i></a>
                                    <a href="{{ route('order.edit', $order) }}" class="btn btn-sm btn-soft-primary me-1"><i class="bx bx-edit"></i></a>
                                    <form method="POST" action="{{ route('order.destroy', $order) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Hapus pesanan ini?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Belum ada pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
