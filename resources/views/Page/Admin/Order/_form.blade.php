{{-- Form pesanan bersama untuk Create & Edit. Variabel: $order (nullable), $customersForJs, $sizes, $paymentMethods --}}
@php
    $order = $order ?? null;
    $isEdit = $order !== null;
    // Bentuk data item: [product_name, material, color, unit_price, sizes => [UKURAN => [quantity, extra_price]]]
    $items = old('items', $isEdit
        ? $order->items->map(fn ($item) => [
            ...$item->only(['product_name', 'material', 'color', 'unit_price']),
            'sizes' => $item->sizes->mapWithKeys(fn ($size) => [$size->size => $size->only(['quantity', 'extra_price'])])->all(),
        ])->all()
        : [[]]);
    $customer = $isEdit ? $order->customer : null;
@endphp

<form method="POST" action="{{ $isEdit ? route('order.update', $order) : route('order.store') }}" class="row g-3" id="order-form">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Data Pelanggan</h5></div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label">Cari Pelanggan Lama</label>
                    <div class="position-relative">
                        <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $customer?->id) }}" />
                        <input type="text" class="form-control" id="customer_search" value="{{ old('name', $customer?->name) }}" placeholder="Ketik nama pelanggan, atau isi data baru di bawah..." autocomplete="off" />
                        <div id="customer_suggestions" class="dropdown-menu w-100" style="max-height: 260px; overflow: auto;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer?->name) }}" required />
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="institute" id="institute" class="form-control @error('institute') is-invalid @enderror" value="{{ old('institute', $customer?->institute) }}" required />
                    @error('institute')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer?->email) }}" required />
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer?->phone) }}" required />
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $customer?->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Item Pesanan</h5>
                <span class="text-muted small" id="items-count"></span>
            </div>
            <div class="card-body">
                @error('items')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
                <p class="text-muted small mb-3">Satu produk per kartu. Isi jumlah pada ukuran yang dipesan; kolom "+ Harga" untuk tambahan harga per pcs ukuran tertentu (mis. XXL).</p>

                <div id="items-list">
                    @foreach (array_values($items) as $i => $item)
                        @include('Page.Admin.Order._item', ['index' => $i, 'item' => $item])
                    @endforeach
                </div>

                <button type="button" class="btn btn-outline-primary w-100" id="add-item">+ Tambah Produk</button>
                <template id="item-template">
                    @include('Page.Admin.Order._item', ['index' => '__INDEX__', 'item' => []])
                </template>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Detail Pesanan</h5></div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jadi</label>
                    <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $order?->due_date?->toDateString()) }}" />
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Diskon</label>
                    <input type="number" min="0" step="1" name="discount_amount" id="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', (float) ($order?->discount_amount ?? 0)) }}" />
                    @error('discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Desain</label>
                    <textarea name="design_notes" class="form-control @error('design_notes') is-invalid @enderror" rows="2" placeholder="Posisi sablon/bordir, warna logo, tautan file desain...">{{ old('design_notes', $order?->design_notes) }}</textarea>
                    @error('design_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $order?->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Ringkasan Harga</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong id="subtotal_preview">Rp 0</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Diskon</span><strong id="discount_preview">Rp 0</strong></div>
                <div class="d-flex justify-content-between border-top pt-2 mb-3"><span>Total Pesanan</span><strong id="total_preview">Rp 0</strong></div>

                @if ($isEdit)
                    <p class="text-muted small mb-0">Pembayaran (uang muka, termin, pelunasan) dicatat melalui menu Invoice.</p>
                @else
                    <label class="form-label">Uang Muka</label>
                    <input type="number" min="0" step="1" name="down_payment" id="down_payment" class="form-control @error('down_payment') is-invalid @enderror" value="{{ old('down_payment', 0) }}" />
                    @error('down_payment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <label class="form-label mt-2">Metode Uang Muka</label>
                    <select name="down_payment_method" class="form-select @error('down_payment_method') is-invalid @enderror">
                        <option value="">-</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method }}" @selected(old('down_payment_method') === $method)>{{ Status::label($method) }}</option>
                        @endforeach
                    </select>
                    @error('down_payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="d-flex justify-content-between border-top pt-2 mt-3"><span>Sisa Tagihan</span><strong id="remaining_preview">Rp 0</strong></div>
                    <p class="text-muted small mt-2 mb-0">Uang muka otomatis tercatat sebagai invoice uang muka yang lunas.</p>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ $isEdit ? route('order-detail', $order) : route('order') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    (function () {
        const customers = @json($customersForJs);
        const customerIdInput = document.getElementById('customer_id')
        const customerSearchInput = document.getElementById('customer_search')
        const suggestions = document.getElementById('customer_suggestions')
        const fields = ['name', 'email', 'phone', 'address', 'institute'].reduce((acc, key) => ({ ...acc, [key]: document.getElementById(key) }), {})

        function setCustomer(customer) {
            customerIdInput.value = customer?.id ? String(customer.id) : ''
            customerSearchInput.value = customer?.name || ''
            Object.keys(fields).forEach((key) => { fields[key].value = customer?.[key] || '' })
            suggestions.classList.remove('show')
        }

        function renderSuggestions() {
            const q = customerSearchInput.value.trim().toLowerCase()
            suggestions.innerHTML = ''
            if (!q) return customerIdInput.value = ''

            customers.filter((c) => c.name.toLowerCase().includes(q)).slice(0, 7).forEach((customer) => {
                const button = document.createElement('button')
                button.type = 'button'
                button.className = 'dropdown-item'
                button.textContent = `${customer.name} - ${customer.email}`
                button.addEventListener('click', () => setCustomer(customer))
                suggestions.appendChild(button)
            })
            suggestions.classList.toggle('show', suggestions.childElementCount > 0)
        }

        customerSearchInput.addEventListener('input', renderSuggestions)
        customerSearchInput.addEventListener('focus', renderSuggestions)
        document.addEventListener('click', (event) => {
            if (event.target !== customerSearchInput && !suggestions.contains(event.target)) suggestions.classList.remove('show')
        })

        // Repeater produk: setiap kartu berisi grid ukuran; nama input memakai indeks urut items[n].
        const list = document.getElementById('items-list')
        const template = document.getElementById('item-template')
        const rupiah = (value) => `Rp ${Number(value || 0).toLocaleString('id-ID')}`
        const valueOf = (id) => Number(document.getElementById(id)?.value || 0)
        const cards = () => list.querySelectorAll('.order-item')

        function reindex() {
            cards().forEach((card, index) => {
                card.dataset.index = index
                card.querySelector('.item-number').textContent = index + 1
                card.querySelectorAll('[name]').forEach((input) => {
                    input.name = input.name.replace(/^items\[[^\]]+\]/, `items[${index}]`)
                })
                card.querySelector('.remove-item').disabled = cards().length === 1
            })
            document.getElementById('items-count').textContent = `${cards().length} produk`
        }

        function renderTotals() {
            let subtotal = 0
            cards().forEach((card) => {
                const basePrice = Number(card.querySelector('.item-price').value || 0)
                let quantity = 0
                let itemTotal = 0
                card.querySelectorAll('.size-qty').forEach((qtyInput, column) => {
                    const qty = Number(qtyInput.value || 0)
                    const extra = Number(card.querySelectorAll('.size-extra')[column].value || 0)
                    quantity += qty
                    itemTotal += qty * (basePrice + extra)
                })
                card.querySelector('.item-qty-total').textContent = quantity.toLocaleString('id-ID')
                card.querySelector('.item-subtotal').textContent = rupiah(itemTotal)
                subtotal += itemTotal
            })
            const discount = valueOf('discount_amount')
            const total = Math.max(0, subtotal - discount)
            document.getElementById('subtotal_preview').textContent = rupiah(subtotal)
            document.getElementById('discount_preview').textContent = rupiah(discount)
            document.getElementById('total_preview').textContent = rupiah(total)
            const remaining = document.getElementById('remaining_preview')
            if (remaining) remaining.textContent = rupiah(Math.max(0, total - valueOf('down_payment')))
        }

        document.getElementById('add-item').addEventListener('click', () => {
            list.appendChild(template.content.cloneNode(true))
            reindex()
            renderTotals()
            cards()[cards().length - 1].querySelector('input').focus()
        })

        list.addEventListener('click', (event) => {
            if (!event.target.classList.contains('remove-item') || cards().length === 1) return
            event.target.closest('.order-item').remove()
            reindex()
            renderTotals()
        })

        reindex()
        document.getElementById('order-form').addEventListener('input', renderTotals)
        renderTotals()
    })()
</script>
@endpush
