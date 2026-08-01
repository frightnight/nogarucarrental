<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Nogaru Car Rental</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}" />
    <!-- Theme Config Js -->
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>

    <!-- Vendor css -->
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body class="authentication-bg position-relative">
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">
                        <!-- Logo -->
                        <div class="card-header py-4 text-center bg-primary">
                            <a href="{{ url('/') }}">
                                <span><img src="{{ asset('theme/assets/images/logo.png') }}" alt="logo" height="28"></span>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center mt-0 fw-bold">Sign In</h4>
                                <p class="text-muted mb-4">Enter your email address and password to access your account.</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input id="password" name="password" type="password" class="form-control" required placeholder="Enter your password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="remember" class="form-check-input" id="checkbox-signin">
                                        <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                    </div>
                                </div>

                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-primary" type="submit">Sign In</button>
                                </div>
                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-muted ms-1"><b>Sign Up</b></a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row mt-2">
                        <div class="col-12 text-center">
                            <p class="text-muted">Or sign in with</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-brand-google me-1"></i> Google
                                </a>
                                <a href="{{ route('social.redirect', 'facebook') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-brand-facebook me-1"></i> Facebook
                                </a>
                            </div>
                        </div>
                    </div>

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt">
        <p class="text-muted mb-0">{{ date('Y') }} &copy; Nogaru Car Rental</p>
    </footer>

    <!-- Vendor js -->
    <script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('theme/assets/js/app.js') }}"></script>
</body>
</html>