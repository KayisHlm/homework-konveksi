@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Procurements</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Procurements</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="d-flex flex-wrap justify-content-between gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="search-bar">
                            <span><i class="bx bx-search-alt"></i></span>
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari bahan, pesanan, supplier..." />
                        </div>
                        <select name="status" class="form-select" style="width: 180px;">
                            <option value="">Semua status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <select name="supplier_id" class="form-select" style="width: 220px;">
                            <option value="">Semua supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((string) request('supplier_id') === (string) $supplier->id)>{{ $supplier->store_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('procurement') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>

                    <a href="{{ route('procurement.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i>Tambah Pengadaan</a>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th>Tanggal Pesan</th>
                                    <th>Pesanan</th>
                                    <th>Supplier</th>
                                    <th>Bahan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 220px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($procurements as $procurement)
                                    <tr>
                                        <td>{{ $procurement->ordered_at?->format('d M Y') ?? '-' }}</td>
                                        <td>{{ $procurement->order?->order_number ?? '-' }}</td>
                                        <td>{{ $procurement->supplier?->store_name ?? '-' }}</td>
                                        <td>{{ $procurement->material_name }}</td>
                                        <td>Rp {{ number_format((float) $procurement->total_cost, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $procurement->status === 'diterima' ? 'badge-soft-success' : ($procurement->status === 'dibatalkan' ? 'badge-soft-danger' : 'badge-soft-warning') }}">
                                                {{ ucfirst($procurement->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center gap-2 justify-content-center flex-nowrap">
                                                @if ($procurement->status !== 'diterima')
                                                    <form method="POST" action="{{ route('procurement.receive', $procurement) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-soft-success">Diterima</button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('procurement.edit', $procurement) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('procurement.destroy', $procurement) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Hapus pengadaan ini?')" title="Hapus">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada data pengadaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body border-top">
                        {{ $procurements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
