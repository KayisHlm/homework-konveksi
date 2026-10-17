@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Edit Pengeluaran</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('expense') }}">Expenses</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('expense.update', $expense) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pesanan</label>
                            <select name="order_id" class="form-select @error('order_id') is-invalid @enderror">
                                <option value="">Tanpa pesanan</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}" @selected(old('order_id', $expense->order_id) == $order->id)>{{ $order->order_number ?? 'Order #' . $order->id }}</option>
                                @endforeach
                            </select>
                            @error('order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category', $expense->category) === $category)>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                                @endforeach
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $expense->description) }}" required />
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nominal</label>
                            <input type="number" min="0" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $expense->amount) }}" required />
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="spent_at" class="form-control @error('spent_at') is-invalid @enderror" value="{{ old('spent_at', $expense->spent_at?->toDateString()) }}" required />
                            @error('spent_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex justify-content-end align-items-start gap-2">
                <a href="{{ route('expense') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
