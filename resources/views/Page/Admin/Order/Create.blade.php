@extends('Layout.Admin._Layout')
@section('Content')
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="mb-0 fw-semibold">Create Order</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">Order</a>
                            </li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('order.store') }}" class="row g-3" id="order-form">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label">Customer</label>
                                    <div class="position-relative">
                                        <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}" />
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="customer_search"
                                            placeholder="Ketik nama customer..."
                                            autocomplete="off"
                                        />
                                        <div
                                            id="customer_suggestions"
                                            class="dropdown-menu w-100"
                                            style="max-height: 260px; overflow: auto;"
                                        ></div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Product Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="product_name"
                                        value="{{ old('product_name') }}"
                                        placeholder="e.g. Konveksi Seragam"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Payment Type</label>
                                    <select class="form-select" name="payment_type" required>
                                        @foreach ($paymentTypes as $type)
                                            <option value="{{ $type }}" @selected(old('payment_type', 'cash') === $type)>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Payment Status</label>
                                    <select class="form-select" name="payment_status" required>
                                        @foreach ($paymentStatuses as $status)
                                            <option value="{{ $status }}" @selected(old('payment_status', 'pending') === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <hr class="my-1" />
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Institute</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="institute"
                                        id="institute"
                                        value="{{ old('institute') }}"
                                        placeholder="e.g. PT / CV / Sekolah"
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="name"
                                        id="name"
                                        value="{{ old('name') }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        id="email"
                                        value="{{ old('email') }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="phone"
                                        id="phone"
                                        value="{{ old('phone') }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="address"
                                        id="address"
                                        value="{{ old('address') }}"
                                        required
                                    />
                                </div>

                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('order') }}" class="btn btn-outline-secondary">Back</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Save Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tips</h5>
                        </div>
                        <div class="card-body text-muted">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="bx bx-info-circle fs-20"></i>
                                <div>
                                    Pilih customer untuk mengisi otomatis name/email/phone/address, lalu tetap bisa diubah.
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="bx bx-list-check fs-20"></i>
                                <div>Payment type & status mengikuti enum pada tabel orders.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            (function () {
                const customers = @json($customersForJs)

                const customerIdInput = document.getElementById('customer_id')
                const customerSearchInput = document.getElementById('customer_search')
                const suggestions = document.getElementById('customer_suggestions')
                if (!customerSearchInput || !suggestions || !customerIdInput) return

                const nameInput = document.getElementById('name')
                const emailInput = document.getElementById('email')
                const phoneInput = document.getElementById('phone')
                const addressInput = document.getElementById('address')
                const instituteInput = document.getElementById('institute')

                function openSuggestions() {
                    suggestions.classList.add('show')
                }

                function closeSuggestions() {
                    suggestions.classList.remove('show')
                }

                function setCustomer(customer) {
                    customerIdInput.value = customer?.id ? String(customer.id) : ''
                    customerSearchInput.value = customer?.name || customerSearchInput.value

                    if (nameInput) nameInput.value = customer?.name || nameInput.value
                    if (emailInput) emailInput.value = customer?.email || emailInput.value
                    if (phoneInput) phoneInput.value = customer?.phone || phoneInput.value
                    if (addressInput) addressInput.value = customer?.address || addressInput.value
                    if (instituteInput) instituteInput.value = customer?.institute || instituteInput.value
                }

                function renderSuggestions(list) {
                    suggestions.innerHTML = ''
                    if (!list.length) {
                        closeSuggestions()
                        return
                    }

                    list.forEach((customer) => {
                        const button = document.createElement('button')
                        button.type = 'button'
                        button.className = 'dropdown-item'
                        button.textContent = `${customer.name} — ${customer.email}`
                        button.addEventListener('click', () => {
                            setCustomer(customer)
                            closeSuggestions()
                        })
                        suggestions.appendChild(button)
                    })
                    openSuggestions()
                }

                function syncInputValueToSelection() {
                    const inputValue = customerSearchInput.value.trim().toLowerCase()
                    const selectedId = customerIdInput.value ? Number(customerIdInput.value) : null
                    if (selectedId) {
                        const selected = customers.find((c) => c.id === selectedId)
                        if (!selected || selected.name.toLowerCase() !== inputValue) {
                            customerIdInput.value = ''
                        }
                    }
                }

                function onSearch() {
                    syncInputValueToSelection()

                    const q = customerSearchInput.value.trim().toLowerCase()
                    if (!q) {
                        closeSuggestions()
                        return
                    }

                    if (nameInput && !nameInput.value) {
                        nameInput.value = customerSearchInput.value
                    }

                    const matches = customers
                        .filter((c) => c.name.toLowerCase().includes(q))
                        .slice(0, 7)

                    renderSuggestions(matches)
                }

                customerSearchInput.addEventListener('input', onSearch)
                customerSearchInput.addEventListener('focus', onSearch)

                document.addEventListener('click', (event) => {
                    if (event.target === customerSearchInput || suggestions.contains(event.target)) return
                    closeSuggestions()
                })

                if (customerIdInput.value) {
                    const selectedId = Number(customerIdInput.value)
                    const selected = customers.find((c) => c.id === selectedId)
                    if (selected) {
                        customerSearchInput.value = selected.name
                        setCustomer(selected)
                    }
                }

                const form = document.getElementById('order-form')
                if (form) {
                    form.addEventListener('submit', () => {
                        if (customerIdInput.value) return

                        const q = customerSearchInput.value.trim().toLowerCase()
                        if (!q) return

                        const exact = customers.find((c) => c.name.toLowerCase() === q)
                        if (!exact) return

                        setCustomer(exact)
                    })
                }
            })()
        </script>
    @endpush
@endsection
