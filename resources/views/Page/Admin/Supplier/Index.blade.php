@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Supplier List</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Order</a>
                                    </li>
                                    <li class="breadcrumb-item active">Supplier List</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


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
                                            placeholder="Search (pic, store, category)..."
                                        />
                                    </div>
                                    <select name="sort" class="form-select" style="width: 180px;">
                                        <option value="">Sort</option>
                                        <option value="id_desc" @selected(request('sort') === 'id_desc')>ID (Desc)</option>
                                        <option value="id_asc" @selected(request('sort') === 'id_asc')>ID (Asc)</option>
                                        <option value="pic_asc" @selected(request('sort') === 'pic_asc')>PIC (A-Z)</option>
                                        <option value="pic_desc" @selected(request('sort') === 'pic_desc')>PIC (Z-A)</option>
                                        <option value="store_asc" @selected(request('sort') === 'store_asc')>Store (A-Z)</option>
                                        <option value="store_desc" @selected(request('sort') === 'store_desc')>Store (Z-A)</option>
                                        <option value="category_asc" @selected(request('sort') === 'category_asc')>Category (A-Z)</option>
                                        <option value="category_desc" @selected(request('sort') === 'category_desc')>Category (Z-A)</option>
                                    </select>
                                    <select name="category" class="form-select" style="width: 220px;">
                                        <option value="">All Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('supplier') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>

                                <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                                    <a href="{{ route('supplier.create') }}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>Add Supplier
                                    </a>
                                </div>
                            </form>
                            <!-- end row -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                    <div class="card overflow-hidden">
                                <div class="table-responsive table-centered">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 16%;">PIC Name</th>
                                                <th style="width: 18%;">Store Name</th>
                                                <th style="width: 18%;">Product Category</th>
                                                <th style="width: 14%;">Phone Number</th>
                                                <th style="width: 26%;">Address</th>
                                                <th class="text-center" style="width: 8%;">Action</th>
                                            </tr>
                                        </thead>
                                        <!-- end thead-->
                                        <tbody>
                                            @forelse ($suppliers as $supplier)
                                                <tr>
                                                    <td class="text-wrap">{{ $supplier->pic_name }}</td>
                                                    <td class="text-wrap">{{ $supplier->store_name }}</td>
                                                    <td class="text-wrap">{{ $supplier->product_category }}</td>
                                                    <td class="text-nowrap">{{ $supplier->phone }}</td>
                                                    <td class="text-wrap">{{ $supplier->address }}</td>
                                                    <td class="text-center">
                                                        <div class="d-inline-flex align-items-center gap-1">
                                                            <a href="{{ route('supplier.edit', $supplier) }}" class="btn btn-sm btn-soft-primary">
                                                                <i class="bx bx-edit fs-18"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-soft-danger" disabled>
                                                                <i class="bx bx-trash fs-18"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">No suppliers found.</td>
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
                                            @if ($suppliers->total() > 0)
                                                Showing
                                                <span class="fw-semibold">{{ $suppliers->firstItem() }}</span>
                                                to
                                                <span class="fw-semibold">{{ $suppliers->lastItem() }}</span>
                                                of
                                                <span class="fw-semibold">{{ $suppliers->total() }}</span>
                                                Results
                                            @else
                                                Showing <span class="fw-semibold">0</span> Results
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        @php
                                            $currentPage = $suppliers->currentPage();
                                            $lastPage = $suppliers->lastPage();
                                            $prevUrl = $suppliers->previousPageUrl();
                                            $nextUrl = $suppliers->nextPageUrl();

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
                                                    href="{{ $currentPage <= 1 ? '#' : $suppliers->url(1) }}"
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
                                                    <a href="{{ $suppliers->url(1) }}" class="page-link">1</a>
                                                </li>
                                                @if ($startPage > 2)
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="#" aria-disabled="true" tabindex="-1">...</a>
                                                    </li>
                                                @endif
                                            @endif

                                            @for ($page = $startPage; $page <= $endPage; $page++)
                                                <li class="page-item @if ($page === $currentPage) active @endif">
                                                    <a href="{{ $suppliers->url($page) }}" class="page-link">{{ $page }}</a>
                                                </li>
                                            @endfor

                                            @if ($endPage < $lastPage)
                                                @if ($endPage < $lastPage - 1)
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="#" aria-disabled="true" tabindex="-1">...</a>
                                                    </li>
                                                @endif
                                                <li class="page-item @if ($lastPage === $currentPage) active @endif">
                                                    <a href="{{ $suppliers->url($lastPage) }}" class="page-link">{{ $lastPage }}</a>
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
                                                    href="{{ $currentPage >= $lastPage ? '#' : $suppliers->url($lastPage) }}"
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
                    </div>
                </div>
                <!-- End Container -->
            </div>
@endsection
