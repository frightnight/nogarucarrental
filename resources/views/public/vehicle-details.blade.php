<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $car->car_model ?: $car->vehicle_type }} | Nogaru</title>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <style>
        :root { --vehicle-navy: #152d63; --vehicle-text: #16213a; }
        body { background: #fbfcfe; color: var(--vehicle-text); }
        .vehicle-detail-card { border: 1px solid #edf0f5; border-radius: 18px; box-shadow: 0 4px 18px rgba(25, 42, 70, .05); }
        .vehicle-detail-image { aspect-ratio: 1.35 / 1; border-radius: 15px; object-fit: cover; width: 100%; }
        .vehicle-spec { align-items: center; background: #f8f9fb; border-radius: 14px; display: flex; gap: .85rem; min-height: 76px; padding: 1rem; }
        .vehicle-spec__icon { font-size: 1.3rem; }
        .vehicle-spec__label { color: #7b8497; display: block; font-size: .8rem; line-height: 1.2; }
        .vehicle-spec__value { display: block; font-weight: 700; line-height: 1.35; }
        .ride-option { border: 1px solid #cbd6ea; border-radius: 13px; cursor: pointer; display: block; padding: .8rem; }
        .ride-option input { position: absolute; opacity: 0; }
        .ride-option:has(input:checked) { background: var(--vehicle-navy); border-color: var(--vehicle-navy); color: #fff; }
        .ride-option:has(input:checked) .text-muted { color: #d8e2fa !important; }
        .quick-estimate .form-control { border-radius: 10px; }
        .availability-note { color: #069448; font-size: .85rem; }
        .estimate-map { background: #f3f5f9; border-radius: 12px; height: 180px; overflow: hidden; }
        .estimate-summary { background: #eef3fb; border-radius: 12px; font-size: .82rem; padding: .85rem; }
        .location-picker-map { height: min(55vh, 440px); width: 100%; }
        .location-picker-address { background: #f5f7fb; border-radius: .65rem; min-height: 3.25rem; padding: .75rem; }
        .location-picker-map.leaflet-container, .estimate-map.leaflet-container { font: inherit; position: relative; }
        .exact-location-pin { align-items: center; background: #d92d20; border: 3px solid #fff; border-radius: 50% 50% 50% 0; box-shadow: 0 2px 8px rgba(0, 0, 0, .35); color: #fff; display: flex; height: 34px; justify-content: center; transform: rotate(-45deg); width: 34px; }
        .exact-location-pin i { font-size: 1rem; transform: rotate(45deg); }
        .self-drive-map { background: #f3f5f9; border-radius: 12px; height: 220px; overflow: hidden; }
    </style>
</head>
<body>
<main class="container py-4 py-lg-5">
    <a href="{{ url()->previous() }}" class="btn btn-link px-0 text-decoration-none mb-3"><i class="ti ti-arrow-left me-1"></i>Back to vehicles</a>
    <div class="row g-4 align-items-start">
        <section class="col-lg-8">
            <article class="vehicle-detail-card bg-white p-4 p-lg-4">
                <h1 class="h2 fw-bold mb-4">{{ $car->car_model ?: $car->vehicle_type }}</h1>
                <div class="row g-4">
                    <div class="col-md-6">
                        @if($car->images->first())
                            <img src="{{ $car->images->first()->image_path }}" alt="{{ $car->car_model ?: $car->vehicle_type }}" class="vehicle-detail-image">
                        @else
                            <div class="vehicle-detail-image bg-light d-flex align-items-center justify-content-center text-muted"><i class="ti ti-car" style="font-size: 4rem;"></i></div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-user text-primary vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Seats</span><span class="vehicle-spec__value">{{ $car->seats ?: '—' }} Passengers</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-briefcase text-purple vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Luggage</span><span class="vehicle-spec__value">{{ $car->seats ? max(1, intdiv($car->seats, 2)) : '—' }} Bags</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-settings text-secondary vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Transmission</span><span class="vehicle-spec__value">{{ $car->transmission ?: '—' }}</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-gas-station text-warning vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Fuel</span><span class="vehicle-spec__value">{{ $car->fuel_type ?: '—' }}</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-car text-success vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Vehicle Type</span><span class="vehicle-spec__value">{{ $car->vehicle_type ?: '—' }}</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-currency-peso text-primary vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Starts at</span><span id="vehicle-starting-price" class="vehicle-spec__value">{{ $twentyFourHourRate ? '₱'.number_format($twentyFourHourRate, 0).'/day' : 'On request' }}</span>@if($twelveHourRate)<small class="vehicle-spec__label">12 hrs from ₱{{ number_format($twelveHourRate, 0) }}</small>@endif</span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-clock-dollar text-primary vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Extension</span><span class="vehicle-spec__value">{{ $extensionPerHourRate ? '₱'.number_format($extensionPerHourRate, 0).'/hour' : 'On request' }}</span></span></div></div>
                        </div>
                    </div>
                </div>
                <section class="mt-4">
                    <h2 class="h6 fw-bold">Description</h2>
                    <p class="text-muted mb-0">Enjoy a comfortable, dependable ride from {{ $car->business->name }}. Contact the business for pickup arrangements and any additional vehicle details.</p>
                </section>
            </article>
            <article id="self-drive-computation" class="vehicle-detail-card bg-white p-4 mt-4 d-none">
                <h2 class="h5 fw-bold mb-3">Estimated Total Computation</h2>
                <div class="row g-3 small">
                    <div class="col-md-6"><span class="text-muted d-block">Base Rental</span><strong id="computation-base">—</strong></div>
                    <div class="col-md-6"><span class="text-muted d-block">Delivery Fee</span><strong id="computation-delivery">—</strong></div>
                    <div class="col-md-6"><span class="text-muted d-block">Return Fee</span><strong id="computation-return">—</strong></div>
                    <div class="col-md-6"><span class="text-muted d-block">Car Wash Fee</span><strong id="computation-wash">—</strong></div>
                    <div class="col-md-6"><span class="text-muted d-block">VAT (12%)</span><strong id="computation-vat">—</strong></div>
                    <div class="col-md-6"><span class="text-muted d-block">Estimated Total</span><strong id="computation-total" class="text-primary">—</strong></div>
                </div>
            </article>
        </section>
        <aside class="col-lg-4">
            <section class="vehicle-detail-card quick-estimate bg-white p-4">
                <h2 class="h5 fw-bold mb-4">Quick Estimate</h2>
                @if($errors->any())<div class="alert alert-danger small py-2">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
                <form method="POST" action="{{ route('guest-checkout.store', $car) }}" id="ride-form">
                    @csrf
                    <p class="small text-muted mb-2">How would you like to ride?</p>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="ride-option"><input type="radio" name="ride_type" value="self_drive" @checked(old('ride_type', 'self_drive') === 'self_drive')><i class="ti ti-car me-1"></i><strong>Self-Drive</strong><span class="d-block small text-muted">You're in control</span></label></div>
                        <div class="col-6"><label class="ride-option"><input type="radio" name="ride_type" value="with_driver" @checked(old('ride_type') === 'with_driver')><i class="ti ti-user me-1"></i><strong>With Driver</strong><span class="d-block small text-muted">Sit back &amp; relax</span></label></div>
                    </div>

                    <div id="self-drive-panel">
                        <div class="mb-2"><label for="self-drive-start-at" class="form-label small">Start Date &amp; Time</label><input id="self-drive-start-at" type="datetime-local" name="self_drive_start_at" class="form-control"></div>
                        <div class="mb-3"><label for="self-drive-end-at" class="form-label small">End Date &amp; Time</label><input id="self-drive-end-at" type="datetime-local" name="self_drive_end_at" class="form-control"></div>
                        <div class="mb-2"><label for="self-drive-delivery-location" class="form-label small">Delivery Location</label><div class="input-group"><input id="self-drive-delivery-location" name="self_drive_delivery_location" class="form-control" value="Pick-up to Garage" readonly><button class="btn btn-outline-primary self-drive-location-button" type="button" data-location="delivery">Set on map</button><button class="btn btn-outline-danger self-drive-location-reset" type="button" data-location="delivery" aria-label="Reset delivery location">×</button></div><input id="self-drive-delivery-latitude" type="hidden" name="self_drive_delivery_latitude"><input id="self-drive-delivery-longitude" type="hidden" name="self_drive_delivery_longitude"></div>
                        <div class="mb-2"><label for="self-drive-return-location" class="form-label small">Return Location</label><div class="input-group"><input id="self-drive-return-location" name="self_drive_return_location" class="form-control" value="Return to Garage" readonly><button class="btn btn-outline-primary self-drive-location-button" type="button" data-location="return">Set on map</button><button class="btn btn-outline-danger self-drive-location-reset" type="button" data-location="return" aria-label="Reset return location">×</button></div><input id="self-drive-return-latitude" type="hidden" name="self_drive_return_latitude"><input id="self-drive-return-longitude" type="hidden" name="self_drive_return_longitude"></div>
                        <input id="self-drive-delivery-return-fee" type="hidden" name="self_drive_delivery_return_fee" value="0"><input id="self-drive-delivery-return-distance" type="hidden" name="self_drive_delivery_return_distance" value="0"><div id="self-drive-map" class="self-drive-map mb-3"></div><p class="small text-muted mb-3">The map displays Delivery (blue) and Return (green). Use “Set on map” to select a location. “Pick-up to Garage” has no fee.</p>
                        <p class="availability-note mb-3"><i class="ti ti-check me-1"></i>Available for selected dates</p>
                        <div class="estimate-summary mb-3"><div class="d-flex justify-content-between"><span id="self-drive-base-label">Base</span><strong id="self-drive-base">—</strong></div><div class="d-flex justify-content-between mt-1"><span id="self-drive-delivery-fee-label">Delivery Fee</span><strong id="self-drive-delivery-fee-display">₱0.00</strong></div><div class="d-flex justify-content-between mt-1"><span id="self-drive-return-fee-label">Return Fee</span><strong id="self-drive-return-fee-display">₱0.00</strong></div><div class="d-flex justify-content-between mt-1"><span>Car Wash Fee</span><strong id="self-drive-wash">—</strong></div><div class="d-flex justify-content-between mt-1"><span>VAT (12%)</span><strong id="self-drive-vat">—</strong></div><hr class="my-2"><div class="d-flex justify-content-between fw-bold"><span>Estimated Total</span><strong id="self-drive-total">—</strong></div></div>
                        @auth
                            @if(auth()->user()->hasRole('client'))
                                <button id="self-drive-book-button" class="btn btn-primary w-100" type="submit">Book Now</button>
                            @else
                                <p class="small text-muted text-center mb-0">Self-drive bookings require a client account.</p>
                            @endif
                        @else
                            <p class="small text-muted text-center">Self-drive booking requires an account.</p>
                            <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-primary w-100"><i class="ti ti-brand-google me-1"></i>Continue with Google</a>
                            <p class="small text-muted text-center my-3">Already have an account? <a href="{{ route('login') }}">Sign in</a> · <a href="{{ route('register') }}">Register</a></p>
                        @endauth
                    </div>

                    <div id="with-driver-panel" class="d-none">
                        <p class="small text-muted mb-2">How long do you need the car?</p>
                        <div class="row g-2 mb-3"><div class="col-6"><label class="ride-option"><input type="radio" name="trip_length" value="dropoff" @checked(old('trip_length', 'dropoff') === 'dropoff')><strong>Just a drop-off</strong><span class="d-block small text-muted">by distance</span></label></div><div class="col-6"><label class="ride-option"><input type="radio" name="trip_length" value="daily" @checked(old('trip_length') === 'daily')><strong>Keep the car</strong><span class="d-block small text-muted">Daily rate</span></label></div></div>
                        <div class="mb-2"><label for="pickup-at" class="form-label small">Pick-up Date &amp; Time</label><input id="pickup-at" type="datetime-local" name="pickup_at" class="form-control" value="{{ old('pickup_at') }}" disabled></div>
                        <div id="daily-rental-fields" class="d-none"><div class="mb-3"><label for="return-at" class="form-label small">Return Date &amp; Time</label><input id="return-at" type="datetime-local" name="return_at" class="form-control" value="{{ old('return_at') }}" disabled></div></div>
                        <div id="dropoff-fields"><div class="mb-2"><label for="pickup-location" class="form-label small">Pick-up Location</label><input id="pickup-location" name="pickup_location" class="form-control location-picker-input" placeholder="Pin your pickup location on the map" value="{{ old('pickup_location') }}" autocomplete="off" readonly disabled></div><div class="mb-3"><label for="dropoff-location" class="form-label small">Drop-off Location</label><input id="dropoff-location" name="dropoff_location" class="form-control location-picker-input" placeholder="Pin your drop-off location on the map" value="{{ old('dropoff_location') }}" autocomplete="off" readonly disabled></div><input id="pickup-latitude" type="hidden" name="pickup_latitude" disabled><input id="pickup-longitude" type="hidden" name="pickup_longitude" disabled><input id="dropoff-latitude" type="hidden" name="dropoff_latitude" disabled><input id="dropoff-longitude" type="hidden" name="dropoff_longitude" disabled><input id="total-distance-km" type="hidden" name="total_distance_km" disabled><div id="estimate-map" class="estimate-map mb-3"></div></div>
                        <div id="dropoff-summary" class="estimate-summary mb-3"><div class="d-flex justify-content-between"><span>Distance</span><strong id="distance-display">Choose both addresses</strong></div><div class="d-flex justify-content-between mt-1"><span>Travel time</span><strong id="duration-display">—</strong></div><div class="d-flex justify-content-between mt-1"><span>Pick-up &amp; Drop-off</span><strong id="pickup-dropoff-fee">₱0.00</strong></div><div class="d-flex justify-content-between mt-1"><span>VAT (12%)</span><strong id="dropoff-vat">₱0.00</strong></div><hr class="my-2"><div class="d-flex justify-content-between fw-bold"><span>Estimated Total</span><strong id="estimate-total">{{ $pickupDropoffFee ? '₱'.number_format($pickupDropoffFee * 1.12, 2) : 'To be confirmed' }}</strong></div><div class="d-flex justify-content-between mt-1 text-primary fw-semibold"><span>Downpayment due now</span><strong id="downpayment-due">{{ $pickupDropoffFee ? '₱'.number_format($pickupDropoffFee * 1.12 * .2, 2) : 'To be confirmed' }}</strong></div></div>
                        <div id="daily-summary" class="estimate-summary mb-3 d-none"><div class="d-flex justify-content-between"><span id="daily-base-label">Base</span><strong id="daily-base">—</strong></div><div class="d-flex justify-content-between mt-1"><span id="daily-driver-label">Driver</span><strong id="daily-driver">—</strong></div><div class="d-flex justify-content-between mt-1"><span>Car Wash Fee</span><strong id="daily-wash">—</strong></div><div class="d-flex justify-content-between mt-1 text-success"><span>Discount</span><strong id="daily-discount">—</strong></div><div class="d-flex justify-content-between mt-1"><span>VAT (12%)</span><strong id="daily-vat">—</strong></div><hr class="my-2"><div class="d-flex justify-content-between fw-bold"><span>Estimated Total</span><strong id="daily-total">—</strong></div></div>
                        <p class="availability-note mb-3"><i class="ti ti-check me-1"></i>Continue to payment without an account.</p>
                        <button class="btn btn-primary w-100" type="submit">Book Now</button>
                    </div>
                </form>
            </section>
        </aside>
    </div>
</main>
    <div class="modal fade" id="location-picker-modal" tabindex="-1" aria-labelledby="location-picker-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content border-0 shadow">
            <div class="modal-header"><h2 id="location-picker-title" class="modal-title fs-5">Choose location</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body"><p class="small text-muted">Search for an address, click the map, or drag the pin to select an exact location.</p><label for="location-search" class="visually-hidden">Search address</label><input id="location-search" class="form-control mb-2" placeholder="Search address"><div id="location-search-results" class="list-group mb-3"></div><div id="location-picker-map" class="location-picker-map rounded overflow-hidden"></div><div class="mt-3"><span class="small text-muted d-block mb-1">Selected address</span><div id="location-picker-address" class="location-picker-address">Choose a point on the map.</div></div></div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button id="confirm-location-button" type="button" class="btn btn-primary" disabled>Confirm location</button></div>
        </div></div>
    </div>
    <div class="modal fade" id="self-drive-location-modal" tabindex="-1" aria-labelledby="self-drive-location-title" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 id="self-drive-location-title" class="modal-title fs-5">Set location</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="small text-muted">Click the map to place the pin.</p><div id="self-drive-location-picker-map" class="location-picker-map rounded overflow-hidden"></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button id="confirm-self-drive-location" type="button" class="btn btn-primary" disabled>Use this location</button></div></div></div></div>
<script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
<script>
    const rideInputs = document.querySelectorAll('input[name="ride_type"]');
    const selfDrivePanel = document.getElementById('self-drive-panel');
    const withDriverPanel = document.getElementById('with-driver-panel');
    const withDriverInputs = withDriverPanel.querySelectorAll('input:not([name="trip_length"])');
    const tripLengthInputs = document.querySelectorAll('input[name="trip_length"]');
    const dailyRentalFields = document.getElementById('daily-rental-fields');
    const dropoffFields = document.getElementById('dropoff-fields');
    const dailySummary = document.getElementById('daily-summary');
    const dropoffSummary = document.getElementById('dropoff-summary');
    const selfDriveStartAt = document.getElementById('self-drive-start-at');
    const selfDriveEndAt = document.getElementById('self-drive-end-at');
    const selfDriveInputs = selfDrivePanel.querySelectorAll('input');

    function toggleRideType() {
        const withDriver = document.querySelector('input[name="ride_type"]:checked').value === 'with_driver';
        selfDrivePanel.classList.toggle('d-none', withDriver);
        withDriverPanel.classList.toggle('d-none', !withDriver);
        selfDriveInputs.forEach((input) => input.disabled = withDriver);
        withDriverInputs.forEach((input) => input.disabled = !withDriver);
        toggleTripLength();
    }

    rideInputs.forEach((input) => input.addEventListener('change', toggleRideType));

    function toggleTripLength() {
        const isDaily = document.querySelector('input[name="trip_length"]:checked').value === 'daily';
        dailyRentalFields.classList.toggle('d-none', !isDaily);
        dropoffFields.classList.toggle('d-none', isDaily);
        dailySummary.classList.toggle('d-none', !isDaily);
        dropoffSummary.classList.toggle('d-none', isDaily);
        document.getElementById('return-at').disabled = !isDaily;
        document.getElementById('pickup-at').required = !withDriverPanel.classList.contains('d-none');
        document.getElementById('return-at').required = isDaily;
        dropoffFields.querySelectorAll('input').forEach((input) => input.disabled = isDaily || withDriverPanel.classList.contains('d-none'));
        document.getElementById('pickup-location').required = !isDaily && !withDriverPanel.classList.contains('d-none');
        document.getElementById('dropoff-location').required = !isDaily && !withDriverPanel.classList.contains('d-none');
        if (!isDaily && typeof refreshEstimateMap === 'function') refreshEstimateMap();
        if (typeof updateDailyEstimate === 'function') updateDailyEstimate();
    }

    tripLengthInputs.forEach((input) => input.addEventListener('change', toggleTripLength));
    toggleRideType();
    toggleTripLength();
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let estimateMap;
    let estimateRoute;
    let locationPickerMap;
    let locationPickerMarker;
    let selectedLocation;
    let selectedLocationInput;
    const defaultLocation = [13.1391, 123.7438];
    const twelveHourRate = {{ $twelveHourRate }};
    const twentyFourHourRate = {{ $twentyFourHourRate }};
    const baseRate = twentyFourHourRate;
    const driverDailyRate = {{ $driverDailyRate }};
    const carWashFee = {{ $carWashFee }};
    const dailyDiscount = {{ $dailyDiscount }};
    const pickupDropoffFee = {{ $pickupDropoffFee }};
    const fuelConsumptionKmPerLiter = {{ $fuelConsumptionKmPerLiter }};
    const fuelPricePerLiter = {{ $fuelPricePerLiter }};
    const garageLocation = { lat: {{ $garageLatitude }}, lng: {{ $garageLongitude }} };
    const formatPeso = (amount) => `₱${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    function toDateTimeLocalValue(date) {
        const offsetDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);

        return offsetDate.toISOString().slice(0, 16);
    }

    function updateSelfDriveEstimate() {
        const startAt = selfDriveStartAt.value;
        const endAt = selfDriveEndAt.value;
        const baseLabel = document.getElementById('self-drive-base-label');
        const baseElement = document.getElementById('self-drive-base');
        const washElement = document.getElementById('self-drive-wash');
        const deliveryReturnFee = Number(document.getElementById('self-drive-delivery-return-fee').value || 0);
        const vatElement = document.getElementById('self-drive-vat');
        const totalElement = document.getElementById('self-drive-total');
        const computation = document.getElementById('self-drive-computation');

        if (!startAt || !endAt || new Date(endAt) <= new Date(startAt)) {
            baseLabel.textContent = 'Base';
            [baseElement, washElement, vatElement, totalElement].forEach((element) => element.textContent = '—');
            computation.classList.add('d-none');
            return;
        }

        const rental = calculateVehicleRentalRate(startAt, endAt);
        const base = rental.amount;
        const vat = (base + deliveryReturnFee + carWashFee) * .12;
        const total = base + deliveryReturnFee + carWashFee + vat;

        baseLabel.textContent = `Base (${rental.label})`;
        baseElement.textContent = formatPeso(base);
        document.getElementById('self-drive-delivery-return-fee-display').textContent = formatPeso(deliveryReturnFee);
        washElement.textContent = formatPeso(carWashFee);
        vatElement.textContent = formatPeso(vat);
        totalElement.textContent = formatPeso(total);
        computation.classList.remove('d-none');
        document.getElementById('computation-base').textContent = baseLabel.textContent.replace('Base ', '')+' = '+formatPeso(base);
        const deliveryComputation = document.getElementById('computation-delivery');
        const returnComputation = document.getElementById('computation-return');
        deliveryComputation.textContent = `${deliveryComputation.dataset.breakdown ?? 'Fuel: ₱0.00 + Distance: ₱0.00'} = ${document.getElementById('self-drive-delivery-fee-display').textContent}`;
        returnComputation.textContent = `${returnComputation.dataset.breakdown ?? 'Fuel: ₱0.00 + Distance: ₱0.00'} = ${document.getElementById('self-drive-return-fee-display').textContent}`;
        document.getElementById('computation-wash').textContent = formatPeso(carWashFee);
        document.getElementById('computation-vat').textContent = formatPeso(vat);
        document.getElementById('computation-total').textContent = formatPeso(total);

    }

    const minimumSelfDriveStart = new Date();
    minimumSelfDriveStart.setMinutes(Math.ceil(minimumSelfDriveStart.getMinutes() / 15) * 15, 0, 0);
    selfDriveStartAt.min = toDateTimeLocalValue(minimumSelfDriveStart);
    selfDriveStartAt.addEventListener('change', () => {
        selfDriveEndAt.min = selfDriveStartAt.value;
        updateSelfDriveEstimate();
    });
    selfDriveEndAt.addEventListener('change', updateSelfDriveEstimate);
    updateSelfDriveEstimate();

    let selfDriveMap;
    let selfDriveDeliveryMarker;
    let selfDriveReturnMarker;
    let selfDriveLocationPickerMap;
    let selfDriveLocationPickerMarker;
    let selfDriveLocationTarget;
    let selfDriveSelectedLocation;
    let selfDriveDeliveryIcon;
    let selfDriveReturnIcon;

    async function updateSelfDriveDeliveryReturnFee() {
        const deliveryLocation = document.getElementById('self-drive-delivery-location');
        const returnLocation = document.getElementById('self-drive-return-location');
        const deliveryIsGarage = deliveryLocation.value.trim().toLowerCase() === 'pick-up to garage';
        const returnIsGarage = returnLocation.value.trim().toLowerCase() === 'return to garage';
        const deliveryCoordinates = selfDriveDeliveryMarker?.getLatLng();
        const returnCoordinates = selfDriveReturnMarker?.getLatLng();
        let deliveryDistanceKm = 0;
        let returnDistanceKm = 0;

        if ((!deliveryIsGarage && !deliveryCoordinates) || (!returnIsGarage && !returnCoordinates)) {
            return;
        }

        try {
            const getDistance = async (coordinates) => {
                const response = await fetch(`https://router.project-osrm.org/route/v1/driving/${coordinates}?overview=false`);
                const route = response.ok ? (await response.json()).routes?.[0] : null;
                return (route?.distance ?? 0) / 1000;
            };
            if (!deliveryIsGarage) deliveryDistanceKm = await getDistance(`${garageLocation.lng},${garageLocation.lat};${deliveryCoordinates.lng},${deliveryCoordinates.lat}`);
            if (!returnIsGarage) returnDistanceKm = await getDistance(`${returnCoordinates.lng},${returnCoordinates.lat};${garageLocation.lng},${garageLocation.lat}`);
        } catch (error) {
            deliveryDistanceKm = 0;
            returnDistanceKm = 0;
        }

        const distanceRate = (distanceKm) => distanceKm > 0 ? 14 + Math.max(0, Math.ceil(distanceKm) - 4) * 2 : 0;
        const fuelRate = (distanceKm) => fuelConsumptionKmPerLiter > 0 ? (distanceKm / fuelConsumptionKmPerLiter) * fuelPricePerLiter : 0;
        const deliveryRateA = fuelRate(deliveryDistanceKm);
        const deliveryRateB = distanceRate(deliveryDistanceKm);
        const returnRateA = fuelRate(returnDistanceKm);
        const returnRateB = distanceRate(returnDistanceKm);
        const roundUpToFifty = (amount) => amount > 0 ? Math.ceil(amount / 50) * 50 : 0;
        const deliveryFee = roundUpToFifty(deliveryRateA + deliveryRateB);
        const returnFee = roundUpToFifty(returnRateA + returnRateB);
        document.getElementById('self-drive-delivery-return-distance').value = (deliveryDistanceKm + returnDistanceKm).toFixed(2);
        document.getElementById('self-drive-delivery-return-fee').value = (deliveryFee + returnFee).toFixed(2);
        document.getElementById('self-drive-delivery-fee-label').textContent = `Delivery Fee (${deliveryDistanceKm.toFixed(1)} km)`;
        document.getElementById('self-drive-return-fee-label').textContent = `Return Fee (${returnDistanceKm.toFixed(1)} km)`;
        document.getElementById('self-drive-delivery-fee-display').textContent = formatPeso(deliveryFee);
        document.getElementById('self-drive-return-fee-display').textContent = formatPeso(returnFee);
        document.getElementById('computation-delivery').dataset.breakdown = `Fuel: ${formatPeso(deliveryRateA)} + Distance: ${formatPeso(deliveryRateB)} (rounded up to ₱50)`;
        document.getElementById('computation-return').dataset.breakdown = `Fuel: ${formatPeso(returnRateA)} + Distance: ${formatPeso(returnRateB)} (rounded up to ₱50)`;
        updateSelfDriveEstimate();
    }

    function initializeSelfDriveMap() {
        const element = document.getElementById('self-drive-map');
        if (!element || !Number.isFinite(garageLocation.lat) || !Number.isFinite(garageLocation.lng) || garageLocation.lat === 0 || garageLocation.lng === 0) return;
        selfDriveMap = L.map(element).setView([garageLocation.lat, garageLocation.lng], 12);
        addOpenStreetMapTiles(selfDriveMap);
        const markerIcon = (color) => L.divIcon({ className: '', html: `<span style="background:${color};border:3px solid #fff;border-radius:50% 50% 50% 0;box-shadow:0 2px 8px rgba(0,0,0,.35);display:block;height:28px;transform:rotate(-45deg);width:28px"></span>`, iconSize: [28, 28], iconAnchor: [14, 28] });
        selfDriveDeliveryIcon = markerIcon('#2563eb');
        selfDriveReturnIcon = markerIcon('#16a34a');
        selfDriveLocationPickerMap = L.map('self-drive-location-picker-map').setView([garageLocation.lat, garageLocation.lng], 12);
        addOpenStreetMapTiles(selfDriveLocationPickerMap);
        selfDriveLocationPickerMarker = L.marker([garageLocation.lat, garageLocation.lng], { draggable: true }).addTo(selfDriveLocationPickerMap);
        const selectLocation = (location) => { selfDriveSelectedLocation = location; selfDriveLocationPickerMarker.setLatLng(location); document.getElementById('confirm-self-drive-location').disabled = false; };
        selfDriveLocationPickerMap.on('click', (event) => selectLocation(event.latlng));
        selfDriveLocationPickerMarker.on('dragend', (event) => selectLocation(event.target.getLatLng()));
        document.querySelectorAll('.self-drive-location-button').forEach((button) => button.addEventListener('click', () => {
            selfDriveLocationTarget = button.dataset.location;
            document.getElementById('self-drive-location-title').textContent = `Set ${selfDriveLocationTarget === 'delivery' ? 'Delivery' : 'Return'} Location`;
            selfDriveSelectedLocation = null;
            document.getElementById('confirm-self-drive-location').disabled = true;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('self-drive-location-modal')).show();
        }));
        document.getElementById('self-drive-location-modal').addEventListener('shown.bs.modal', () => selfDriveLocationPickerMap.invalidateSize());
        document.getElementById('confirm-self-drive-location').addEventListener('click', async () => {
            if (!selfDriveSelectedLocation) return;
            const isDelivery = selfDriveLocationTarget === 'delivery';
            const marker = isDelivery ? selfDriveDeliveryMarker : selfDriveReturnMarker;
            const icon = isDelivery ? selfDriveDeliveryIcon : selfDriveReturnIcon;
            if (marker) marker.setLatLng(selfDriveSelectedLocation); else if (isDelivery) selfDriveDeliveryMarker = L.marker(selfDriveSelectedLocation, { icon }).addTo(selfDriveMap).bindPopup('Delivery'); else selfDriveReturnMarker = L.marker(selfDriveSelectedLocation, { icon }).addTo(selfDriveMap).bindPopup('Return');
            const prefix = isDelivery ? 'self-drive-delivery' : 'self-drive-return';
            document.getElementById(`${prefix}-latitude`).value = selfDriveSelectedLocation.lat;
            document.getElementById(`${prefix}-longitude`).value = selfDriveSelectedLocation.lng;
            document.getElementById(`${prefix}-location`).value = `Pinned location (${selfDriveSelectedLocation.lat.toFixed(5)}, ${selfDriveSelectedLocation.lng.toFixed(5)})`;
            bootstrap.Modal.getInstance(document.getElementById('self-drive-location-modal')).hide();
            updateSelfDriveDeliveryReturnFee();
        });
        document.querySelectorAll('.self-drive-location-reset').forEach((button) => button.addEventListener('click', () => {
            const isDelivery = button.dataset.location === 'delivery';
            const marker = isDelivery ? selfDriveDeliveryMarker : selfDriveReturnMarker;
            if (marker) selfDriveMap.removeLayer(marker);
            if (isDelivery) selfDriveDeliveryMarker = null; else selfDriveReturnMarker = null;
            const prefix = isDelivery ? 'self-drive-delivery' : 'self-drive-return';
            document.getElementById(`${prefix}-location`).value = isDelivery ? 'Pick-up to Garage' : 'Return to Garage';
            document.getElementById(`${prefix}-latitude`).value = '';
            document.getElementById(`${prefix}-longitude`).value = '';
            updateSelfDriveDeliveryReturnFee();
        }));
    }

    function calculateVehicleRentalRate(startAt, endAt) {
        const rentalMinutes = (new Date(endAt) - new Date(startAt)) / 60000;

        if (rentalMinutes <= 720) {
            return { amount: twelveHourRate, label: `12 hrs × ${formatPeso(twelveHourRate)}`, periods: 1 };
        }

        const periods = Math.max(1, Math.ceil(rentalMinutes / 1440));

        return {
            amount: twentyFourHourRate * periods,
            label: `${periods} × 24 hrs × ${formatPeso(twentyFourHourRate)}`,
            periods,
        };
    }

    function updateDailyEstimate() {
        const pickupAt = document.getElementById('pickup-at').value;
        const returnAt = document.getElementById('return-at').value;
        if (!pickupAt || !returnAt || new Date(returnAt) <= new Date(pickupAt)) return;
        const rental = calculateVehicleRentalRate(pickupAt, returnAt);
        const base = rental.amount;
        const driver = driverDailyRate * rental.periods;
        const wash = carWashFee;
        const discount = dailyDiscount * rental.periods;
        const vat = (base + driver + wash - discount) * .12;
        const total = base + driver + wash - discount + vat;
        document.getElementById('daily-base-label').textContent = `Base (${rental.label})`;
        document.getElementById('daily-driver-label').textContent = `Driver (${rental.periods} rental period${rental.periods === 1 ? '' : 's'})`;
        document.getElementById('daily-base').textContent = formatPeso(base);
        document.getElementById('daily-driver').textContent = formatPeso(driver);
        document.getElementById('daily-wash').textContent = formatPeso(wash);
        document.getElementById('daily-discount').textContent = `-${formatPeso(discount)}`;
        document.getElementById('daily-vat').textContent = formatPeso(vat);
        document.getElementById('daily-total').textContent = formatPeso(total);
    }

    function updateStartingPrice() {
        const isDropoffWithDriver = document.querySelector('input[name="ride_type"]:checked').value === 'with_driver'
            && document.querySelector('input[name="trip_length"]:checked').value === 'dropoff';

        document.getElementById('vehicle-starting-price').textContent = isDropoffWithDriver
            ? formatPeso(pickupDropoffFee)
            : baseRate ? `${formatPeso(baseRate)}/day` : 'On request';
    }

    function addOpenStreetMapTiles(map) {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);
    }

    function refreshEstimateMap() {
        if (!estimateMap) return;
        setTimeout(() => {
            estimateMap.invalidateSize();
        }, 100);
    }

    async function reverseGeocode(location) {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${location.lat}&lon=${location.lng}&addressdetails=1`);
        if (!response.ok) throw new Error('Address lookup failed.');

        return response.json();
    }

    async function searchAddresses(query) {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&q=${encodeURIComponent(query)}`);
        if (!response.ok) throw new Error('Address search failed.');

        return response.json();
    }

    async function setLocationPickerPoint(location) {
        locationPickerMarker.setLatLng([location.lat, location.lng]);
        locationPickerMap.panTo([location.lat, location.lng]);
        selectedLocation = location;
        document.getElementById('location-picker-address').textContent = 'Getting address…';
        document.getElementById('confirm-location-button').disabled = true;

        try {
            const place = await reverseGeocode(location);
            selectedLocation.address = place.display_name;
            document.getElementById('location-picker-address').textContent = selectedLocation.address;
            document.getElementById('confirm-location-button').disabled = false;
        } catch (error) {
            document.getElementById('location-picker-address').textContent = 'We could not find an address for this pin. Please choose another point.';
        }
    }

    function openLocationPicker(input) {
        selectedLocationInput = input;
        document.getElementById('location-picker-title').textContent = input.id === 'pickup-location' ? 'Choose pick-up location' : 'Choose drop-off location';
        const initialLocation = locationPickerMarker?.getLatLng() ?? { lat: defaultLocation[0], lng: defaultLocation[1] };
        locationPickerMap.setView([initialLocation.lat, initialLocation.lng], 15);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('location-picker-modal')).show();
        setLocationPickerPoint(initialLocation);
    }

    async function calculateRoute() {
        const pickup = document.getElementById('pickup-location').dataset.location;
        const dropoff = document.getElementById('dropoff-location').dataset.location;
        if (!pickup || !dropoff) return;

        const [pickupLat, pickupLng] = pickup.split(',').map(Number);
        const [dropoffLat, dropoffLng] = dropoff.split(',').map(Number);
        if (!Number.isFinite(garageLocation.lat) || !Number.isFinite(garageLocation.lng) || garageLocation.lat === 0 || garageLocation.lng === 0) return;
        const routeCoordinates = `${garageLocation.lng},${garageLocation.lat};${pickupLng},${pickupLat};${dropoffLng},${dropoffLat};${garageLocation.lng},${garageLocation.lat}`;
        const response = await fetch(`https://router.project-osrm.org/route/v1/driving/${routeCoordinates}?overview=full&geometries=geojson`);
        if (!response.ok) return;

        const result = await response.json();
        const route = result.routes?.[0];
        if (!route) return;

        if (estimateRoute) estimateMap.removeLayer(estimateRoute);
        estimateRoute = L.geoJSON(route.geometry, { style: { color: '#152d63', weight: 5 } }).addTo(estimateMap);
        estimateMap.fitBounds(estimateRoute.getBounds(), { padding: [24, 24] });
        const kilometers = route.distance / 1000;
        const pickupToDropoffLeg = route.legs[1];
        const displayedKilometers = (pickupToDropoffLeg?.distance ?? 0) / 1000;
        const displayedMinutes = Math.round((pickupToDropoffLeg?.duration ?? 0) / 60);
        const fuelCost = fuelConsumptionKmPerLiter > 0 ? (kilometers / fuelConsumptionKmPerLiter) * fuelPricePerLiter : 0;
        const pickupDropoffTotal = pickupDropoffFee + fuelCost + driverDailyRate;
        const vat = pickupDropoffTotal * .12;
        const estimatedTotal = pickupDropoffTotal + vat;
        const downpaymentDue = estimatedTotal * .2;
        document.getElementById('total-distance-km').value = kilometers.toFixed(2);
        document.getElementById('distance-display').textContent = `${displayedKilometers.toFixed(1)} km`;
        document.getElementById('duration-display').textContent = `${Math.floor(displayedMinutes / 60)}h ${displayedMinutes % 60}m`;
        document.getElementById('pickup-dropoff-fee').textContent = formatPeso(pickupDropoffTotal);
        document.getElementById('dropoff-vat').textContent = formatPeso(vat);
        document.getElementById('estimate-total').textContent = formatPeso(estimatedTotal);
        document.getElementById('downpayment-due').textContent = formatPeso(downpaymentDue);
    }

    function initVehicleEstimateMap() {
        const mapElement = document.getElementById('estimate-map');
        if (!mapElement) return;
        estimateMap = L.map(mapElement).setView(defaultLocation, 12);
        addOpenStreetMapTiles(estimateMap);
        const pickerElement = document.getElementById('location-picker-map');
        locationPickerMap = L.map(pickerElement).setView(defaultLocation, 14);
        addOpenStreetMapTiles(locationPickerMap);
        const exactLocationIcon = L.divIcon({
            className: '',
            html: '<span class="exact-location-pin"><i class="ti ti-map-pin-filled"></i></span>',
            iconSize: [34, 34],
            iconAnchor: [17, 34],
        });
        locationPickerMarker = L.marker(defaultLocation, { draggable: true, icon: exactLocationIcon }).addTo(locationPickerMap);
        locationPickerMap.on('click', (event) => setLocationPickerPoint(event.latlng));
        locationPickerMarker.on('dragend', (event) => setLocationPickerPoint(event.target.getLatLng()));
        document.querySelectorAll('.location-picker-input').forEach((input) => input.addEventListener('click', () => openLocationPicker(input)));
        document.getElementById('confirm-location-button').addEventListener('click', () => {
            if (!selectedLocationInput || !selectedLocation?.address) return;
            selectedLocationInput.value = selectedLocation.address;
            selectedLocationInput.dataset.location = `${selectedLocation.lat},${selectedLocation.lng}`;
            const prefix = selectedLocationInput.id === 'pickup-location' ? 'pickup' : 'dropoff';
            document.getElementById(`${prefix}-latitude`).value = selectedLocation.lat;
            document.getElementById(`${prefix}-longitude`).value = selectedLocation.lng;
            bootstrap.Modal.getInstance(document.getElementById('location-picker-modal')).hide();
            calculateRoute();
        });
        document.getElementById('location-picker-modal').addEventListener('shown.bs.modal', () => {
            locationPickerMap.invalidateSize();
            locationPickerMap.panTo(locationPickerMarker.getLatLng());
        });
        let searchTimeout;
        document.getElementById('location-search').addEventListener('input', (event) => {
            clearTimeout(searchTimeout);
            const query = event.target.value.trim();
            const resultsElement = document.getElementById('location-search-results');
            resultsElement.innerHTML = '';
            if (query.length < 3) return;
            searchTimeout = setTimeout(async () => {
                try {
                    const results = await searchAddresses(query);
                    results.forEach((place) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'list-group-item list-group-item-action small';
                        button.textContent = place.display_name;
                        button.addEventListener('click', () => {
                            resultsElement.innerHTML = '';
                            document.getElementById('location-search').value = place.display_name;
                            setLocationPickerPoint({ lat: Number(place.lat), lng: Number(place.lon) });
                        });
                        resultsElement.appendChild(button);
                    });
                } catch (error) {
                    resultsElement.innerHTML = '<div class="small text-muted px-2">Address search is unavailable. Please pin the location on the map.</div>';
                }
            }, 350);
        });
        ['pickup-at', 'return-at'].forEach((id) => document.getElementById(id).addEventListener('change', updateDailyEstimate));
        document.querySelectorAll('input[name="ride_type"], input[name="trip_length"]').forEach((input) => input.addEventListener('change', updateStartingPrice));
        updateStartingPrice();
    }

    initializeSelfDriveMap();
    initVehicleEstimateMap();
</script>
</body>
</html>
