@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Create Invoice</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('invoice') }}">Invoices</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('invoice.store') }}" class="row g-3" id="invoice-form">
            @csrf

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Informasi Invoice</h5></div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pesanan</label>
                            <select name="order_id" id="order_id" class="form-select @error('order_id') is-invalid @enderror">
                                <option value="">Tanpa pesanan</option>
                                @foreach ($orders as $order)
                                    <option
                                        value="{{ $order->id }}"
                                        data-customer-id="{{ $order->customer_id }}"
                                        data-product="{{ $order->product_name }}"
                                        data-quantity="{{ $order->quantity }}"
                                        data-price="{{ $order->unit_price }}"
                                        @selected(old('order_id', $invoice->order_id) == $order->id)
                                    >
                                        {{ $order->order_number ?? 'Order #' . $order->id }} - {{ $order->product_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                <option value="">Pilih customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id', $invoice->customer_id) == $customer->id)>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Terbit</label>
                            <input type="date" name="issued_at" class="form-control @error('issued_at') is-invalid @enderror" value="{{ old('issued_at', $invoice->issued_at?->toDateString()) }}" required />
                            @error('issued_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $invoice->due_date) }}" />
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status Pembayaran</label>
                            <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                @foreach ($invoiceStatuses as $status)
                                    <option value="{{ $status }}" @selected(old('payment_status', $invoice->payment_status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Metode Pembayaran</label>
                            <input type="text" name="payment_via" class="form-control @error('payment_via') is-invalid @enderror" value="{{ old('payment_via', $invoice->payment_via) }}" placeholder="Transfer BCA / Cash / QRIS" />
                            @error('payment_via')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Diskon</label>
                            <input type="number" min="0" step="0.01" name="discount_amount" id="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', $invoice->discount_amount ?? 0) }}" />
                            @error('discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Item Invoice</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="add-item">Tambah Item</button>
                    </div>
                    <div class="card-body">
                        <div id="invoice-items">
                            @php($oldItems = old('items', $invoice->items ?? [['product_name' => '', 'quantity' => 1, 'unit_price' => 0]]))
                            @foreach ($oldItems as $index => $item)
                                <div class="row g-2 align-items-end invoice-item mb-2">
                                    <div class="col-md-5">
                                        <label class="form-label">Produk</label>
                                        <input type="text" name="items[{{ $index }}][product_name]" class="form-control" value="{{ $item['product_name'] ?? '' }}" required />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Qty</label>
                                        <input type="number" min="1" name="items[{{ $index }}][quantity]" class="form-control item-qty" value="{{ $item['quantity'] ?? 1 }}" required />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Harga</label>
                                        <input type="number" min="0" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control item-price" value="{{ $item['unit_price'] ?? 0 }}" required />
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-item">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Ringkasan</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong id="subtotal-preview">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Diskon</span>
                            <strong id="discount-preview">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span>Total</span>
                            <strong id="total-preview">Rp 0</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('invoice') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Invoice</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const container = document.getElementById('invoice-items')
        const addButton = document.getElementById('add-item')
        const discountInput = document.getElementById('discount_amount')
        const orderInput = document.getElementById('order_id')
        const customerInput = document.getElementById('customer_id')
        if (!container || !addButton || !discountInput) return

        function formatRupiah(value) {
            return `Rp ${Number(value || 0).toLocaleString('id-ID')}`
        }

        function bindItem(row) {
            row.querySelectorAll('.item-qty, .item-price').forEach((input) => {
                input.addEventListener('input', renderTotals)
            })

            const removeButton = row.querySelector('.remove-item')
            if (removeButton) {
                removeButton.addEventListener('click', () => {
                    if (container.querySelectorAll('.invoice-item').length === 1) return
                    row.remove()
                    reindex()
                    renderTotals()
                })
            }
        }

        function reindex() {
            container.querySelectorAll('.invoice-item').forEach((row, index) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`)
                })
            })
        }

        function renderTotals() {
            let subtotal = 0
            container.querySelectorAll('.invoice-item').forEach((row) => {
                const qty = Number(row.querySelector('.item-qty')?.value || 0)
                const price = Number(row.querySelector('.item-price')?.value || 0)
                subtotal += qty * price
            })

            const discount = Number(discountInput.value || 0)
            const total = Math.max(0, subtotal - discount)

            document.getElementById('subtotal-preview').textContent = formatRupiah(subtotal)
            document.getElementById('discount-preview').textContent = formatRupiah(discount)
            document.getElementById('total-preview').textContent = formatRupiah(total)
        }

        function newRow(index, values = {}) {
            const wrapper = document.createElement('div')
            wrapper.className = 'row g-2 align-items-end invoice-item mb-2'
            wrapper.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label">Produk</label>
                    <input type="text" name="items[${index}][product_name]" class="form-control" value="${values.product_name || ''}" required />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Qty</label>
                    <input type="number" min="1" name="items[${index}][quantity]" class="form-control item-qty" value="${values.quantity || 1}" required />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Harga</label>
                    <input type="number" min="0" step="0.01" name="items[${index}][unit_price]" class="form-control item-price" value="${values.unit_price || 0}" required />
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100 remove-item">Hapus</button>
                </div>
            `

            bindItem(wrapper)
            return wrapper
        }

        addButton.addEventListener('click', () => {
            container.appendChild(newRow(container.querySelectorAll('.invoice-item').length))
        })

        discountInput.addEventListener('input', renderTotals)
        container.querySelectorAll('.invoice-item').forEach(bindItem)

        if (orderInput && customerInput) {
            orderInput.addEventListener('change', () => {
                const selected = orderInput.options[orderInput.selectedIndex]
                const customerId = selected?.dataset.customerId
                if (customerId) {
                    customerInput.value = customerId
                }

                const product = selected?.dataset.product
                const quantity = selected?.dataset.quantity
                const price = selected?.dataset.price
                if (product && container.querySelectorAll('.invoice-item').length === 1) {
                    const first = container.querySelector('.invoice-item')
                    first.querySelector('input[name$="[product_name]"]').value = product
                    first.querySelector('.item-qty').value = quantity || 1
                    first.querySelector('.item-price').value = price || 0
                    renderTotals()
                }
            })
        }

        renderTotals()
    })()
</script>
@endpush
@endsection
