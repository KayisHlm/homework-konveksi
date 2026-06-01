@extends('Layout.Admin._Layout')

@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Orders List</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Order</a>
                                    </li>
                                    <li class="breadcrumb-item active">Orders List</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <div class="search-bar">
                                                <span><i class="bx bx-search-alt"></i></span>
                                                <input
                                                    type="search"
                                                    class="form-control"
                                                    name="q"
                                                    value="{{ request('q') }}"
                                                    placeholder="Search..."
                                                />
                                            </div>
                                            <select name="sort" class="form-select" style="width: 180px;">
                                                <option value="">Sort</option>
                                                <option value="id_desc" @selected(request('sort') === 'id_desc')>ID (Desc)</option>
                                                <option value="id_asc" @selected(request('sort') === 'id_asc')>ID (Asc)</option>
                                                <option value="date_desc" @selected(request('sort') === 'date_desc')>Date (Newest)</option>
                                                <option value="date_asc" @selected(request('sort') === 'date_asc')>Date (Oldest)</option>
                                                <option value="product_asc" @selected(request('sort') === 'product_asc')>Product (A-Z)</option>
                                                <option value="product_desc" @selected(request('sort') === 'product_desc')>Product (Z-A)</option>
                                                <option value="customer_asc" @selected(request('sort') === 'customer_asc')>Customer (A-Z)</option>
                                                <option value="customer_desc" @selected(request('sort') === 'customer_desc')>Customer (Z-A)</option>
                                            </select>
                                            <select name="payment_status" class="form-select" style="width: 180px;">
                                                <option value="">All Status</option>
                                                <option value="pending" @selected(request('payment_status') === 'pending')>Pending</option>
                                                <option value="paid" @selected(request('payment_status') === 'paid')>Paid</option>
                                                <option value="cancelled" @selected(request('payment_status') === 'cancelled')>Cancelled</option>
                                            </select>
                                            <select name="payment_type" class="form-select" style="width: 180px;">
                                                <option value="">All Payment</option>
                                                <option value="cash" @selected(request('payment_type') === 'cash')>Cash</option>
                                                <option value="transfer" @selected(request('payment_type') === 'transfer')>Transfer</option>
                                                <option value="instalment" @selected(request('payment_type') === 'instalment')>Instalment</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="{{ route('order') }}" class="btn btn-outline-secondary">Reset</a>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            <a href="{{ route('order.create') }}" class="btn btn-primary">
                                                <i class="bx bx-plus me-1"></i>Create Order
                                            </a>
                                        </div>
                                    </form>
                                </div>
                                <!-- end card body -->
                                <div class="table-responsive table-centered">
                                    <table class="table text-nowrap mb-0">
                                        <thead class="bg-light bg-opacity-50">
                                            <tr>
                                                <th>Order ID.</th>
                                                <th>Date</th>
                                                <th>Product Name</th>
                                                <th>Customer Name</th>
                                                <th>Email ID</th>
                                                <th>Phone No.</th>
                                                <th>Address</th>
                                                <th>Payment Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <!-- end thead-->
                                        <tbody>
                                            @forelse($orders as $order)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('order-detail', $order) }}">{{ $order->id }}</a>
                                                    </td>
                                                    <td>{{ $order->created_at?->format('d M, Y H:i') }}</td>
                                                    <td>{{ $order->product_name }}</td>
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
                                                    <td>
                                                        <a href="{{ route('order-detail', $order) }}" class="btn btn-sm btn-soft-secondary">
                                                            <i class="bx bx-show fs-16"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted py-4">
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
                                            @if ($orders->total() > 0)
                                                Showing
                                                <span class="fw-semibold">{{ $orders->firstItem() }}</span>
                                                to
                                                <span class="fw-semibold">{{ $orders->lastItem() }}</span>
                                                of
                                                <span class="fw-semibold">{{ $orders->total() }}</span>
                                                orders
                                            @else
                                                Showing <span class="fw-semibold">0</span> orders
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        @php
                                            $currentPage = $orders->currentPage();
                                            $lastPage = $orders->lastPage();
                                            $prevUrl = $orders->previousPageUrl();
                                            $nextUrl = $orders->nextPageUrl();

                                            if ($lastPage <= 3) {
                                                $startPage = 1;
                                                $endPage = $lastPage;
                                            } elseif ($currentPage <= 1) {
                                                $startPage = 1;
                                                $endPage = 3;
                                            } elseif ($currentPage >= $lastPage) {
                                                $startPage = max(1, $lastPage - 2);
                                                $endPage = $lastPage;
                                            } else {
                                                $startPage = max(1, $currentPage - 1);
                                                $endPage = min($lastPage, $currentPage + 1);
                                            }
                                        @endphp

                                        <ul class="pagination pagination-rounded m-0">
                                            <li class="page-item @if ($currentPage <= 1) disabled @endif">
                                                <a
                                                    href="{{ $currentPage <= 1 ? '#' : $orders->url(1) }}"
                                                    class="page-link"
                                                    @if ($currentPage <= 1) aria-disabled="true" tabindex="-1" @endif
                                                >
                                                    <i class="bx bx-chevrons-left"></i>
                                                </a>
                                            </li>

                                            <li class="page-item @if (!$prevUrl) disabled @endif">
                                                <a
                                                    href="{{ $prevUrl ?: '#' }}"
                                                    class="page-link"
                                                    @if (!$prevUrl) aria-disabled="true" tabindex="-1" @endif
                                                >
                                                    <i class="bx bx-left-arrow-alt"></i>
                                                </a>
                                            </li>

                                            @if ($startPage > 1)
                                                <li class="page-item @if (1 === $currentPage) active @endif">
                                                    <a href="{{ $orders->url(1) }}" class="page-link">1</a>
                                                </li>
                                                @if ($startPage > 2)
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="#" aria-disabled="true" tabindex="-1">...</a>
                                                    </li>
                                                @endif
                                            @endif

                                            @for ($page = $startPage; $page <= $endPage; $page++)
                                                <li class="page-item @if ($page === $currentPage) active @endif">
                                                    <a href="{{ $orders->url($page) }}" class="page-link">{{ $page }}</a>
                                                </li>
                                            @endfor

                                            @if ($endPage < $lastPage)
                                                @if ($endPage < $lastPage - 1)
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="#" aria-disabled="true" tabindex="-1">...</a>
                                                    </li>
                                                @endif
                                                <li class="page-item @if ($lastPage === $currentPage) active @endif">
                                                    <a href="{{ $orders->url($lastPage) }}" class="page-link">{{ $lastPage }}</a>
                                                </li>
                                            @endif

                                            <li class="page-item @if (!$nextUrl) disabled @endif">
                                                <a
                                                    href="{{ $nextUrl ?: '#' }}"
                                                    class="page-link"
                                                    @if (!$nextUrl) aria-disabled="true" tabindex="-1" @endif
                                                >
                                                    <i class="bx bx-right-arrow-alt"></i>
                                                </a>
                                            </li>

                                            <li class="page-item @if ($currentPage >= $lastPage) disabled @endif">
                                                <a
                                                    href="{{ $currentPage >= $lastPage ? '#' : $orders->url($lastPage) }}"
                                                    class="page-link"
                                                    @if ($currentPage >= $lastPage) aria-disabled="true" tabindex="-1" @endif
                                                >
                                                    <i class="bx bx-chevrons-right"></i>
                                                </a>
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
