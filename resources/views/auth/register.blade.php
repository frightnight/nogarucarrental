<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Nogaru Car Rental</title>

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
                                <h4 class="text-dark-50 text-center mt-0 fw-bold">Create an account</h4>
                                <p class="text-muted mb-4">Enter your details to register for a new account.</p>
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

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Enter your name">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required placeholder="Enter your email">
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

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <div class="input-group input-group-merge">
                                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required placeholder="Confirm your password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-primary" type="submit">Register</button>
                                </div>
                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Applying as a driver? <a href="{{ route('driver.register') }}" class="text-muted ms-1"><b>Driver registration</b></a></p>
                            <p class="text-muted">Already have an account? <a href="{{ route('login') }}" class="text-muted ms-1"><b>Log In</b></a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
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