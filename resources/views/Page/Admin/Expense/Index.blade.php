@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Expenses</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Expenses</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <div style="min-width: 220px; flex: 0 1 250px;">
                        <div class="search-bar">
                            <span><i class="bx bx-search-alt"></i></span>
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari deskripsi / pesanan..." />
                        </div>
                    </div>

                    <div style="min-width: 180px; flex: 0 0 180px;">
                        <input type="month" name="month" class="form-control" value="{{ request('month') }}" aria-label="Bulan" />
                    </div>

                    <div style="min-width: 160px; flex: 0 0 170px;">
                        <select name="category" class="form-select" aria-label="Kategori">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-filter-alt me-1"></i>Filter
                    </button>
                    <a href="{{ route('expense') }}" class="btn btn-outline-secondary">Reset</a>
                    <div class="ms-lg-auto">
                        <a href="{{ route('expense.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Tambah Pengeluaran
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Pesanan</th>
                                    <th>Nominal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->spent_at?->format('d M Y') ?? '-' }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $expense->category)) }}</td>
                                        <td>
                                            {{ $expense->description }}
                                            @if ($expense->procurement_id)
                                                <div><small class="text-muted">Terkunci dari modul pengadaan</small></div>
                                            @endif
                                        </td>
                                        <td>{{ $expense->order?->order_number ?? '-' }}</td>
                                        <td>Rp {{ number_format((float) $expense->amount, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            @if (!$expense->procurement_id)
                                                <a href="{{ route('expense.edit', $expense) }}" class="btn btn-sm btn-soft-primary me-1"><i class="bx bx-edit"></i></a>
                                                <form method="POST" action="{{ route('expense.destroy', $expense) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Hapus pengeluaran ini?')"><i class="bx bx-trash"></i></button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Kelola via procurement</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada pengeluaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body border-top">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0">Terhubung ke Pesanan</h5></div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse ($linkedTotals as $category => $amount)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $category)) }}</td>
                                        <td class="text-end">Rp {{ number_format((float) $amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-3">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Umum / Non Pesanan</h5></div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse ($generalTotals as $category => $amount)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $category)) }}</td>
                                        <td class="text-end">Rp {{ number_format((float) $amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-3">Belum ada data.</td></tr>
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
