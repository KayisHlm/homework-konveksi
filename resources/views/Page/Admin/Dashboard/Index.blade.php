@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Dashboard</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">Dashboards</li>
                                    <li class="breadcrumb-item active">Dashboard</li>
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
                                        {{ $paidInvoiceCount }} invoice lunas</span
                                    >                               
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
                                        {{ $totalOrders }}
                                    </h3>
                                    <p class="text-muted">Total Order</p>
                                    <span class="badge fs-12 badge-soft-warning"
                                        ><i class="ti ti-clock-hour-4"></i>
                                        {{ $pendingOrderCount }} pending</span
                                    >
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
                                    <span class="badge fs-12 badge-soft-info"
                                        ><i class="ti ti-user-plus"></i>
                                        {{ $newCustomersThisMonth }} baru bulan ini</span
                                    >
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
                                        {{ $totalInvoices }}
                                    </h3>
                                    <p class="text-muted">Total Invoice</p>
                                    <span class="badge fs-12 badge-soft-success"
                                        ><i class="ti ti-file-invoice"></i>
                                        {{ $newInvoicesThisMonth }} dibuat bulan ini</span
                                    >
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
                                    <span class="badge fs-12 badge-soft-danger"
                                        ><i class="ti ti-alert-circle"></i>
                                        {{ $outstandingInvoiceCount }} invoice tertunda</span
                                    >
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
                                    <span class="badge badge-soft-primary">12 bulan terakhir</span>
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
                                        Penjualan Berdasarkan Produk
                                    </h4>
                                    <span class="badge badge-soft-primary">Top 5 produk</span>
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
                                                        Produk
                                                    </th>
                                                    <th class="py-1">Orders</th>
                                                    <th class="py-1">Porsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalProductOrders = max($salesByProduct->sum('orders'), 1);
                                                @endphp
                                                @forelse ($salesByProduct as $product)
                                                    <tr>
                                                        <td>{{ $product['name'] }}</td>
                                                        <td>{{ number_format($product['orders'], 0, ',', '.') }}</td>
                                                        <td>
                                                            {{ number_format(($product['orders'] / $totalProductOrders) * 100, 1, ',', '.') }}%
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-3">
                                                            Belum ada data produk.
                                                        </td>
                                                    </tr>
                                                @endforelse
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
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div
                                                                class="avatar-sm bg-light rounded d-inline-flex align-items-center justify-content-center"
                                                            >
                                                                <iconify-icon
                                                                    icon="iconamoon:shopping-bag-duotone"
                                                                    class="fs-20 text-muted"
                                                                ></iconify-icon>
                                                            </div>
                                                            <span>{{ $order->product_name ?? '-' }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="#!">{{ $order->customer?->name ?? '-' }}</a>
                                                    </td>
                                                    <td>{{ $order->customer?->email ?? '-' }}</td>
                                                    <td>{{ $order->customer?->phone ?? '-' }}</td>
                                                    <td>{{ $order->customer?->address ?? '-' }}</td>
                                                    <td>{{ ucfirst($order->payment_type) }}</td>
                                                    <td>
                                                        <i
                                                            class="bx bxs-circle me-1 {{ $order->payment_status == 'paid' ? 'text-success' : ($order->payment_status == 'pending' ? 'text-primary' : 'text-danger') }}"
                                                        ></i>
                                                        {{ ucfirst($order->payment_status) }}
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
                                            Menampilkan
                                            <span class="fw-semibold">{{ $recentOrders->count() }}</span>
                                            dari
                                            <span class="fw-semibold"
                                                >{{ $totalOrders }}</span
                                            >
                                            order terbaru
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        <a href="{{ route('order') }}" class="btn btn-sm btn-light">
                                            Lihat semua order
                                        </a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const overviewElement = document.querySelector('#dash-overview-chart');
            const salesCategoryElement = document.querySelector('#sales-category-chart');
            const overviewLabels = @json($overviewLabels);
            const paidSalesSeries = @json($paidSalesSeries);
            const outstandingSalesSeries = @json($outstandingSalesSeries);
            const orderSeries = @json($orderSeries);
            const salesByProduct = @json($salesByProduct);

            const currencyFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            });

            const renderFallback = (element, message) => {
                if (!element) {
                    return;
                }

                element.innerHTML = `<div class="text-center text-muted py-5">${message}</div>`;
            };

            if (typeof ApexCharts === 'undefined') {
                renderFallback(overviewElement, 'Library chart tidak tersedia.');
                renderFallback(salesCategoryElement, 'Library chart tidak tersedia.');
                return;
            }

            if (overviewElement) {
                new ApexCharts(overviewElement, {
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: {
                            show: false,
                        },
                    },
                    series: [
                        {
                            name: 'Penjualan Lunas',
                            data: paidSalesSeries,
                        },
                        {
                            name: 'Belum Lunas',
                            data: outstandingSalesSeries,
                        },
                        {
                            name: 'Jumlah Order',
                            data: orderSeries,
                        },
                    ],
                    stroke: {
                        curve: 'smooth',
                        width: [3, 3, 3],
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.05,
                            stops: [0, 90, 100],
                        },
                    },
                    colors: ['#3e60d5', '#f15776', '#47ad77'],
                    xaxis: {
                        categories: overviewLabels,
                    },
                    yaxis: [
                        {
                            labels: {
                                formatter: (value) => currencyFormatter.format(value || 0),
                            },
                        },
                        {
                            opposite: true,
                            labels: {
                                formatter: (value) => Math.round(value || 0),
                            },
                        },
                    ],
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: [
                            {
                                formatter: (value) => currencyFormatter.format(value || 0),
                            },
                            {
                                formatter: (value) => currencyFormatter.format(value || 0),
                            },
                            {
                                formatter: (value) => `${Math.round(value || 0)} order`,
                            },
                        ],
                    },
                    legend: {
                        position: 'top',
                    },
                }).render();
            }

            if (salesCategoryElement) {
                if (!salesByProduct.length) {
                    renderFallback(salesCategoryElement, 'Belum ada data produk.');
                    return;
                }

                new ApexCharts(salesCategoryElement, {
                    chart: {
                        type: 'donut',
                        height: 320,
                    },
                    series: salesByProduct.map((item) => item.orders),
                    labels: salesByProduct.map((item) => item.name),
                    dataLabels: {
                        enabled: true,
                    },
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        y: {
                            formatter: (value) => `${Math.round(value || 0)} order`,
                        },
                    },
                    colors: ['#3e60d5', '#47ad77', '#fa5c7c', '#f9bc0b', '#6c757d'],
                }).render();
            }
        });
    </script>
@endpush
