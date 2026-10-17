@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Edit Order</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('order') }}">Orders</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('order.update', $order) }}" class="row g-3" id="order-form">
            @csrf
            @method('PUT')

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Detail Pesanan</h5></div>
                    <div class="card-body row g-3">
                        <div class="col-12">
                            <label class="form-label">Customer</label>
                            <div class="position-relative">
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $order->customer_id) }}" />
                                <input type="text" class="form-control" id="customer_search" value="{{ old('name', $order->customer?->name) }}" placeholder="Ketik nama customer..." autocomplete="off" />
                                <div id="customer_suggestions" class="dropdown-menu w-100" style="max-height: 260px; overflow: auto;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Produk</label>
                            <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $order->product_name) }}" required />
                            @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bahan</label>
                            <input type="text" name="material" class="form-control @error('material') is-invalid @enderror" value="{{ old('material', $order->material) }}" required />
                            @error('material')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Qty</label>
                            <input type="number" min="1" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $order->quantity) }}" required />
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Harga Satuan</label>
                            <input type="number" min="0" step="0.01" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $order->unit_price) }}" required />
                            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Uang Muka</label>
                            <input type="number" min="0" step="0.01" name="down_payment" id="down_payment" class="form-control @error('down_payment') is-invalid @enderror" value="{{ old('down_payment', $order->down_payment) }}" />
                            @error('down_payment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Jadi</label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $order->due_date?->toDateString()) }}" />
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Produksi</label>
                            <select name="production_status" class="form-select @error('production_status') is-invalid @enderror">
                                @foreach ($productionStatuses as $status)
                                    <option value="{{ $status }}" @selected(old('production_status', $order->production_status) === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                            @error('production_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipe Pembayaran</label>
                            <select name="payment_type" class="form-select @error('payment_type') is-invalid @enderror">
                                @foreach ($paymentTypes as $type)
                                    <option value="{{ $type }}" @selected(old('payment_type', $order->payment_type) === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            @error('payment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Pembayaran</label>
                            <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                @foreach ($paymentStatuses as $status)
                                    <option value="{{ $status }}" @selected(old('payment_status', $order->payment_status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Rincian Ukuran</label>
                            <textarea name="size_details" class="form-control @error('size_details') is-invalid @enderror" rows="3">{{ old('size_details', $order->size_details) }}</textarea>
                            @error('size_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Data Customer</h5></div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $order->customer?->name) }}" required />
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instansi</label>
                            <input type="text" name="institute" id="institute" class="form-control @error('institute') is-invalid @enderror" value="{{ old('institute', $order->customer?->institute) }}" required />
                            @error('institute')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $order->customer?->email) }}" required />
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $order->customer?->phone) }}" required />
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $order->customer?->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Ringkasan Harga</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2"><span>Total</span><strong id="total_price_preview">Rp 0</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Uang Muka</span><strong id="down_payment_preview">Rp 0</strong></div>
                        <div class="d-flex justify-content-between border-top pt-2"><span>Sisa Tagihan</span><strong id="remaining_balance_preview">Rp 0</strong></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('order-detail', $order) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const customers = @json($customersForJs)
        const customerIdInput = document.getElementById('customer_id')
        const customerSearchInput = document.getElementById('customer_search')
        const suggestions = document.getElementById('customer_suggestions')
        const quantityInput = document.getElementById('quantity')
        const unitPriceInput = document.getElementById('unit_price')
        const downPaymentInput = document.getElementById('down_payment')
        if (!customerIdInput || !customerSearchInput || !suggestions) return

        const fields = {
            name: document.getElementById('name'),
            email: document.getElementById('email'),
            phone: document.getElementById('phone'),
            address: document.getElementById('address'),
            institute: document.getElementById('institute'),
        }

        function setCustomer(customer) {
            customerIdInput.value = customer?.id ? String(customer.id) : ''
            customerSearchInput.value = customer?.name || ''
            Object.keys(fields).forEach((key) => {
                if (fields[key]) fields[key].value = customer?.[key] || ''
            })
            suggestions.classList.remove('show')
        }

        function renderSuggestions() {
            const q = customerSearchInput.value.trim().toLowerCase()
            suggestions.innerHTML = ''
            if (!q) return

            customers.filter((customer) => customer.name.toLowerCase().includes(q)).slice(0, 7).forEach((customer) => {
                const button = document.createElement('button')
                button.type = 'button'
                button.className = 'dropdown-item'
                button.textContent = `${customer.name} - ${customer.email}`
                button.addEventListener('click', () => setCustomer(customer))
                suggestions.appendChild(button)
            })

            if (suggestions.childElementCount > 0) {
                suggestions.classList.add('show')
            } else {
                suggestions.classList.remove('show')
            }
        }

        function formatRupiah(value) {
            return `Rp ${Number(value || 0).toLocaleString('id-ID')}`
        }

        function renderTotals() {
            const quantity = Number(quantityInput?.value || 0)
            const price = Number(unitPriceInput?.value || 0)
            const downPayment = Number(downPaymentInput?.value || 0)
            const total = quantity * price
            const remaining = Math.max(0, total - downPayment)
            document.getElementById('total_price_preview').textContent = formatRupiah(total)
            document.getElementById('down_payment_preview').textContent = formatRupiah(downPayment)
            document.getElementById('remaining_balance_preview').textContent = formatRupiah(remaining)
        }

        customerSearchInput.addEventListener('input', renderSuggestions)
        customerSearchInput.addEventListener('focus', renderSuggestions)
        document.addEventListener('click', (event) => {
            if (event.target === customerSearchInput || suggestions.contains(event.target)) return
            suggestions.classList.remove('show')
        })

        ;[quantityInput, unitPriceInput, downPaymentInput].forEach((input) => input?.addEventListener('input', renderTotals))
        renderTotals()
    })()
</script>
@endpush
@endsection
