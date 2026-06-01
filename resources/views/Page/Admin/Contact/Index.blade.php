@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Contacts</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Apps</a>
                                    </li>
                                    <li class="breadcrumb-item active">Contacts</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6 col-md-4">
                            <form method="GET" class="search-bar">
                                <span><i class="bx bx-search-alt"></i></span>
                                <input
                                    type="search"
                                    class="form-control"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Search contact..."
                                />
                            </form>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 gx-3">
                        @forelse ($contacts as $contact)
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div
                                                class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border d-inline-flex align-items-center justify-content-center bg-light"
                                            >
                                                <iconify-icon
                                                    icon="iconamoon:profile-circle-duotone"
                                                    class="fs-36 text-muted"
                                                ></iconify-icon>
                                            </div>
                                            <h4 class="mt-3 fs-18">
                                                <span class="text-dark">{{ $contact->name }}</span>
                                            </h4>
                                            @if ($contact->city || $contact->address)
                                                <div class="mb-1 d-inline-block text-muted">
                                                    <i class="bx bx-location-plus text-danger fs-14 me-1"></i>
                                                    {{ trim(($contact->address ? $contact->address . ', ' : '') . ($contact->city ?? '')) }}
                                                </div>
                                                <br />
                                            @endif
                                            @if ($contact->phone)
                                                <a href="tel:{{ $contact->phone }}" class="d-inline-block">
                                                    <i class="bx bx-phone-call text-success fs-14 me-1"></i>
                                                    {{ $contact->phone }}
                                                </a>
                                                <br />
                                            @endif
                                            @if ($contact->email)
                                                <a href="mailto:{{ $contact->email }}" class="d-inline-block">
                                                    <i class="bx bxs-envelope text-warning fs-14 me-1"></i>
                                                    {{ $contact->email }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col">
                                <div class="alert alert-secondary mb-0">No contacts found.</div>
                            </div>
                        @endforelse
                    </div>

                    @if (false)
                    <div
                        class="row row-cols-1 row-cols-md-2 row-cols-xl-4 gx-3"
                    >
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-1.jpg"
                                            alt="avatar-1"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Anna M. Hines</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Burr Ridge/Illinois</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >555-1564-261</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-2.jpg"
                                            alt="avatar-2"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Candice F. Gilmore</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Roselle/Illinois</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >755-5532-588</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-3.jpg"
                                            alt="avatar-3"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Vanessa R. Davis</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Wann/Oklahoma</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >441-5558-183</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-4.jpg"
                                            alt="avatar-4"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Judith H. Fritsche</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >SULLIVAN/Kentucky</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >305-5579-759</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-5.jpg"
                                            alt="avatar-5"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Peter T. Smith</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Yreka/California</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >655-5187-93</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-6.jpg"
                                            alt="avatar-6"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Emmanuel J. Delcid</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Atlanta/Georgia</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >693-5553-637</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-7.jpg"
                                            alt="avatar-7"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >William J. Cook</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Rosenberg/Texas</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >855-5446-150</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-8.jpg"
                                            alt="avatar-8"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Martin R. Peters</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Youngstown/Ohio</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >455-5943-13</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-9.jpg"
                                            alt="avatar-9"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Paul M. Schubert</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Austin/Texas</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >035-5531-64</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img
                                            src="assets/images/users/avatar-10.jpg"
                                            alt="avatar-10"
                                            class="img-fluid avatar-xl img-thumbnail rounded-circle avatar-border"
                                        />
                                        <h4 class="mt-3 fs-18">
                                            <a
                                                href="javascript:void(0);"
                                                class="text-dark"
                                                >Janet J. Champine</a
                                            >
                                        </h4>
                                        <a
                                            href="javascript:void(0);"
                                            class="mb-1 d-inline-block"
                                            ><i
                                                class="bx bx-location-plus text-danger fs-14 me-1"
                                            ></i
                                            >Nashville/Tennessee</a
                                        >
                                        <br />
                                        <a href="javascript:void(0);"
                                            ><i
                                                class="bx bx-phone-call text-success fs-14 me-1"
                                            ></i
                                            >115-5592-916</a
                                        >
                                        <br />
                                        <a
                                            href="pages-profile.html"
                                            class="btn btn-sm btn-outline-primary mt-3"
                                            >View Profile</a
                                        >
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                    @endif
                </div>
                <!-- End Container -->
            </div>
@endsection
