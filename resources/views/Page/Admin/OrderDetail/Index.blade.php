@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Order Details</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Ecommerce</a>
                                    </li>
                                    <li class="breadcrumb-item active">Order Details</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-xl-7">
                            <ul class="progressbar ps-0 my-5 pb-5">
                                @php
                                    $stepCount = match ($order->payment_status) {
                                        'paid' => 4,
                                        'cancelled' => 1,
                                        default => 2,
                                    };
                                @endphp
                                <li class="{{ $stepCount >= 1 ? 'active' : '' }}">Order Placed</li>
                                <li class="{{ $stepCount >= 2 ? 'active' : '' }}">Processed</li>
                                <li class="{{ $stepCount >= 3 ? 'active' : '' }}">Paid</li>
                                <li class="{{ $stepCount >= 4 ? 'active' : '' }}">Completed</li>
                            </ul>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-xl-7">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        Order #{{ $order->id }}
                                    </h5>
                                    <div class="table-responsive">
                                        <table
                                            class="table table-centered table-dashed mb-0"
                                        >
                                            <thead class="table-">
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Payment Type</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <!-- end thead -->
                                            <tbody>
                                                <tr>
                                                    <td>{{ $order->product_name }}</td>
                                                    <td>{{ ucfirst((string) $order->payment_type) }}</td>
                                                    <td>{{ ucfirst((string) $order->payment_status) }}</td>
                                                    <td>{{ $order->created_at?->format('d M, Y H:i') }}</td>
                                                </tr>
                                            </tbody>
                                            <!-- end tbody -->
                                        </table>
                                        <!-- end table -->
                                    </div>
                                    <!-- end table responsive -->
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xl-5">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        Order Summary
                                    </h5>
                                    <div
                                        class="table-responsive text-nowrap table-centered"
                                    >
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <!-- end thead -->
                                            <tbody>
                                                <tr>
                                                    <td>Customer :</td>
                                                    <td>{{ $order->customer?->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Email :</td>
                                                    <td>{{ $order->customer?->email ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Phone :</td>
                                                    <td>{{ $order->customer?->phone ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">
                                                        Status :
                                                    </td>
                                                    <td class="fw-semibold">
                                                        {{ ucfirst((string) $order->payment_status) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <!-- end tbody -->
                                        </table>
                                        <!-- end table -->
                                    </div>
                                    <!-- end table responsive -->
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card card-height-100">
                                <div class="card-body">
                                    <div class="float-end">
                                        <a
                                            href="javascript:void(0);"
                                            class="text-primary"
                                        >
                                            <i class="bx bx-edit me-1"></i>
                                            Change
                                        </a>
                                    </div>
                                    <h5 class="card-title mb-3">
                                        Shipping Information
                                    </h5>
                                    <h5 class="">{{ $order->name }}</h5>
                                    <address class="mb-0">
                                        {{ $order->address }}<br />
                                        <abbr title="phone">Phone :</abbr>
                                        {{ $order->phone }}
                                    </address>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col-lg-4">
                            <div class="card card-height-100">
                                <div class="card-body">
                                    <div class="float-end">
                                        <a
                                            href="javascript:void(0);"
                                            class="text-primary"
                                        >
                                            <i class="bx bx-edit me-1"></i>
                                            Change
                                        </a>
                                    </div>
                                    <h5 class="card-title mb-3">
                                        Billing Information
                                    </h5>
                                    <p class="mb-1">
                                        Payment Type :
                                        <span class="text-muted me-2"></span>
                                        <b>{{ ucfirst((string) $order->payment_type) }}</b>
                                    </p>
                                    <!-- <p class="mb-1">Holder Name : <span class="text-muted me-2"></span> <b>Alice S. Shepherd</b></p> -->
                                    <p class="mb-1">
                                        Status :
                                        <span class="text-muted me-2"></span>
                                        <b>{{ ucfirst((string) $order->payment_status) }}</b>
                                    </p>
                                    <p class="mb-1">
                                        Email :
                                        <span class="text-muted me-2"></span>
                                        <b>{{ $order->email }}</b>
                                    </p>
                                    <p class="mb-0">
                                        Date :
                                        <span class="text-muted me-2"></span>
                                        <b>{{ $order->created_at?->format('d M, Y H:i') }}</b>
                                    </p>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col-lg-4">
                            <div class="card card-height-100">
                                <div class="card-body">
                                    <div class="float-end">
                                        <a
                                            href="javascript:void(0);"
                                            class="text-primary"
                                            >View All Detail</a
                                        >
                                    </div>
                                    <h5 class="card-title mb-3">
                                        Delivery Information
                                    </h5>
                                    <div class="text-center">
                                        <i class="bx bx-cart h2"></i>
                                        <h5>Customer</h5>
                                        <p class="mb-1">
                                            Order ID :
                                            <span
                                                class="text-muted me-2"
                                            ></span>
                                            <b>#{{ $order->id }}</b>
                                        </p>
                                        <p class="mb-0">
                                            Customer Email :
                                            <span
                                                class="text-muted me-2"
                                            ></span>
                                            <b>{{ $order->customer?->email ?? '-' }}</b>
                                        </p>
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
