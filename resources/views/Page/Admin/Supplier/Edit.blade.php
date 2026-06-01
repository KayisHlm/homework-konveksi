@extends('Layout.Admin._Layout')
@section('Content')
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="mb-0 fw-semibold">Edit Supplier</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('supplier') }}">Suppliers</a>
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
                            <h5 class="card-title mb-0">Supplier Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('supplier.update', $supplier) }}" class="row g-3">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <label class="form-label">PIC Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="pic_name"
                                        value="{{ old('pic_name', $supplier->pic_name) }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Store Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="store_name"
                                        value="{{ old('store_name', $supplier->store_name) }}"
                                        required
                                    />
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Product Category</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="product_category"
                                        value="{{ old('product_category', $supplier->product_category) }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="phone"
                                        value="{{ old('phone', $supplier->phone) }}"
                                        required
                                    />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="address"
                                        value="{{ old('address', $supplier->address) }}"
                                        required
                                    />
                                </div>

                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('supplier') }}" class="btn btn-outline-secondary">Back</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Update Supplier
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
                        <div class="card-body text-muted">Perubahan supplier akan mempengaruhi referensi data di modul lain jika nanti dihubungkan.</div>
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

