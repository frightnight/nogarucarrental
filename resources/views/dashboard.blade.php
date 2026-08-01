<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Nogaru Car Rental</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}" />
    <!-- Theme Config Js -->
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>

    <!-- Vendor css -->
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">Dashboard</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted">You are logged in as <strong>{{ auth()->user()->name }}</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->has('social'))
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ $errors->first('social') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->provider)
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-muted mb-0">Connected with <strong>{{ ucfirst(auth()->user()->provider) }}</strong>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-muted">Connect a social account to sign in more easily in the future.</p>
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('social.connect.redirect', 'google') }}" class="btn btn-outline-secondary">
                                                <i class="ti ti-brand-google me-1"></i> Connect with Google
                                            </a>
                                            <a href="{{ route('social.connect.redirect', 'facebook') }}" class="btn btn-outline-secondary">
                                                <i class="ti ti-brand-facebook me-1"></i> Connect with Facebook
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div> <!-- container -->
            </div> <!-- content -->

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0">{{ date('Y') }} &copy; Nogaru Car Rental Platform</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0 text-end">Dashboard</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('theme/assets/js/app.js') }}"></script>
</body>
</html>