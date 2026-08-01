<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a Car Rental Business | Nogaru Car Rental</title>

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
                <div class="container-fluid">

                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm"><i class="ti ti-user me-1"></i> Sign In</a>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm"><i class="ti ti-user-plus me-1"></i> Register</a>
                                </div>
                                <h4 class="page-title">Car Rental Marketplace</h4>
                                <p class="text-muted mb-0">Browse trusted rental businesses, compare their fleet, and pick the right vehicle for your trip.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Business Cards -->
                    <div class="row">
                        @foreach ($businesses as $business)
                            <div class="col-xl-6">
                                <div class="card card-h-100">
                                    <a href="{{ route('business.landing', ['businessSlug' => $business['slug']]) }}/" class="text-body text-decoration-none">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div>
                                                    <p class="text-muted text-uppercase fs-12 fw-semibold mb-1">
                                                        <i class="ti ti-map-pin me-1"></i>{{ $business['city'] }}
                                                    </p>
                                                    <h4 class="mt-0 mb-1">{{ $business['name'] }}</h4>
                                                </div>
                                                <span class="badge bg-success-subtle text-success rounded-pill">Open</span>
                                            </div>
                                            <p class="text-muted mt-2 mb-1">{{ $business['description'] }}</p>
                                            <p class="mb-0 fw-medium">{{ $business['tagline'] }}</p>
                                            <div class="mt-3">
                                                <span class="text-primary fw-semibold">
                                                    View fleet and cars
                                                    <i class="ti ti-arrow-right ms-1"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

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
                            <p class="mb-0 text-end">Car Rental Marketplace</p>
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