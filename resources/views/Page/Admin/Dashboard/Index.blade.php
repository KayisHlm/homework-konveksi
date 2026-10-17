@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
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

        <div class="row g-3">
            <div class="col-xl col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body overflow-hidden position-relative">
                        <iconify-icon icon="iconamoon:shopping-card-add-duotone" class="fs-36 text-info"></iconify-icon>
                        <h3 class="mb-0 fw-bold mt-3 mb-1">
                            Rp <span class="counter-value" data-target="{{ (int) round($totalSalesPaid) }}">0</span>
                        </h3>
                        <p class="text-muted mb-2">Penjualan Lunas</p>
                        <span class="badge fs-12 badge-soft-success">
                            <i class="ti ti-arrow-badge-up"></i> {{ $paidInvoiceCount }} invoice lunas
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body overflow-hidden position-relative">
                        <iconify-icon icon="mdi:wallet-outline" class="fs-36 text-danger"></iconify-icon>
                        <h3 class="mb-0 fw-bold mt-3 mb-1">
                            Rp <span class="counter-value" data-target="{{ (int) round($totalExpenses) }}">0</span>
                        </h3>
                        <p class="text-muted mb-2">Total Pengeluaran</p>
                        <span class="badge fs-12 badge-soft-danger">
                            <i class="ti ti-receipt-2"></i> Semua kategori biaya
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body overflow-hidden position-relative">
                        <iconify-icon icon="mdi:finance" class="fs-36 text-success"></iconify-icon>
                        <h3 class="mb-0 fw-bold mt-3 mb-1">
                            Rp {{ number_format($profit, 0, ',', '.') }}
                        </h3>
                        <p class="text-muted mb-2">Laba</p>
                        <span class="badge fs-12 {{ $profit >= 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                            <i class="ti {{ $profit >= 0 ? 'ti-trending-up' : 'ti-trending-down' }}"></i>
                            Penjualan lunas dikurangi biaya
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body overflow-hidden position-relative">
                        <iconify-icon icon="iconamoon:certificate-badge-duotone" class="fs-36 text-warning"></iconify-icon>
                        <h3 class="mb-0 fw-bold mt-3 mb-1">
                            Rp <span class="counter-value" data-target="{{ (int) round($totalOutstanding) }}">0</span>
                        </h3>
                        <p class="text-muted mb-2">Outstanding Invoice</p>
                        <span class="badge fs-12 badge-soft-warning">
                            <i class="ti ti-alert-circle"></i> {{ $outstandingInvoiceCount }} invoice belum lunas
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-body overflow-hidden position-relative">
                        <iconify-icon icon="iconamoon:delivery-fast-duotone" class="fs-36 text-purple"></iconify-icon>
                        <h3 class="mb-0 fw-bold mt-3 mb-1">
                            <span class="counter-value" data-target="{{ $totalOrders }}">0</span>
                        </h3>
                        <p class="text-muted mb-2">Total Order</p>
                        <span class="badge fs-12 badge-soft-primary">
                            <i class="ti ti-package"></i> {{ $newOrdersThisMonth }} order baru bulan ini
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1 align-items-start">
            <div class="col-xxl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Overview</h4>
                            <p class="text-muted mb-0">Pergerakan penjualan, outstanding, dan order 12 bulan terakhir</p>
                        </div>
                        <span class="badge badge-soft-primary">12 bulan terakhir</span>
                    </div>
                    <div class="card-body">
                        <div dir="ltr">
                            <div id="dash-overview-chart" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title mb-1">Recent Orders</h4>
                                <p class="text-muted mb-0">Pesanan terbaru dengan informasi produksi dan pelanggan</p>
                            </div>
                            <a href="{{ route('order.create') }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-plus me-1"></i>Create Order
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive table-centered">
                        <table class="table mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="border-0 py-2">No. Pesanan</th>
                                    <th class="border-0 py-2">Tanggal</th>
                                    <th class="border-0 py-2">Produk</th>
                                    <th class="border-0 py-2">Pelanggan</th>
                                    <th class="border-0 py-2">Total</th>
                                    <th class="border-0 py-2">Produksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('order-detail', $order) }}">
                                                {{ $order->order_number ?? "#ORD{$order->id}" }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at?->format('d M, Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm bg-light rounded d-inline-flex align-items-center justify-content-center">
                                                    <iconify-icon icon="iconamoon:shopping-bag-duotone" class="fs-20 text-muted"></iconify-icon>
                                                </div>
                                                <span>{{ $order->product_name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $order->customer?->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $order->customer?->email ?? '-' }}</small>
                                        </td>
                                        <td>Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $productionClass = match ($order->production_status) {
                                                    'selesai' => 'success',
                                                    'quality_control' => 'warning',
                                                    'proses_produksi' => 'info',
                                                    'menunggu_bahan' => 'secondary',
                                                    default => 'primary',
                                                };
                                            @endphp
                                            <span class="badge badge-soft-{{ $productionClass }}">
                                                {{ ucwords(str_replace('_', ' ', (string) $order->production_status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="align-items-center justify-content-between row g-0 text-center text-sm-start p-3 border-top">
                        <div class="col-sm">
                            <div class="text-muted">
                                Menampilkan <span class="fw-semibold">{{ $recentOrders->count() }}</span> dari
                                <span class="fw-semibold">{{ $totalOrders }}</span> order terbaru
                            </div>
                        </div>
                        <div class="col-sm-auto mt-3 mt-sm-0">
                            <a href="{{ route('order') }}" class="btn btn-sm btn-light">Lihat semua order</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Penjualan Berdasarkan Produk</h4>
                            <p class="text-muted mb-0">Top 5 produk paling sering dipesan</p>
                        </div>
                        <span class="badge badge-soft-primary">Top 5 produk</span>
                    </div>
                    <div class="card-body">
                        <div dir="ltr">
                            <div id="sales-category-chart" class="apex-charts"></div>
                        </div>
                        <div class="table-responsive mb-n1 mt-2">
                            <table class="table table-nowrap table-borderless table-sm table-centered mb-0">
                                <thead class="bg-light bg-opacity-50 thead-sm">
                                    <tr>
                                        <th class="py-1">Produk</th>
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
                                            <td>{{ number_format(($product['orders'] / $totalProductOrders) * 100, 1, ',', '.') }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Belum ada data produk.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Status Produksi</h4>
                            <p class="text-muted mb-0">Distribusi pesanan berdasarkan tahap produksi</p>
                        </div>
                        <iconify-icon icon="iconamoon:timer-duotone" class="fs-24 text-primary"></iconify-icon>
                    </div>
                    <div class="card-body">
                        @php $ordersCount = max($totalOrders, 1); @endphp
                        @foreach ($ordersByStatus as $status => $count)
                            @php
                                $percentage = ($count / $ordersCount) * 100;
                                $barClass = match ($status) {
                                    'selesai' => 'success',
                                    'quality_control' => 'warning',
                                    'proses_produksi' => 'info',
                                    'menunggu_bahan' => 'secondary',
                                    default => 'primary',
                                };
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                    <span class="fw-semibold">{{ $count }}</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-{{ $barClass }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    height: 360,
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 900,
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
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    hover: {
                        size: 6,
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
                grid: {
                    borderColor: '#f1f3fa',
                    strokeDashArray: 4,
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
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 900,
                    },
                },
                series: salesByProduct.map((item) => item.orders),
                labels: salesByProduct.map((item) => item.name),
                dataLabels: {
                    enabled: true,
                },
                legend: {
                    position: 'bottom',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                        },
                    },
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
