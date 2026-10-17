<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>Homework Konveksi | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Login admin Homework Konveksi" />
    <meta name="author" content="Homework Konveksi" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('build/assets/images/homework-konveksi.png') }}" sizes="64x64" type="image/png" />
    <link href="{{ asset('build/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('build/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('build/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('build/assets/js/config.js') }}"></script>
</head>
<body class="authentication-bg">
    <div class="account-pages pt-5 pb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-6">
                    <div class="card auth-card">
                        <div class="card-body px-4 py-5">
                            <div class="mx-auto mb-4 text-center auth-logo">
                                <img
                                    src="{{ asset('build/assets/images/homework-konveksi.png') }}"
                                    height="96"
                                    class="rounded-circle"
                                    alt="Homework Konveksi"
                                />
                            </div>

                            <h2 class="fw-bold text-center fs-18 mb-2">Login Admin</h2>
                            <p class="text-muted text-center mt-1 mb-4">
                                Masuk menggunakan email dan password untuk mengakses seluruh modul.
                            </p>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form method="POST" action="{{ route('login.attempt') }}" class="authentication-form">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Masukkan email"
                                        value="{{ old('email') }}"
                                        required
                                        autofocus
                                    />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password">Password</label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password"
                                        required
                                    />
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="remember"
                                            name="remember"
                                            value="1"
                                            @checked(old('remember'))
                                        />
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-primary" type="submit">
                                        Log In
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('build/assets/js/vendor.js') }}"></script>
    <script src="{{ asset('build/assets/js/app.js') }}"></script>
</body>
</html>
