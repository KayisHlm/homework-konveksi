{{-- Satu produk dalam pesanan beserta grid jumlah per ukuran.
     Variabel: $index (int|string '__INDEX__' untuk template), $item (array), $sizes (list ukuran baku) --}}
<div class="border rounded-3 mb-3 order-item" data-index="{{ $index }}">
    <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light bg-opacity-50 border-bottom rounded-top-3">
        <span class="fw-semibold">Produk <span class="item-number">{{ is_int($index) ? $index + 1 : '' }}</span></span>
        <button type="button" class="btn btn-sm btn-soft-danger remove-item">Hapus produk</button>
    </div>

    <div class="p-3">
        @php($prefix = "items.$index")
        @if (is_int($index) && $errors->has("$prefix.*"))
            <div class="alert alert-danger py-2 small">{{ collect($errors->get("$prefix.*"))->flatten()->first() }}</div>
        @endif

        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <label class="form-label small mb-1">Nama produk</label>
                <input type="text" name="items[{{ $index }}][product_name]" class="form-control form-control-sm" value="{{ $item['product_name'] ?? '' }}" placeholder="Kaos PDH" required />
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Bahan</label>
                <input type="text" name="items[{{ $index }}][material]" class="form-control form-control-sm" value="{{ $item['material'] ?? '' }}" placeholder="Cotton Combed 24s" required />
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Warna</label>
                <input type="text" name="items[{{ $index }}][color]" class="form-control form-control-sm" value="{{ $item['color'] ?? '' }}" placeholder="Hitam" />
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Harga dasar / pcs</label>
                <input type="number" min="0" step="1" name="items[{{ $index }}][unit_price]" class="form-control form-control-sm item-price" value="{{ (float) ($item['unit_price'] ?? 0) }}" required />
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center align-middle mb-2 size-grid">
                <thead class="bg-light bg-opacity-50">
                    <tr>
                        <th class="text-start" style="width: 110px;">Ukuran</th>
                        @foreach ($sizes as $size)
                            <th style="min-width: 72px;">{{ $size }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="text-start fw-normal">Qty</th>
                        @foreach ($sizes as $size)
                            <td class="p-1">
                                <input type="number" min="0" name="items[{{ $index }}][sizes][{{ $size }}][quantity]" class="form-control form-control-sm text-center size-qty" value="{{ $item['sizes'][$size]['quantity'] ?? '' }}" aria-label="Qty {{ $size }}" />
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th class="text-start fw-normal">+ Harga</th>
                        @foreach ($sizes as $size)
                            <td class="p-1">
                                <input type="number" min="0" step="1" name="items[{{ $index }}][sizes][{{ $size }}][extra_price]" class="form-control form-control-sm text-center size-extra" value="{{ ((float) ($item['sizes'][$size]['extra_price'] ?? 0)) ?: '' }}" placeholder="0" aria-label="Tambahan harga {{ $size }}" />
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-4 small">
            <span>Total qty: <strong class="item-qty-total">0</strong> pcs</span>
            <span>Subtotal: <strong class="item-subtotal">Rp 0</strong></span>
        </div>
    </div>
</div>
