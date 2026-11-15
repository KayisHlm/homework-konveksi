@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Edit Pengadaan</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('procurement') }}">Procurements</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('procurement.update', $procurement) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pesanan</label>
                            <select name="order_id" class="form-select @error('order_id') is-invalid @enderror" required>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}" @selected(old('order_id', $procurement->order_id) == $order->id)>{{ $order->order_number }} - {{ $order->customer?->name }} ({{ $order->productSummary() }})</option>
                                @endforeach
                            </select>
                            @error('order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $procurement->supplier_id) == $supplier->id)>{{ $supplier->store_name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Bahan</label>
                            <input type="text" name="material_name" class="form-control @error('material_name') is-invalid @enderror" value="{{ old('material_name', $procurement->material_name) }}" required />
                            @error('material_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" min="0" step="0.01" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $procurement->quantity) }}" required />
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $procurement->unit) }}" required />
                            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Satuan</label>
                            <input type="number" min="0" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $procurement->unit_price) }}" required />
                            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Pesan</label>
                            <input type="date" name="ordered_at" class="form-control @error('ordered_at') is-invalid @enderror" value="{{ old('ordered_at', $procurement->ordered_at?->toDateString()) }}" required />
                            @error('ordered_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estimasi Datang</label>
                            <input type="date" name="expected_at" class="form-control @error('expected_at') is-invalid @enderror" value="{{ old('expected_at', $procurement->expected_at?->toDateString()) }}" />
                            @error('expected_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-control-plaintext">
                                <span class="badge badge-soft-{{ Status::badge($procurement->status) }}">{{ Status::label($procurement->status) }}</span>
                                <small class="text-muted ms-1">diubah lewat tombol Diterima / Batalkan</small>
                            </div>
                        </div>
                        @if ($procurement->status === \App\Models\Procurement::STATUS_DITERIMA)
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Diterima</label>
                                <input type="date" name="received_at" class="form-control @error('received_at') is-invalid @enderror" value="{{ old('received_at', $procurement->received_at?->toDateString()) }}" />
                                @error('received_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $procurement->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex justify-content-end align-items-start gap-2">
                <a href="{{ route('procurement') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
