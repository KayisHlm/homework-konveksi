@extends('Layout.Admin._Layout')
@section('Content')
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="mb-0 fw-semibold">Edit Customer</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('customer') }}">Customers</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('customer.update', $customer) }}" class="row g-3">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="name"
                                        value="{{ old('name', $customer->name) }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        value="{{ old('email', $customer->email) }}"
                                        required
                                    />
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Institute</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="institute"
                                        value="{{ old('institute', $customer->institute) }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="phone"
                                        value="{{ old('phone', $customer->phone) }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="address"
                                        value="{{ old('address', $customer->address) }}"
                                        required
                                    />
                                </div>

                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('customer') }}" class="btn btn-outline-secondary">Back</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Update Customer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Info</h5>
                        </div>
                        <div class="card-body text-muted">
                            Email harus tetap unik, tapi boleh sama dengan email customer ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center">
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                        &copy; Reback.
                    </div>
                </div>
            </div>
        </footer>
    </div>
@endsection

