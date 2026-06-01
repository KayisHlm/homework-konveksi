@extends('Layout.Admin._Layout')
@section('Content')
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="mb-0 fw-semibold">Add Member</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('team') }}">Team</a>
                            </li>
                            <li class="breadcrumb-item active">Add Member</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Member Information</h5>
                        </div>
                        <div class="card-body">
                            <form class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="e.g. Budi Santoso" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" placeholder="e.g. Sales" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" placeholder="e.g. 62812xxxxxxx" />
                                    <div class="form-text">Format disarankan: 62xxxxxxxxxxx</div>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('team') }}" class="btn btn-outline-secondary">Back</a>
                                    <button type="button" class="btn btn-primary" disabled>Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Note</h5>
                        </div>
                        <div class="card-body text-muted">
                            Simpan data member ke database belum diaktifkan. Halaman ini dibuat untuk melengkapi navigasi dari tombol Add Members.
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

