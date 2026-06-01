@extends('Layout.Admin._Layout')
@section('Content')
            <div class="page-content">
                <!-- Start Container -->
                <div class="container-xxl">
                    <!-- ========== Page Title Start ========== -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="mb-0 fw-semibold">Welcome</h4>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript: void(0);">Pages</a>
                                    </li>
                                    <li class="breadcrumb-item active">Welcome</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== Page Title End ========== -->


                    <!-- Start here.... -->
                </div>
                <!-- End Container -->

                <!-- ========== Footer Start ========== -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 text-center">
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>
                                &copy; Reback.
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- ========== Footer End ========== -->

            </div>
@endsection
