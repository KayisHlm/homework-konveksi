@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Sales</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">Dashboards</li>
                                    <li class="breadcrumb-item active">Sales</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


                    <div class="row">
                        <div class="col-xl col-lg-4 col-md-6">
                            <div class="card">
                                <div
                                    class="card-body overflow-hidden position-relative"
                                >
                                    <iconify-icon
                                        icon="iconamoon:shopping-card-add-duotone"
                                        class="fs-36 text-info"
                                    ></iconify-icon>
                                    <h3 class="mb-0 fw-bold mt-3 mb-1">
                                        Rp {{ number_format((float) $totalSalesPaid, 0, ',', '.') }}
                                    </h3>
                                    <p class="text-muted">Total Penjualan</p>
                                    <span class="badge fs-12 badge-soft-success"
                                        ><i class="ti ti-arrow-badge-up"></i>
                                        8.72%</span
                                    >
                                    <i
                                        class="bx bx-doughnut-chart widget-icon"
                                    ></i>
                                </div>
                                <!-- end card-body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xl col-lg-4 col-md-6">
                            <div class="card">
                                <div
                                    class="card-body overflow-hidden position-relative"
                                >
                                    <iconify-icon
                                        icon="iconamoon:link-external-duotone"
                                        class="fs-36 text-success"
                                    ></iconify-icon>
                                    <h3 class="mb-0 fw-bold mt-3 mb-1">
                                        Rp {{ number_format(0, 0, ',', '.') }}
                                    </h3>
                                    <p class="text-muted">Total Pengeluaran</p>
                                    <span class="badge fs-12 badge-soft-danger"
                                        ><i class="ti ti-arrow-badge-down"></i>
                                        3.28%</span
                                    >
                                    <i
                                        class="bx bx-bar-chart-alt-2 widget-icon"
                                    ></i>
                                </div>
                                <!-- end card-body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xl col-lg-4 col-md-6">
                            <div class="card">
                                <div
                                    class="card-body overflow-hidden position-relative"
                                >
                                    <iconify-icon
                                        icon="iconamoon:store-duotone"
                                        class="fs-36 text-purple"
                                    ></iconify-icon>
                                    <h3 class="mb-0 fw-bold mt-3 mb-1">
                                        {{ $totalCustomers }}
                                    </h3>
                                    <p class="text-muted">Total Pelanggan</p>
                                    <span class="badge fs-12 badge-soft-danger"
                                        ><i class="ti ti-arrow-badge-down"></i>
                                        5.69%</span
                                    >
                                    <i
                                        class="bx bx-building-house widget-icon"
                                    ></i>
                                </div>
                                <!-- end card-body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xl col-lg-6 col-md-6">
                            <div class="card">
                                <div
                                    class="card-body overflow-hidden position-relative"
                                >
                                    <iconify-icon
                                        icon="iconamoon:gift-duotone"
                                        class="fs-36 text-orange"
                                    ></iconify-icon>
                                    <h3 class="mb-0 fw-bold mt-3 mb-1">
                                        Rp {{ number_format((float) $totalSalesPaid, 0, ',', '.') }}
                                    </h3>
                                    <p class="text-muted">Profit</p>
                                    <span class="badge fs-12 badge-soft-success"
                                        ><i class="ti ti-arrow-badge-up"></i>
                                        10.58%</span
                                    >
                                    <i class="bx bx-bowl-hot widget-icon"></i>
                                </div>
                                <!-- end card-body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xl col-lg-6 col-md-12">
                            <div class="card">
                                <div
                                    class="card-body overflow-hidden position-relative"
                                >
                                    <iconify-icon
                                        icon="iconamoon:certificate-badge-duotone"
                                        class="fs-36 text-warning"
                                    ></iconify-icon>
                                    <h3 class="mb-0 fw-bold mt-3 mb-1">
                                        Rp {{ number_format((float) $totalOutstanding, 0, ',', '.') }}
                                    </h3>
                                    <p class="text-muted">Belum Lunas</p>
                                    <span class="badge fs-12 badge-soft-success"
                                        ><i class="ti ti-arrow-badge-up"></i>
                                        8.72%</span
                                    >
                                    <i
                                        class="bx bx-cricket-ball widget-icon"
                                    ></i>
                                </div>
                                <!-- end card-body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row-->

                    <div class="row">
                        <div class="col-xxl-8">
                            <div class="card">
                                <div
                                    class="card-header d-flex justify-content-between align-items-center"
                                >
                                    <h4 class="card-title">Overview</h4>
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-soft-secondary"
                                        >
                                            ALL
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-soft-secondary"
                                        >
                                            1M
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-soft-secondary"
                                        >
                                            6M
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-soft-secondary active"
                                        >
                                            1Y
                                        </button>
                                    </div>
                                </div>
                                <!-- end card-title-->

                                <div class="card-body">
                                    <div dir="ltr">
                                        <div
                                            id="dash-overview-chart"
                                            class="apex-charts"
                                        ></div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xxl-4">
                            <div class="card">
                                <div
                                    class="card-header d-flex justify-content-between align-items-center"
                                >
                                    <h4 class="card-title">
                                        Penjualan Berdasarkan Bahan
                                    </h4>
                                    <div class="dropdown">
                                        <a
                                            href="#"
                                            class="dropdown-toggle arrow-none card-drop"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                        >
                                            <iconify-icon
                                                icon="iconamoon:menu-kebab-vertical-circle-duotone"
                                                class="fs-20 align-middle text-muted"
                                            ></iconify-icon>
                                        </a>
                                        <div
                                            class="dropdown-menu dropdown-menu-end"
                                        >
                                            <!-- item-->
                                            <a
                                                href="javascript:void(0);"
                                                class="dropdown-item"
                                                >Sales Report</a
                                            >
                                            <!-- item-->
                                            <a
                                                href="javascript:void(0);"
                                                class="dropdown-item"
                                                >Export Report</a
                                            >
                                            <!-- item-->
                                            <a
                                                href="javascript:void(0);"
                                                class="dropdown-item"
                                                >Profit</a
                                            >
                                            <!-- item-->
                                            <a
                                                href="javascript:void(0);"
                                                class="dropdown-item"
                                                >Action</a
                                            >
                                        </div>
                                    </div>
                                </div>
                                <!-- end card-header-->
                                <div class="card-body">
                                    <div dir="ltr">
                                        <div
                                            id="sales-category-chart"
                                            class="apex-charts"
                                        ></div>
                                    </div>
                                    <div class="table-responsive mb-n1 mt-2">
                                        <table
                                            class="table table-nowrap table-borderless table-sm table-centered mb-0"
                                        >
                                            <thead
                                                class="bg-light bg-opacity-50 thead-sm"
                                            >
                                                <tr>
                                                    <th class="py-1">
                                                        Corduroy
                                                    </th>
                                                    <th class="py-1">Orders</th>
                                                    <th class="py-1">Perc.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Work Jacket</td>
                                                    <td>187,232</td>
                                                    <td>
                                                        48.63%
                                                        <span
                                                            class="badge badge-soft-success ms-1"
                                                            >2.5% Up</span
                                                        >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Coach Jacket</td>
                                                    <td>126,874</td>
                                                    <td>
                                                        36.08%
                                                        <span
                                                            class="badge badge-soft-success ms-1"
                                                            >8.5% Up</span
                                                        >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Other</td>
                                                    <td>90,127</td>
                                                    <td>
                                                        23.41%
                                                        <span
                                                            class="badge badge-soft-danger ms-1"
                                                            >10.98% Down</span
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- end table-responsive-->
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card-->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div
                                        class="d-flex align-items-center justify-content-between"
                                    >
                                        <h4 class="card-title">
                                            Recent Orders
                                        </h4>

                                        <a
                                            href="{{ route('order.create') }}"
                                            class="btn btn-sm btn-primary"
                                        >
                                            <i class="bx bx-plus me-1"></i
                                            >Create Order
                                        </a>
                                    </div>
                                </div>
                                <!-- end card body -->
                                <div class="table-responsive table-centered">
                                    <table class="table mb-0">
                                        <thead class="bg-light bg-opacity-50">
                                            <tr>
                                                <th class="border-0 py-2">
                                                    Order ID.
                                                </th>
                                                <th class="border-0 py-2">
                                                    Date
                                                </th>
                                                <th class="border-0 py-2">
                                                    Product
                                                </th>
                                                <th class="border-0 py-2">
                                                    Customer Name
                                                </th>
                                                <th class="border-0 py-2">
                                                    Email ID
                                                </th>
                                                <th class="border-0 py-2">
                                                    Phone No.
                                                </th>
                                                <th class="border-0 py-2">
                                                    Address
                                                </th>
                                                <th class="border-0 py-2">
                                                    Payment Type
                                                </th>
                                                <th class="border-0 py-2">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <!-- end thead-->
                                        <tbody>
                                            @forelse ($recentOrders as $order)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('order-detail', $order) }}">
                                                            #ORD{{ $order->id }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $order->created_at?->format('d M, Y') }}</td>
                                                    <td>
                                                        <div
                                                            class="avatar-sm bg-light rounded d-inline-flex align-items-center justify-content-center"
                                                        >
                                                            <iconify-icon
                                                                icon="iconamoon:shopping-bag-duotone"
                                                                class="fs-20 text-muted"
                                                            ></iconify-icon>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="#!">{{ $order->customer?->name ?? '-' }}</a>
                                                    </td>
                                                    <td>{{ $order->customer?->email ?? '-' }}</td>
                                                    <td>{{ $order->customer?->phone ?? '-' }}</td>
                                                    <td>{{ $order->customer?->address ?? '-' }}</td>
                                                    <td>{{ $order->payment_type }}</td>
                                                    <td>
                                                        <i
                                                            class="bx bxs-circle me-1 {{ $order->payment_status == 'paid' ? 'text-success' : ($order->payment_status == 'pending' ? 'text-primary' : 'text-danger') }}"
                                                        ></i>
                                                        {{ $order->payment_status }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        No orders found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <!-- end tbody -->
                                    </table>
                                    <!-- end table -->
                                </div>
                                <!-- table responsive -->
                                <div
                                    class="align-items-center justify-content-between row g-0 text-center text-sm-start p-3 border-top"
                                >
                                    <div class="col-sm">
                                        <div class="text-muted">
                                            Showing
                                            <span class="fw-semibold">{{ $recentOrders->count() }}</span>
                                            of
                                            <span class="fw-semibold"
                                                >{{ $totalOrders }}</span
                                            >
                                            orders
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        <ul
                                            class="pagination pagination-rounded m-0"
                                        >
                                            <li class="page-item">
                                                <a href="#" class="page-link"
                                                    ><i
                                                        class="bx bx-left-arrow-alt"
                                                    ></i
                                                ></a>
                                            </li>
                                            <li class="page-item active">
                                                <a href="#" class="page-link"
                                                    >1</a
                                                >
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link"
                                                    >2</a
                                                >
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link"
                                                    >3</a
                                                >
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link"
                                                    ><i
                                                        class="bx bx-right-arrow-alt"
                                                    ></i
                                                ></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- End Container -->
            </div>
@endsection

@push('scripts')
    <script src="{{ asset('build/assets/js/pages/dashboard.sales.js') }}"></script>
@endpush
