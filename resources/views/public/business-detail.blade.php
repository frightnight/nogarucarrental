<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business['name'] }} - Car Rental | Nogaru Car Rental</title>

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
                                    <a href="{{ route('businesses.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="ti ti-arrow-left me-1"></i> Back to businesses
                                    </a>
                                </div>
                                <h4 class="page-title">{{ $business['name'] }}</h4>
                                <p class="text-muted mb-0">{{ $business['city'] }} &bull; {{ $business['tagline'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted mb-0">{{ $business['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $selectedIndex = request()->query('car');
                        $selectedCar = null;
                        if ($selectedIndex !== null && is_numeric($selectedIndex)) {
                            $idx = (int) $selectedIndex;
                            $selectedCar = $business['cars'][$idx] ?? null;
                        }
                    @endphp

                    @if($selectedCar)
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                            <div>
                                                <p class="text-muted text-uppercase fs-12 fw-semibold mb-1">Selected car</p>
                                                <h4 class="mt-0 mb-1">{{ $selectedCar['name'] }}</h4>
                                                <p class="text-muted mb-0">{{ $selectedCar['price'] ?? 'Price available on request' }} &bull; Ready for booking</p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ url(request()->path()) }}" class="btn btn-outline-secondary btn-sm">Clear selection</a>
<a href="{{ route('bookings.details') }}?car_id={{ $selectedCar['car_id'] ?? $selectedCar['id'] }}&business_id={{ $business['business_id'] ?? $business['id'] }}"
                                                    class="btn btn-primary btn-sm"
                                                >
                                                    Continue to booking
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-muted mb-0">Choose a vehicle from the list below. Your selection will be highlighted.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Car Cards -->
                    <div class="row">
                        @foreach ($business['cars'] as $carIndex => $car)
                            @php
                                $isSelected = $selectedCar && $selectedCar['name'] === $car['name'];
                                $carouselId = 'car-carousel-'.$carIndex;
                                $images = $car['images'] ?? [];
                            @endphp
                            <div class="col-xl-6">
                                <div class="card card-h-100 {{ $isSelected ? 'border-primary' : '' }}">
                                    <a href="{{ route('business.landing', ['businessSlug' => $business['slug']]) }}/?car={{ $carIndex }}" class="text-body text-decoration-none">

                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div>
                                                    <h4 class="mt-0 mb-1">{{ $car['name'] }}</h4>
                                                    <p class="text-muted mb-0">{{ $car['price'] ?? 'Price available on request' }}</p>
                                                </div>
                                                @if($isSelected)
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill">Selected</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Select</span>
                                                @endif
                                            </div>

                                            @if(count($images) > 0)
                                                <div class="mt-3 overflow-hidden rounded-2">
                                                    <div id="{{ $carouselId }}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                                                        <!-- Carousel indicators -->
                                                        @if(count($images) > 1)
                                                            <div class="carousel-indicators">
                                                                @foreach($images as $imgIndex => $image)
                                                                    <button type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide-to="{{ $imgIndex }}" class="{{ $imgIndex === 0 ? 'active' : '' }}" aria-label="Slide {{ $imgIndex + 1 }}"></button>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <!-- Carousel slides -->
                                                        <div class="carousel-inner">
                                                            @foreach($images as $imgIndex => $image)
                                                                <div class="carousel-item {{ $imgIndex === 0 ? 'active' : '' }}">
                                                                    <img src="{{ $image }}" alt="{{ $car['name'] }} - Image {{ $imgIndex + 1 }}" class="d-block w-100" style="max-height: 180px; width: 100%; object-fit: cover;">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Carousel controls -->
                                                        @if(count($images) > 1)
                                                            <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Previous</span>
                                                            </button>
                                                            <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Next</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="mt-3">
                                                <span class="text-primary fw-semibold">
                                                    {{ $isSelected ? 'Keep this vehicle' : 'Select this car' }}
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
                            <p class="mb-0 text-end">{{ $business['name'] }}</p>
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