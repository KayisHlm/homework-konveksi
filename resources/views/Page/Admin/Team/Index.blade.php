@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Our Team</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Pages</a>
                                    </li>
                                    <li class="breadcrumb-item active">Our Team</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row g-2">
                                        <!--end col-->
                                        <div class="col-sm-auto ms-auto">
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('team.create') }}" class="btn btn-primary">
                                                    <i class="bx bx-plus me-1 align-middle"></i>
                                                    Add Members
                                                </a>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @forelse ($teamMembers as $member)
                                    <div class="col-xxl-4 col-md-6">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div
                                                    class="avatar-lg rounded-circle border border-light border-3 bg-light d-inline-flex align-items-center justify-content-center mb-3"
                                                >
                                                    <iconify-icon
                                                        icon="iconamoon:profile-circle-duotone"
                                                        class="fs-36 text-muted"
                                                    ></iconify-icon>
                                                </div>

                                                <div class="text-dark fw-medium fs-18">{{ $member['name'] }}</div>
                                                <div class="my-2">
                                                    <span class="badge bg-light text-dark px-2 py-1 fs-14">
                                                        {{ $member['role'] }}
                                                    </span>
                                                </div>

                                                <div class="d-flex justify-content-center gap-2">                                    
                                                    <a
                                                        href="https://wa.me/{{ $member['whatsapp'] }}"
                                                        target="_blank"
                                                        class="btn btn-soft-success avatar-sm fs-20 d-inline-flex align-items-center justify-content-center p-0"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom"
                                                        data-bs-custom-class="success-tooltip"
                                                        title="WhatsApp"
                                                    >
                                                        <i class="bx bxl-whatsapp"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-secondary mb-0">No team members found.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Container -->
            </div>
@endsection
