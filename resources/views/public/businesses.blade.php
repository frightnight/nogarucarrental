<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a Car Rental Business | Nogaru</title>
    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}">
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
    <style>
        :root { --navy: #10243e; --blue: #276ef1; }
        body { color: var(--navy); }
        .brand { font-size: 1.5rem; letter-spacing: -.06em; }
        .section { padding: 88px 0; }
        .kicker { color: var(--blue); font-size: .75rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .section-title { font-size: clamp(2rem, 3vw, 3rem); letter-spacing: -.045em; }
        .company-card { border: 0; border-radius: 18px; box-shadow: 0 10px 30px rgba(16, 36, 62, .08); transition: transform .2s ease, box-shadow .2s ease; }
        .company-card:hover { box-shadow: 0 18px 38px rgba(16, 36, 62, .15); transform: translateY(-4px); }
        .company-mark { width: 54px; height: 54px; display: grid; place-items: center; background: #eaf1ff; border-radius: 14px; color: var(--blue); font-size: 1.25rem; font-weight: 800; }
        .footer { background: var(--navy); color: #fff; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top"><div class="container py-2"><a class="navbar-brand brand fw-bold" href="{{ route('home') }}"><i class="ti ti-steering-wheel text-primary"></i> Nogaru</a><button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><i class="ti ti-menu-2"></i></button><div class="collapse navbar-collapse" id="nav"><ul class="navbar-nav mx-auto"><li class="nav-item"><a class="nav-link" href="{{ route('home') }}#rentals">Rentals</a></li><li class="nav-item"><a class="nav-link" href="{{ route('home') }}#destinations">Destinations</a></li><li class="nav-item"><a class="nav-link" href="{{ route('home') }}#owners">For owners</a></li></ul>@auth<a class="btn btn-light" href="{{ route('dashboard') }}">Dashboard</a>@else<a class="btn btn-primary" href="{{ route('register') }}">Create account</a>@endauth</div></div></nav>
<main>
    <section class="section bg-light">
        <div class="container">
            <div class="row align-items-end g-4 mb-5">
                <div class="col-lg-8">
                    <p class="kicker mb-2">Trusted local partners</p>
                    <h1 class="section-title fw-bold mb-2">Choose a rental business</h1>
                    <p class="text-muted fs-16 mb-0">Browse trusted rental businesses, compare their fleets, and find the right vehicle for your trip.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('marketplace.index') }}" class="btn btn-primary"><i class="ti ti-search me-1"></i> Search all vehicles</a>
                </div>
            </div>

            <div class="row g-4">
                @forelse ($businesses as $business)
                    <div class="col-md-6 col-xl-4">
                        <a href="{{ route('business.landing', ['businessSlug' => $business->slug]) }}/" class="card company-card h-100 text-decoration-none text-body">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <span class="company-mark">{{ str($business->name)->substr(0, 1) }}</span>
                                    <div>
                                        <h2 class="h5 mb-1">{{ $business->name }}</h2>
                                        <p class="text-muted small mb-0"><i class="ti ti-map-pin me-1"></i>{{ $business->city ?: 'Philippines' }}</p>
                                    </div>
                                </div>
                                <p class="text-muted mb-4">{{ $business->description ?: 'A local rental company ready to help with your next journey.' }}</p>
                                <div class="mt-auto d-flex align-items-center justify-content-between border-top pt-3">
                                    <span class="small fw-semibold text-primary"><i class="ti ti-car me-1"></i>{{ $business->cars_count }} {{ str('vehicle')->plural($business->cars_count) }}</span>
                                    <span class="fw-semibold text-primary">View fleet <i class="ti ti-arrow-right ms-1"></i></span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12"><div class="card border-0"><div class="card-body text-center py-5"><h2 class="h4">Rental companies will appear here soon.</h2><p class="text-muted mb-0">Please check back shortly for available local partners.</p></div></div></div>
                @endforelse
            </div>
        </div>
    </section>
</main>
<footer class="footer py-5"><div class="container"><div class="row"><div class="col-lg-6"><h3 class="fw-bold">Nogaru</h3><p class="text-white-50">{{ $content['footer_text'] ?? 'Making local car rentals easier, one trip at a time.' }}</p></div><div class="col-lg-6 text-lg-end"><a class="btn btn-light text-primary" href="{{ route('marketplace.index') }}">Search vehicles</a></div></div><div class="border-top border-secondary pt-3 mt-4 small text-white-50">© {{ now()->year }} Nogaru. All rights reserved.</div></div></footer>
<script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/app.js') }}"></script>
</body>
</html>
