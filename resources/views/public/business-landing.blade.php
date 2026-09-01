<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business['name'] }} - Car Rental | Nogaru Car Rental</title>
    <meta name="description" content="{{ $business['description'] ?? 'Rent cars from '.$business['name'] }}" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}" />
    <!-- Theme Config Js -->
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>

    <!-- Vendor css -->
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .business-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 85vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .business-hero::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            top: -200px;
            right: -200px;
        }
        .business-hero::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(255,255,255,0.02);
            bottom: -100px;
            left: -100px;
        }
        .section-padding {
            padding: 80px 0;
        }
        .car-card-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        #landing-navbar {
            background: rgba(26,26,46,0.95);
            backdrop-filter: blur(10px);
            transition: background 0.3s ease;
        }
        #landing-navbar .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }
        #landing-navbar .nav-link:hover,
        #landing-navbar .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.1);
        }
        .page-footer {
            background: #1a1a2e;
            color: rgba(255,255,255,0.7);
        }
        .page-footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
        }
        .page-footer a:hover {
            color: #fff;
        }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- ====================== NAVBAR ====================== -->
    <header>
        <nav class="navbar navbar-expand-lg py-3 fixed-top" id="landing-navbar">
            <div class="container">
                <a class="navbar-brand fw-bold text-white fs-4" href="#hero">
                    {{ $business['name'] }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav fw-medium gap-2 fs-sm mx-auto mt-2 mt-lg-0" id="navbar-example">
                        <li class="nav-item">
                            <a class="nav-link active" href="#hero">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#cars">Browse Cars</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('businesses.index') }}" class="btn btn-sm btn-outline-light">Browse All</a>
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Sign In</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- ====================== HERO SECTION ====================== -->
    <section class="business-hero" id="hero">
        <div class="container position-relative z-1">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-7">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 fs-13 fw-semibold">
                        <i class="ti ti-map-pin me-1"></i> {{ $business['city'] ?? 'Your City' }}
                    </span>
                    <h1 class="display-4 fw-bold text-white mb-3 lh-base">
                        {{ $business['hero_title'] ?: $business['name'] }}
                    </h1>
                    <p class="lead text-white text-opacity-75 mb-4 fs-5 lh-lg">
                        {{ $business['hero_subtitle'] ?: $business['tagline'] }}
                    </p>
                    <p class="text-white text-opacity-50 mb-4 fs-sm">
                        {{ Str::limit($business['description'], 200) }}
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#cars" class="btn btn-primary btn-lg fw-semibold px-4">
                            <i class="ti ti-car me-2"></i>Browse Our Fleet
                        </a>
                        <a href="#contact" class="btn btn-outline-light btn-lg fw-semibold px-4">
                            <i class="ti ti-phone me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="p-4">
                        <div class="display-1 text-white text-opacity-10">
                            <i class="ti ti-car"></i>
                        </div>
                        <h2 class="text-white text-opacity-25 mt-3 fw-light">
                            Drive with Confidence
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====================== CARS / FLEET SECTION ====================== -->
    <section class="section-padding bg-light" id="cars">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2 fs-13">Our Fleet</span>
                <h2 class="fw-bold mb-2">Browse Our Vehicles</h2>
                <p class="text-muted fs-sm">Choose from our selection of quality vehicles for your next trip</p>
            </div>

            @php
                        $selectedCarId = request()->integer('car_id');
                        $selectedIndex = request()->query('car');
                        $selectedCar = null;
                        if ($selectedCarId) {
                            $selectedCar = collect($business['cars'])->firstWhere('car_id', $selectedCarId);
                        } elseif ($selectedIndex !== null && is_numeric($selectedIndex)) {
                    $idx = (int) $selectedIndex;
                    $selectedCar = $business['cars'][$idx] ?? null;
                }
            @endphp

            @if($selectedCar)
                <div class="alert alert-success d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4" role="alert">
                    <div>
                        <strong>{{ $selectedCar['name'] }}</strong> selected —
                        <span class="text-muted">{{ $selectedCar['price'] ?? 'Price available on request' }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                        <a href="{{ route('bookings.details') }}?car_id={{ $selectedCar['car_id'] ?? '' }}&business_id={{ $business['business_id'] ?? '' }}"
                           class="btn btn-sm btn-primary">Continue to Booking</a>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                @forelse ($business['cars'] as $carIndex => $car)
                    @php
                        $isSelected = $selectedCar && $selectedCar['name'] === $car['name'];
                        $images = $car['images'] ?? [];
                        $carLink = route('vehicles.show', $car['car_id']);
                    @endphp
                    <div class="col-lg-6 col-xl-4">
                        <x-vehicle-card :title="$car['name']" :image="$images[0] ?? null" :status="$car['status']" :seats="$car['seats']" :vehicle-type="$car['vehicle_type']" :transmission="$car['transmission']" :rental-type="$car['rental_type']" :price="$car['daily_rate'] ? '₱'.number_format((float) $car['daily_rate'], 0) : null" :price-label="$car['rate_label']" :secondary-price="$car['price'] ?? 'Contact us for pricing'" :href="$carLink" action-label="View Details" :selected="$isSelected" />
                        {{--
                            @if(count($images) > 0)
                                <div class="overflow-hidden rounded-top">
                                    <div id="car-{{ $carIndex }}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                                        @if(count($images) > 1)
                                            <div class="carousel-indicators">
                                                @foreach($images as $imgIndex => $image)
                                                    <button type="button" data-bs-target="#car-{{ $carIndex }}"
                                                            data-bs-slide-to="{{ $imgIndex }}" class="{{ $imgIndex === 0 ? 'active' : '' }}"
                                                            aria-label="Slide {{ $imgIndex + 1 }}"></button>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="carousel-inner">
                                            @foreach($images as $imgIndex => $image)
                                                <div class="carousel-item {{ $imgIndex === 0 ? 'active' : '' }}">
                                                    <img src="{{ $image }}" alt="{{ $car['name'] }}" class="car-card-img d-block">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if(count($images) > 1)
                                            <button class="carousel-control-prev" type="button" data-bs-target="#car-{{ $carIndex }}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#car-{{ $carIndex }}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center car-card-img">
                                    <i class="ti ti-car text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0">{{ $car['name'] }}</h5>
                                    @if($isSelected)
                                        <span class="badge bg-primary rounded-pill">Selected</span>
                                    @endif
                                </div>
                                <p class="text-muted fs-sm mb-3">{{ $car['price'] ?? 'Contact us for pricing' }}</p>
                                <div class="mt-auto">
                                    <a href="{{ $carLink }}" class="btn btn-outline-primary w-100 fw-semibold">
                                        {{ $isSelected ? 'Keep this vehicle' : 'Select this car' }}
                                        <i class="ti ti-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        --}}
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="ti ti-car-off text-muted" style="font-size: 3rem;"></i>
                            <h4 class="mt-3 text-muted">No vehicles available yet</h4>
                            <p class="text-muted fs-sm">Check back soon for our latest fleet</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ====================== ABOUT SECTION ====================== -->
    <section class="section-padding" id="about">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2 fs-13">About Us</span>
                <h2 class="fw-bold mb-2">{{ $business['about_title'] ?: 'Why Choose '.$business['name'].'?' }}</h2>
                <p class="text-muted fs-sm">We're committed to providing the best car rental experience</p>
            </div>

            <div class="row g-4 justify-content-center">
                @php $features = $business['about_features'] ?? []; @endphp
                @if(count($features) > 0)
                    @foreach($features as $feature)
                        @php
                            $feature = is_array($feature)
                                ? $feature
                                : ['title' => $feature, 'description' => '', 'icon' => 'star'];
                        @endphp
                        @if(($feature['title'] ?? '') || ($feature['description'] ?? ''))
                            <div class="col-md-6 col-lg-4">
                                <div class="card border-0 shadow-sm text-center h-100">
                                    <div class="card-body p-4">
                                        <div class="avatar-xl mx-auto mb-3">
                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                                                <i class="ti ti-{{ $feature['icon'] ?? 'star' }}"></i>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold">{{ $feature['title'] ?? '' }}</h5>
                                        <p class="text-muted fs-sm mb-0">{{ $feature['description'] ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <!-- Default features when none configured -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm text-center h-100">
                            <div class="card-body p-4">
                                <div class="avatar-xl mx-auto mb-3">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                                        <i class="ti ti-shield-check"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold">Reliable & Safe</h5>
                                <p class="text-muted fs-sm mb-0">All vehicles are regularly maintained and inspected to ensure your safety on the road.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm text-center h-100">
                            <div class="card-body p-4">
                                <div class="avatar-xl mx-auto mb-3">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                                        <i class="ti ti-coin"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold">Competitive Pricing</h5>
                                <p class="text-muted fs-sm mb-0">Get the best value with transparent pricing, no hidden fees, and flexible rental options.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm text-center h-100">
                            <div class="card-body p-4">
                                <div class="avatar-xl mx-auto mb-3">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                                        <i class="ti ti-headset"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold">Customer Support</h5>
                                <p class="text-muted fs-sm mb-0">Our dedicated team is here to help you every step of the way, from booking to return.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row mt-5">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 bg-light">
                        <div class="card-body p-4 p-lg-5">
                            <h4 class="fw-bold mb-3">About {{ $business['name'] }}</h4>
                            <p class="text-muted mb-0 lh-lg">
                                {{ $business['about_content'] ?: ($business['description'] ?: 'We are a trusted car rental provider dedicated to offering quality vehicles and exceptional service. Whether you need a car for business or leisure, we have the perfect vehicle for you.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====================== CONTACT SECTION ====================== -->
    <section class="section-padding bg-light" id="contact">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2 fs-13">Get in Touch</span>
                <h2 class="fw-bold mb-2">Contact {{ $business['name'] }}</h2>
                <p class="text-muted fs-sm">Have questions? Reach out to us and we'll get back to you</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-4">
                            <div class="avatar-xl mx-auto mb-3">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                                    <i class="ti ti-map-pin"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold">Location</h5>
                            <p class="text-muted mb-0">{{ $business['contact_address'] ?: $business['city'] ?: 'Your City' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-4">
                            <div class="avatar-xl mx-auto mb-3">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                                    <i class="ti ti-mail"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold">Email</h5>
                            <p class="text-muted mb-0">{{ $business['contact_email'] ?: 'contact@'.Str::slug($business['name']).'.car' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-4">
                            <div class="avatar-xl mx-auto mb-3">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                                    <i class="ti ti-phone"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold">Phone</h5>
                            <p class="text-muted mb-0">{{ $business['contact_phone'] ?: 'Contact us for direct inquiries' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-bold mb-4">Send us a message</h5>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Your Name" aria-label="Your Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" placeholder="Your Email" aria-label="Your Email">
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control" rows="4" placeholder="Your Message" aria-label="Your Message"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                                            <i class="ti ti-send me-2"></i>Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====================== FOOTER ====================== -->
    <footer class="page-footer py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0 fs-sm">{{ date('Y') }} &copy; {{ $business['name'] }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 fs-sm">
                        Part of <a href="{{ route('home') }}" class="fw-semibold">Nogaru Car Rental Platform</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Vendor js -->
    <script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('theme/assets/js/app.js') }}"></script>
</body>
</html>
