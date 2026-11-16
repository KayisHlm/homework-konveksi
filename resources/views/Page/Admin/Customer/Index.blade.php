@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Customers</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Order</a>
                                    </li>
                                    <li class="breadcrumb-item active">Customers</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


                    <div class="row">
                        <div class="col-12">
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
                                                    placeholder="Cari nama, instansi, ..."
                                                />
                                            </div>
                                            <select name="sort" class="form-select" style="width: 180px;">
                                                <option value="">Urutkan</option>
                                                <option value="name" @selected(request('sort') === 'name')>Name</option>
                                                <option value="created_at" @selected(request('sort') === 'created_at')>Joining Date</option>
                                                <option value="phone" @selected(request('sort') === 'phone')>Phone</option>
                                                <option value="orders" @selected(request('sort') === 'orders')>Orders</option>
                                            </select>
                                            <select name="has_orders" class="form-select" style="width: 180px;">
                                                <option value="">Semua Customers</option>
                                                <option value="1" @selected(request('has_orders') === '1')>Has Orders</option>
                                                <option value="0" @selected(request('has_orders') === '0')>No Orders</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="{{ route('customer') }}" class="btn btn-outline-secondary">Reset</a>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            <a href="{{ route('customer.create') }}" class="btn btn-primary">
                                                <i class="bx bx-plus me-1"></i>Tambah Customer
                                            </a>
                                        </div>
                                    </form>
                                    <!-- end row -->
                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col-->
                    </div>

                    <div class="card overflow-hidden">
                                <div class="table-responsive table-centered">
                                    <table class="table text-nowrap mb-0">
                                        <thead class="teble-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Instansi</th>
                                                <th>Telepon</th>
                                                <th>Alamat</th>
                                                <th>Pesanan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <!-- end thead-->
                                        <tbody>
                                            @forelse($customers as $customer)
                                                <tr>
                                                    <td>{{ $customer->name }}</td>
                                                    <td>{{ $customer->institute }}</td>
                                                    <td>{{ $customer->phone }}</td>
                                                    <td>{{ $customer->address }}</td>
                                                    <td>{{ $customer->orders_count ?? 0 }}</td>
                                                    <td>
                                                        <a href="{{ route('customer.edit', $customer) }}" class="btn btn-sm btn-soft-primary me-1">
                                                            <i class="bx bx-edit fs-18"></i>
                                                        </a>
                                                        @if ($customer->orders_count > 0)
                                                            <span class="d-inline-block" tabindex="0" title="Tidak dapat dihapus: pelanggan masih memiliki pesanan">
                                                                <button type="button" class="btn btn-sm btn-soft-danger" disabled>
                                                                    <i class="bx bx-trash fs-16"></i>
                                                                </button>
                                                            </span>
                                                        @else
                                                            <form method="POST" action="{{ route('customer.destroy', $customer) }}" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-soft-danger" title="Hapus pelanggan" onclick="return confirm('Hapus pelanggan {{ $customer->name }}?')">
                                                                    <i class="bx bx-trash fs-16"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        No customers found.
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
                                            @if ($customers->total() > 0)
                                                Showing
                                                <span class="fw-semibold">{{ $customers->firstItem() }}</span>
                                                to
                                                <span class="fw-semibold">{{ $customers->lastItem() }}</span>
                                                of
                                                <span class="fw-semibold">{{ $customers->total() }}</span>
                                                Results
                                            @else
                                                Showing <span class="fw-semibold">0</span> Results
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        @php
                                            $currentPage = $customers->currentPage();
                                            $lastPage = $customers->lastPage();
                                            $prevUrl = $customers->previousPageUrl();
                                            $nextUrl = $customers->nextPageUrl();

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
                                                    href="{{ $currentPage <= 1 ? '#' : $customers->url(1) }}"
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
                                                    <a href="{{ $customers->url(1) }}" class="page-link">1</a>
                                                </li>
                                                @if ($startPage > 2)
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="#" aria-disabled="true" tabindex="-1">...</a>
                                                    </li>
                                                @endif
                                            @endif

                                            @for ($page = $startPage; $page <= $endPage; $page++)
                                                <li class="page-item @if ($page === $currentPage) active @endif">
                                                    <a href="{{ $customers->url($page) }}" class="page-link">{{ $page }}</a>
                                                </li>
                                            @endfor

                                            @if ($endPage < $lastPage)
                                                @if ($endPage < $lastPage - 1)
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="#" aria-disabled="true" tabindex="-1">...</a>
                                                    </li>
                                                @endif
                                                <li class="page-item @if ($lastPage === $currentPage) active @endif">
                                                    <a href="{{ $customers->url($lastPage) }}" class="page-link">{{ $lastPage }}</a>
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
                                                    href="{{ $currentPage >= $lastPage ? '#' : $customers->url($lastPage) }}"
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
                </div>
                <!-- End Container -->
            </div>
@endsection
