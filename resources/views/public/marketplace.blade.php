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
</head>
<body>
<div class="wrapper">
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="page-title-box">
                    <div class="page-title-right d-flex gap-2">
                        <a href="{{ route('businesses.index') }}" class="btn btn-outline-secondary btn-sm">Rental companies</a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Sign in</a>
                        @endguest
                    </div>
                    <h4 class="page-title">Find the right rental car</h4>
                    <p class="text-muted mb-0">Compare vehicles from local rental companies and book with confidence.</p>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('marketplace.index') }}" class="row g-3 align-items-end">
                            <div class="col-md-3"><label class="form-label" for="location">Location</label><input id="location" name="location" class="form-control" value="{{ $filters['location'] ?? '' }}" placeholder="e.g. Legazpi"></div>
                            <div class="col-md-2"><label class="form-label" for="vehicle_type">Vehicle type</label><select id="vehicle_type" name="vehicle_type" class="form-select"><option value="">Any type</option>@foreach($vehicleTypes as $type)<option value="{{ $type }}" @selected(($filters['vehicle_type'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></div>
                            <div class="col-md-2"><label class="form-label" for="transmission">Transmission</label><select id="transmission" name="transmission" class="form-select"><option value="">Any</option>@foreach(['Automatic', 'Manual', 'CVT'] as $type)<option value="{{ $type }}" @selected(($filters['transmission'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></div>
                            <div class="col-md-1"><label class="form-label" for="seats">Seats</label><input id="seats" name="seats" type="number" min="1" class="form-control" value="{{ $filters['seats'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label" for="pickup_date">Pickup</label><input id="pickup_date" name="pickup_date" type="date" class="form-control" value="{{ $filters['pickup_date'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label" for="return_date">Return</label><input id="return_date" name="return_date" type="date" class="form-control" value="{{ $filters['return_date'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label" for="max_price">Max price (₱)</label><input id="max_price" name="max_price" type="number" min="0" class="form-control" value="{{ $filters['max_price'] ?? '' }}"></div>
                            <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search vehicles</button></div>
                        </form>
                    </div>
                </div>

                <p class="text-muted">{{ $cars->count() }} vehicle{{ $cars->count() === 1 ? '' : 's' }} found</p>
                <div class="row">
                    @forelse($cars as $car)
                        @php($dailyRate = $car->rates->whereIn('name', ['24hrs', 'Daily'])->min('value') ?? $car->rates->min('value'))
                        <div class="col-md-6 col-xl-4">
                            <div class="card card-h-100">
                                @if($car->images->first())<img src="{{ $car->images->first()->image_path }}" class="card-img-top" alt="{{ $car->car_model }}" style="height: 180px; object-fit: cover;">@endif
                                <div class="card-body d-flex flex-column">
                                    <p class="text-muted text-uppercase fs-12 fw-semibold mb-1">{{ $car->business->name }} · {{ $car->business->city }}</p>
                                    <h4>{{ $car->car_model ?: $car->vehicle_type }}</h4>
                                    <p class="text-muted mb-3">{{ collect([$car->vehicle_type, $car->transmission, $car->seats ? $car->seats.' seats' : null])->filter()->implode(' · ') }}</p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center"><span class="fw-bold text-primary">{{ $dailyRate !== null ? '₱'.number_format((float) $dailyRate, 0).'/day' : 'Price on request' }}</span><a href="{{ route('business.landing', $car->business->slug) }}/?car_id={{ $car->id }}" class="btn btn-outline-primary btn-sm">View vehicle</a></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><div class="card"><div class="card-body text-center py-5"><h5>No vehicles match those filters.</h5><p class="text-muted mb-0">Try broader dates, a different location, or remove a filter.</p></div></div></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/app.js') }}"></script>
</body>
</html>
