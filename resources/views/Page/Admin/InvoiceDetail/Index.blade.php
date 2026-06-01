@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Invoice Details</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Invoice</a>
                                    </li>
                                    <li class="breadcrumb-item active">Invoice Details</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-start g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center gap-3">
                                                <img
                                                    src="{{ asset('build/assets/images/homework-konveksi.png') }}"
                                                    alt="homework-konveksi"
                                                    class="rounded"
                                                    style="width: 72px; height: 72px; object-fit: cover;"
                                                />
                                                <div>
                                                    <div class="fw-semibold">Homework Konveksi</div>
                                                    <div class="text-muted">Invoice</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h5 class="card-title mb-1">Invoice: {{ $invoice->invoice_number }}</h5>
                                            <div class="text-muted">
                                                Status:
                                                <span class="fw-medium">{{ ucfirst((string) $invoice->payment_status) }}</span>
                                                @if ($invoice->payment_via)
                                                    <span class="ms-2">Via: <span class="fw-medium">{{ $invoice->payment_via }}</span></span>
                                                @endif
                                            </div>
                                            <div class="text-muted mt-1">
                                                @if ($invoice->issued_at)
                                                    <span>Issued: <span class="fw-medium">{{ $invoice->issued_at->format('d M, Y') }}</span></span>
                                                @endif
                                                @if ($invoice->due_date)
                                                    <span class="ms-2">Due: <span class="fw-medium">{{ $invoice->due_date->format('d M, Y') }}</span></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4 g-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-normal text-muted mb-1">Customer</h6>
                                            <div class="fw-semibold">{{ $invoice->customer?->name ?? '-' }}</div>
                                            <div class="text-muted">{{ $invoice->customer?->institute ?? '-' }}</div>
                                            <div class="text-muted">{{ $invoice->customer?->address ?? '-' }}</div>
                                            <div class="text-muted">Phone: {{ $invoice->customer?->phone ?? '-' }}</div>
                                            <div class="text-muted">Email: {{ $invoice->customer?->email ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted">Issued</td>
                                                            <td class="text-end fw-medium">{{ $invoice->issued_at?->format('d M, Y H:i') ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Due</td>
                                                            <td class="text-end fw-medium">{{ $invoice->due_date?->format('d M, Y') ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Status</td>
                                                            <td class="text-end fw-medium">{{ ucfirst((string) $invoice->payment_status) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Via</td>
                                                            <td class="text-end fw-medium">{{ $invoice->payment_via ?: '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->

                                    <div class="row">
                                        <div class="col-12">
                                            <div
                                                class="table-responsive text-nowrap mt-4 table-centered"
                                            >
                                                <table class="table table-striped mb-0">
                                                    <thead
                                                        class="bg-light bg-opacity-50"
                                                    >
                                                        <tr>
                                                            <th
                                                                class="border-0 py-2"
                                                            >
                                                                Product Name
                                                            </th>
                                                            <th
                                                                class="border-0 py-2"
                                                            >
                                                                Quantity
                                                            </th>
                                                            <th
                                                                class="border-0 py-2"
                                                            >
                                                                Price
                                                            </th>
                                                            <th
                                                                class="text-end border-0 py-2"
                                                            >
                                                                Total
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <!-- end thead -->
                                                    <tbody>
                                                        @forelse ($invoice->items as $item)
                                                            <tr>
                                                                <td>{{ $item->product_name }}</td>
                                                                <td class="text-end">{{ $item->quantity }}</td>
                                                                <td class="text-end">${{ number_format((float) $item->unit_price, 2) }}</td>
                                                                <td class="text-end">${{ number_format((float) $item->line_total, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted py-4">
                                                                    No items.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <!-- end tbody -->
                                                </table>
                                                <!-- end table -->
                                            </div>
                                            <!-- end table responsive -->
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->

                                    <div class="row mt-3">
                                        <div class="col-sm-7">
                                            <h6 class="text-muted mb-1">Notes</h6>
                                            <div class="text-muted">{{ $invoice->notes ?: '-' }}</div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted">Sub-total</td>
                                                            <td class="text-end fw-medium">${{ number_format((float) $invoice->subtotal_amount, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Discount</td>
                                                            <td class="text-end fw-medium">${{ number_format((float) $invoice->discount_amount, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Total</td>
                                                            <td class="text-end fw-bold fs-16">${{ number_format((float) $invoice->total_amount, 2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->

                                    <div class="mt-5 mb-1">
                                        <div class="text-end d-print-none">
                                            <a
                                                href="javascript:window.print()"
                                                class="btn btn-primary"
                                                >Print</a
                                            >
                                            <a
                                                href="javascript:void(0);"
                                                class="btn btn-outline-primary"
                                                >Submit</a
                                            >
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
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
