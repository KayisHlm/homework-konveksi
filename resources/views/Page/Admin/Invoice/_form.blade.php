{{-- Form invoice bersama untuk Create & Edit. Variabel: $invoice, $orders, $ordersForJs, $types, $statuses, $paymentMethods --}}
@php
    $isEdit = $invoice->exists;
@endphp

<form method="POST" action="{{ $isEdit ? route('invoice.update', $invoice) : route('invoice.store') }}" class="row g-3" id="invoice-form">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Informasi Tagihan</h5></div>
            <div class="card-body row g-3">
                <div class="col-md-8">
                    <label class="form-label">Pesanan</label>
                    <select name="order_id" id="order_id" class="form-select @error('order_id') is-invalid @enderror" required>
                        <option value="">Pilih pesanan</option>
                        @foreach ($orders as $order)
                            <option value="{{ $order->id }}" @selected(old('order_id', $invoice->order_id) == $order->id)>
                                {{ $order->order_number }} - {{ $order->customer?->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Tagihan</label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected(old('type', $invoice->type) === $type)>{{ Status::label($type) }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nominal Tagihan</label>
                    <input type="number" min="1" step="1" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $invoice->amount ? (float) $invoice->amount : null) }}" required />
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <button type="button" class="btn btn-link btn-sm px-0" id="fill-remaining">Isi dengan sisa yang belum ditagih</button>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Terbit</label>
                    <input type="date" name="issued_at" class="form-control @error('issued_at') is-invalid @enderror" value="{{ old('issued_at', $invoice->issued_at?->toDateString()) }}" required />
                    @error('issued_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jatuh Tempo</label>
                    <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $invoice->due_date?->toDateString()) }}" />
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $invoice->status) === $status)>{{ Status::label($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 paid-field">
                    <label class="form-label">Tanggal Bayar</label>
                    <input type="date" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror" value="{{ old('paid_at', $invoice->paid_at?->toDateString()) }}" />
                    @error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 paid-field">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                        <option value="">-</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method }}" @selected(old('payment_method', $invoice->payment_method) === $method)>{{ Status::label($method) }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $invoice->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Ringkasan Pesanan</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Total Pesanan</span><strong id="order_total">Rp 0</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Sudah Dibayar</span><strong id="order_paid">Rp 0</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Sudah Ditagihkan</span><strong id="order_invoiced">Rp 0</strong></div>
                <div class="d-flex justify-content-between border-top pt-2"><span>Sisa Belum Ditagih</span><strong id="order_billable">Rp 0</strong></div>
                <p class="text-muted small mt-2 mb-0">Item invoice diambil otomatis dari item pesanan.</p>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ $isEdit ? route('invoice-detail', $invoice) : route('invoice') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Invoice</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    (function () {
        const orders = @json($ordersForJs);
        // Saat edit, nominal invoice ini tidak dihitung sebagai "sudah ditagihkan".
        const ownAmount = {{ $isEdit && $invoice->status !== \App\Models\Invoice::STATUS_DIBATALKAN ? (float) $invoice->amount : 0 }}
        const ownOrderId = {{ $isEdit ? (int) $invoice->order_id : 'null' }}
        const orderSelect = document.getElementById('order_id')
        const statusSelect = document.getElementById('status')
        const rupiah = (value) => `Rp ${Number(value || 0).toLocaleString('id-ID')}`

        function billable() {
            const order = orders[orderSelect.value]
            if (!order) return 0
            const invoiced = order.invoiced - (Number(orderSelect.value) === ownOrderId ? ownAmount : 0)
            return Math.max(0, order.total - invoiced)
        }

        function renderSummary() {
            const order = orders[orderSelect.value] || { total: 0, paid: 0, invoiced: 0 }
            document.getElementById('order_total').textContent = rupiah(order.total)
            document.getElementById('order_paid').textContent = rupiah(order.paid)
            document.getElementById('order_invoiced').textContent = rupiah(order.total - billable())
            document.getElementById('order_billable').textContent = rupiah(billable())
        }

        function togglePaidFields() {
            const isPaid = statusSelect.value === @json(\App\Models\Invoice::STATUS_LUNAS);
            document.querySelectorAll('.paid-field').forEach((el) => el.classList.toggle('d-none', !isPaid))
        }

        document.getElementById('fill-remaining').addEventListener('click', () => {
            document.getElementById('amount').value = billable()
        })
        orderSelect.addEventListener('change', renderSummary)
        statusSelect.addEventListener('change', togglePaidFields)
        renderSummary()
        togglePaidFields()
    })()
</script>
@endpush
