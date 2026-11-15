<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
        <!-- Title Meta -->
        <meta charset="utf-8" />
        <title>Homework Konveksi</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta
            name="description"
            content="A fully responsive premium admin dashboard template"
        />
        <meta name="author" content="Techzaa" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('build/assets/images/homework-konveksi.png') }}" sizes="64x64" type="image/png" />

        <!-- Vendor css (Require in all Page) -->
        <link href="{{ asset('build/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Icons css (Require in all Page) -->
        <link href="{{ asset('build/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- App css (Require in all Page) -->
        <link href="{{ asset('build/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Theme Config js (Require in all Page) -->
        <script src="{{ asset('build/assets/js/config.js') }}"></script>


</head>

<body>
    <!-- START Wrapper -->
    <div class="wrapper">
        @include('Layout.Admin._Header')
        @include('Layout.Admin._Activity')
        @include('Layout.Admin._SideBar')
        @include('Layout.Admin._Footer')
        @yield('Content')        
    </div>
    <!-- END Wrapper -->

    <script src="{{ asset('build/assets/js/vendor.js') }}"></script>
    <script src="{{ asset('build/assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success') || session('error'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 4000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: @json(session('error') ? 'error' : 'success'),
                title: @json(session('error') ?? session('success')),
            })
        </script>
    @endif
    @stack('scripts')
</body>
</html>
