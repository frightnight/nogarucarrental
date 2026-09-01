<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Rental Car | Nogaru</title>
    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}">
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css">
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        :root { --navy: #10243e; --blue: #276ef1; }
        body { color: var(--navy); }
        .brand { font-size: 1.5rem; letter-spacing: -.06em; }
        .section { padding: 88px 0; }
        .kicker { color: var(--blue); font-size: .75rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .section-title { font-size: clamp(2rem, 3vw, 3rem); letter-spacing: -.045em; }
        .vehicle-img { height: 200px; object-fit: cover; background: #edf2f8; }
        .footer { background: var(--navy); color: #fff; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top"><div class="container py-2"><a class="navbar-brand brand fw-bold" href="{{ route('home') }}"><i class="ti ti-steering-wheel text-primary"></i> Nogaru</a><button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><i class="ti ti-menu-2"></i></button><div class="collapse navbar-collapse" id="nav"><ul class="navbar-nav mx-auto"><li class="nav-item"><a class="nav-link" href="{{ route('home') }}#rentals">Rentals</a></li><li class="nav-item"><a class="nav-link" href="{{ route('home') }}#destinations">Destinations</a></li><li class="nav-item"><a class="nav-link" href="{{ route('home') }}#owners">For owners</a></li></ul>@auth<a class="btn btn-light" href="{{ route('dashboard') }}">Dashboard</a>@else<a class="btn btn-primary" href="{{ route('register') }}">Create account</a>@endauth</div></div></nav>
<main>
    <section class="section bg-light">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <p class="kicker mb-2">Find your ride</p>
                    <h1 class="section-title fw-bold mb-2">Find the right rental car</h1>
                    <p class="text-muted mb-0">Compare vehicles from local rental companies and book with confidence.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('businesses.index') }}" class="btn btn-outline-secondary">Rental companies</a>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary">Sign in</a>
                    @endguest
                </div>
            </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('marketplace.index') }}" class="row g-3 align-items-end">
                            <div class="col-md-3"><label class="form-label" for="search">Vehicle</label><input id="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Make, model, or type"></div>
                            <div class="col-md-3"><label class="form-label" for="location">Location</label><input id="location" name="location" class="form-control" value="{{ $filters['location'] ?? '' }}" placeholder="e.g. Legazpi"></div>
                            <div class="col-md-2"><label class="form-label" for="vehicle_type">Vehicle type</label><select id="vehicle_type" name="vehicle_type" class="form-select"><option value="">Any type</option>@foreach($vehicleTypes as $type)<option value="{{ $type }}" @selected(($filters['vehicle_type'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></div>
                            <div class="col-md-2"><label class="form-label" for="transmission">Transmission</label><select id="transmission" name="transmission" class="form-select"><option value="">Any</option>@foreach(['Automatic', 'Manual', 'CVT'] as $type)<option value="{{ $type }}" @selected(($filters['transmission'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></div>
                            <div class="col-md-1"><label class="form-label" for="seats">Seats</label><input id="seats" name="seats" type="number" min="1" class="form-control" value="{{ $filters['seats'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label" for="pickup_date">Pickup</label><input id="pickup_date" name="pickup_date" type="date" class="form-control" value="{{ $filters['pickup_date'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label" for="return_date">Return</label><input id="return_date" name="return_date" type="date" class="form-control" value="{{ $filters['return_date'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label" for="max_price">Max price (₱)</label><input id="max_price" name="max_price" type="number" min="0" class="form-control" value="{{ $filters['max_price'] ?? '' }}"></div>
                            <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search vehicles</button></div>
                            <div class="col-md-2"><a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary w-100">Clear filters</a></div>
                        </form>
                    </div>
                </div>

                <p class="text-muted">{{ $cars->count() }} vehicle{{ $cars->count() === 1 ? '' : 's' }} found</p>
                <div class="row">
                    @forelse($cars as $car)
                        @php($dailyRate = $car->rates->whereIn('name', ['24hrs', 'Daily'])->min('value') ?? $car->rates->min('value'))
                        <div class="col-md-6 col-xl-4">
                            <x-vehicle-card :title="$car->car_model ?: $car->vehicle_type" :image="$car->images->first()?->image_path" :status="$car->status" :seats="$car->seats" :vehicle-type="$car->vehicle_type" :transmission="$car->transmission" :rental-type="$car->rental_type" :price="$dailyRate !== null ? '₱'.number_format((float) $dailyRate, 0) : null" :price-label="$car->rates->firstWhere('name', '24hrs') ? '24hrs' : 'day'" :secondary-price="$car->business->name.($car->business->city ? ' · '.$car->business->city : '')" :href="route('vehicles.show', $car)" />
                        </div>
                    @empty
                        <div class="col-12"><div class="card"><div class="card-body text-center py-5"><h5>No vehicles match those filters.</h5><p class="text-muted mb-0">Try broader dates, a different location, or remove a filter.</p></div></div></div>
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
