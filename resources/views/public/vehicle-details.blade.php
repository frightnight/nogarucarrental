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
                    @php($dailyRate = $car->rates->whereIn('name', ['24hrs', 'Daily'])->min('value') ?? $car->rates->min('value'))
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-user text-primary vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Seats</span><span class="vehicle-spec__value">{{ $car->seats ?: '—' }} Passengers</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-briefcase text-purple vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Luggage</span><span class="vehicle-spec__value">{{ $car->seats ? max(1, intdiv($car->seats, 2)) : '—' }} Bags</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-settings text-secondary vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Transmission</span><span class="vehicle-spec__value">{{ $car->transmission ?: '—' }}</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-gas-station text-warning vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Fuel</span><span class="vehicle-spec__value">{{ $car->fuel_type ?: '—' }}</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-car text-success vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Vehicle Type</span><span class="vehicle-spec__value">{{ $car->vehicle_type ?: '—' }}</span></span></div></div>
                            <div class="col-6"><div class="vehicle-spec"><i class="ti ti-currency-peso text-primary vehicle-spec__icon"></i><span><span class="vehicle-spec__label">Starts at</span><span id="vehicle-starting-price" class="vehicle-spec__value">{{ $dailyRate ? '₱'.number_format((float) $dailyRate, 0).'/day' : 'On request' }}</span></span></div></div>
                        </div>
                    </div>
                </div>
                <section class="mt-4">
                    <h2 class="h6 fw-bold">Description</h2>
                    <p class="text-muted mb-0">Enjoy a comfortable, dependable ride from {{ $car->business->name }}. Contact the business for pickup arrangements and any additional vehicle details.</p>
                </section>
            </article>
        </section>
        <aside class="col-lg-4">
            <section class="vehicle-detail-card quick-estimate bg-white p-4">
                <h2 class="h5 fw-bold mb-4">Quick Estimate</h2>
                <form method="POST" action="{{ route('guest-checkout.store', $car) }}" id="ride-form">
                    @csrf
                    <p class="small text-muted mb-2">How would you like to ride?</p>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="ride-option"><input type="radio" name="ride_type" value="self_drive" checked><i class="ti ti-car me-1"></i><strong>Self-Drive</strong><span class="d-block small text-muted">You're in control</span></label></div>
                        <div class="col-6"><label class="ride-option"><input type="radio" name="ride_type" value="with_driver"><i class="ti ti-user me-1"></i><strong>With Driver</strong><span class="d-block small text-muted">Sit back &amp; relax</span></label></div>
                    </div>

                    <div id="self-drive-panel">
                        <p class="availability-note mb-3"><i class="ti ti-check me-1"></i>Available for selected dates</p>
                        @auth
                            @if(auth()->user()->hasRole('client'))
                                <a href="{{ route('bookings.details', ['car_id' => $car->id, 'business_id' => $car->business_id]) }}" class="btn btn-primary w-100">Continue to Booking</a>
                            @else
                                <p class="small text-muted text-center mb-0">Self-drive bookings require a client account.</p>
                            @endif
                        @else
                            <p class="small text-muted text-center">Self-drive booking requires an account.</p>
                            <div class="d-flex gap-2"><a href="{{ route('login') }}" class="btn btn-outline-primary flex-grow-1">Sign In</a><a href="{{ route('register') }}" class="btn btn-primary flex-grow-1">Register</a></div>
                        @endauth
                    </div>

                    <div id="with-driver-panel" class="d-none">
                        @if($errors->any())<div class="alert alert-danger small py-2">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
                        <p class="small text-muted mb-2">How long do you need the car?</p>
                        <div class="row g-2 mb-3"><div class="col-6"><label class="ride-option"><input type="radio" name="trip_length" value="dropoff" checked><strong>Just a drop-off</strong><span class="d-block small text-muted">by distance</span></label></div><div class="col-6"><label class="ride-option"><input type="radio" name="trip_length" value="daily"><strong>Keep the car</strong><span class="d-block small text-muted">Daily rate</span></label></div></div>
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

    function toggleRideType() {
        const withDriver = document.querySelector('input[name="ride_type"]:checked').value === 'with_driver';
        selfDrivePanel.classList.toggle('d-none', withDriver);
        withDriverPanel.classList.toggle('d-none', !withDriver);
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
        dropoffFields.querySelectorAll('input').forEach((input) => input.disabled = isDaily || withDriverPanel.classList.contains('d-none'));
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
    const baseRate = {{ $dailyRate ? (float) $dailyRate : 0 }};
    const driverDailyRate = {{ $driverDailyRate }};
    const carWashFee = {{ $carWashFee }};
    const dailyDiscount = {{ $dailyDiscount }};
    const pickupDropoffFee = {{ $pickupDropoffFee }};
    const fuelConsumptionKmPerLiter = {{ $fuelConsumptionKmPerLiter }};
    const fuelPricePerLiter = {{ $fuelPricePerLiter }};
    const garageLocation = { lat: {{ $garageLatitude }}, lng: {{ $garageLongitude }} };
    const formatPeso = (amount) => `₱${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    function updateDailyEstimate() {
        const pickupAt = document.getElementById('pickup-at').value;
        const returnAt = document.getElementById('return-at').value;
        if (!pickupAt || !returnAt || new Date(returnAt) <= new Date(pickupAt)) return;
        const days = Math.max(1, Math.ceil((new Date(returnAt) - new Date(pickupAt)) / 86400000));
        const base = baseRate * days;
        const driver = driverDailyRate * days;
        const wash = carWashFee;
        const discount = dailyDiscount * days;
        const vat = (base + driver + wash - discount) * .12;
        const total = base + driver + wash - discount + vat;
        document.getElementById('daily-base-label').textContent = `Base (${days}d × ${formatPeso(baseRate)})`;
        document.getElementById('daily-driver-label').textContent = `Driver (${days}d)`;
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

    initVehicleEstimateMap();
</script>
</body>
</html>
