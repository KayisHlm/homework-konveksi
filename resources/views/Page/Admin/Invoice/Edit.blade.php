@extends('Layout.Admin._Layout')
@section('Content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-0 fw-semibold">Edit Invoice</h4>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('invoice') }}">Invoice</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        @include('Page.Admin.Invoice._form')
    </div>
</div>
@endsection
