<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Details | Nogaru Car Rental</title>

    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}" />
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>

    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        #map {
            height: 400px;
            border-radius: 0.5rem;
        }
        gmp-map:not(:defined) {
            display: none;
        }
        .stop-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            margin-bottom: 0.35rem;
            border: 1px solid #e9ecef;
            transition: box-shadow 0.15s ease;
        }
        .stop-item:hover {
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .stop-item .stop-marker {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--bs-primary, #3b82f6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .stop-item .stop-marker.origin-marker {
            background: #28a745;
        }
        .stop-item .stop-address {
            flex: 1;
            font-size: 0.85rem;
            line-height: 1.3;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .stop-item .stop-leg-dist {
            font-size: 0.75rem;
            color: #6c757d;
            white-space: nowrap;
            margin-left: 0.25rem;
            flex-shrink: 0;
        }
        .stop-item .btn-remove-stop {
            color: #dc3545;
            background: none;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            padding: 0 0 0 0.25rem;
            line-height: 1;
            flex-shrink: 0;
            opacity: 0.6;
            transition: opacity 0.15s;
        }
        .stop-item .btn-remove-stop:hover {
            opacity: 1;
        }
        .distance-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        .alert-map-key {
            font-size: 0.85rem;
        }
        .timeline-connector {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0;
            position: relative;
            width: 30px;
            flex-shrink: 0;
        }
        .timeline-connector .line {
            width: 2px;
            flex: 1;
            min-height: 20px;
            background: #caced4;
        }
        .timeline-connector .dist-label {
            font-size: 0.65rem;
            color: #6c757d;
            white-space: nowrap;
            background: #fff;
            padding: 0 2px;
            line-height: 1;
        }
        .origin-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 0.15rem;
        }
        /* Style the gmpx-place-picker input to match Bootstrap form-control */
        .place-picker-wrapper {
            position: relative;
            width: 100%;
        }
        .place-picker-wrapper gmpx-place-picker {
            width: 100%;
            display: block;
        }
        .place-picker-wrapper gmpx-place-picker::part(input) {
            width: 100%;
            padding: 0.45rem 0.75rem;
            font-size: 0.875rem;
            font-family: inherit;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background: #fff;
            color: #212529;
            outline: none;
            box-sizing: border-box;
            min-height: 37px;
        }
        .place-picker-wrapper gmpx-place-picker::part(input):focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>
                                </div>
                                <h4 class="page-title">Booking details</h4>
                                <p class="text-muted mb-0">
                                    {{ $business?->name }} • {{ $car?->car_model ?: ($car?->vehicle_type ?: 'Car') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Google Maps Itinerary Card --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-map-2 me-1"></i> Itinerary Planner
                                    </h5>
                                    <span class="badge bg-info distance-badge" id="distanceDisplay">
                                        <i class="ti ti-ruler me-1"></i> 0.0 km
                                    </span>
                                </div>
                                <div class="card-body">

                                    @if(!$googleMapsApiKey)
                                        <div class="alert alert-warning alert-map-key mb-3">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            Google Maps API key is not configured. Please set <code>GOOGLE_MAPS_API_KEY</code> in your <code>.env</code> file.
                                        </div>
                                    @endif

                                    <div class="row g-3">
                                        {{-- Pickup — always the origin --}}
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">
                                                <i class="ti ti-circle-filled text-success me-1" style="font-size:0.6rem;"></i>
                                                Pickup location (origin)
                                            </label>
                                            <div class="place-picker-wrapper">
                                                <gmpx-place-picker id="originPicker" type="address" for-map="map"></gmpx-place-picker>
                                            </div>
                                            <input type="hidden" id="originInput" value="{{ old('pickup_location') }}">
                                            <div id="originError" class="invalid-feedback"></div>
                                        </div>

                                        {{-- Add stop input --}}
                                        <div class="col-lg-6">
                                            <label class="form-label fw-semibold">
                                                <i class="ti ti-plus-circle text-primary me-1"></i>
                                                Add destination / stop
                                            </label>
                                            <div class="input-group">
                                                <div class="place-picker-wrapper" style="flex:1;">
                                                    <gmpx-place-picker id="stopPicker" type="address" for-map="map"></gmpx-place-picker>
                                                </div>
                                                <button class="btn btn-outline-primary" type="button" id="addStopBtn">
                                                    <i class="ti ti-plus"></i> Add
                                                </button>
                                            </div>
                                            <input type="hidden" id="stopInput">
                                            <div id="stopInputError" class="invalid-feedback"></div>
                                        </div>

                                        {{-- Stops list --}}
                                        <div class="col-12">
                                            <div id="stopsContainer">
                                                {{-- Dynamically populated --}}
                                            </div>
                                            <p id="noStopsMessage" class="text-muted small mb-0">
                                                <i class="ti ti-info-circle me-1"></i>
                                                Add at least one destination stop above to plot your route.
                                            </p>
                                        </div>

                                        {{-- Map --}}
                                        <div class="col-12">
                                            <gmp-map id="map" center="14.5,121.0" zoom="10" map-id="nogaru_booking_map">
                                                <gmp-advanced-marker id="originMarker" position="14.5,121.0"></gmp-advanced-marker>
                                            </gmp-map>
                                        </div>
                                    </div>

                                    {{-- Hidden fields submitted with form --}}
                                    <input type="hidden" name="itinerary_stops" id="itineraryStopsInput" value="">
                                    <input type="hidden" name="total_distance_km" id="totalDistanceInput" value="">

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Booking form card --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                                        @csrf

                                        <input type="hidden" name="business_id" value="{{ $business->id }}">
                                        <input type="hidden" name="car_id" value="{{ $car->id }}">

                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <label class="form-label">Pickup date</label>
                                                <input type="date" name="pickup_date" value="{{ old('pickup_date') }}" class="form-control" required>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label">Pickup time</label>
                                                <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" class="form-control" required>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Pickup location</label>
                                                <input type="text" name="pickup_location" id="pickupLocationHidden"
                                                       value="{{ old('pickup_location') }}" class="form-control" maxlength="255" required>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Destination / Itinerary</label>
                                                <textarea name="destination_itinerary" id="destinationItinerary" rows="3" class="form-control" maxlength="1000" required>{{ old('destination_itinerary') }}</textarea>
                                            </div>

                                            <div class="col-lg-6">
                                                <label class="form-label">Preferred vehicle</label>
                                                <input type="text" name="preferred_vehicle" value="{{ old('preferred_vehicle', $preferredVehicle) }}" class="form-control" maxlength="255" required>
                                            </div>

                                            <div class="col-lg-6">
                                                <label class="form-label">Number of passengers</label>
                                                <input type="number" name="passengers_count" value="{{ old('passengers_count') }}" class="form-control" min="1" max="20" required>
                                            </div>

                                            <div class="col-lg-6">
                                                <label class="form-label">Return date</label>
                                                <input type="date" name="return_date" value="{{ old('return_date') }}" class="form-control" required>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label">Return time</label>
                                                <input type="time" name="return_time" value="{{ old('return_time') }}" class="form-control" required>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Vehicle Handover Option</label>
                                                <select name="handover_option" class="form-control" required>
                                                    <option value="" disabled {{ old('handover_option') ? '' : 'selected' }}>Select option</option>
                                                    <option value="pickup" {{ old('handover_option') === 'pickup' ? 'selected' : '' }}>Pickup at location</option>
                                                    <option value="dropoff" {{ old('handover_option') === 'dropoff' ? 'selected' : '' }}>Drop-off at location</option>
                                                    <option value="meet" {{ old('handover_option') === 'meet' ? 'selected' : '' }}>Meet at a point</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    Submit booking request
                                                </button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0">{{ date('Y') }} &copy; Nogaru Car Rental Platform</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0 text-end">Booking</p>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/app.js') }}"></script>

    @if($googleMapsApiKey)
    {{-- Load Extended Component Library for gmpx-place-picker and gmp-map --}}
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/@googlemaps/extended-component-library/0.6.15/index.min.js">
    </script>

    <script>
        // ---- All JS must be defined BEFORE the Maps API script loads ----
        let _bookingStops = [];
        let _bookingLegs = [];
        let _bookingMap = null;
        let _bookingDirectionsService = null;
        let _bookingDirectionsRenderer = null;

        // DOM element references
        const originPicker = document.getElementById('originPicker');
        const stopPicker = document.getElementById('stopPicker');
        const originInput = document.getElementById('originInput');
        const stopInput = document.getElementById('stopInput');
        const addStopBtn = document.getElementById('addStopBtn');
        const stopsContainer = document.getElementById('stopsContainer');
        const noStopsMessage = document.getElementById('noStopsMessage');
        const distanceDisplay = document.getElementById('distanceDisplay');
        const itineraryStopsInput = document.getElementById('itineraryStopsInput');
        const totalDistanceInput = document.getElementById('totalDistanceInput');
        const pickupLocationHidden = document.getElementById('pickupLocationHidden');
        const destinationItinerary = document.getElementById('destinationItinerary');

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }

        function buildItineraryText() {
            const parts = [];
            const origin = originInput.value || (originPicker && originPicker.value ? (originPicker.value.formattedAddress || originPicker.value.displayName || '') : '');
            if (origin) parts.push('Pickup: ' + origin);
            _bookingStops.forEach((s, i) => parts.push('Stop ' + (i + 1) + ': ' + (s.address || s)));
            destinationItinerary.value = parts.join('\n');
        }

        function updateHiddenInputs() {
            const data = _bookingStops.map(s => ({
                address: s.address || s,
                lat: s.lat || null,
                lng: s.lng || null,
            }));
            itineraryStopsInput.value = JSON.stringify(data);
        }

        function renderStopsList() {
            stopsContainer.innerHTML = '';
            if (_bookingStops.length === 0) {
                noStopsMessage.style.display = 'block';
                return;
            }
            noStopsMessage.style.display = 'none';

            const origin = originInput.value || (originPicker && originPicker.value ? (originPicker.value.formattedAddress || originPicker.value.displayName || '') : '');

            if (origin) {
                const originDiv = document.createElement('div');
                originDiv.className = 'stop-item';
                originDiv.innerHTML = `
                    <span class="stop-marker origin-marker">
                        <i class="ti ti-circle-filled" style="font-size:0.5rem;"></i>
                    </span>
                    <div style="flex:1;min-width:0;">
                        <div class="origin-label">ORIGIN</div>
                        <div class="stop-address">${escapeHtml(origin)}</div>
                    </div>
                `;
                stopsContainer.appendChild(originDiv);
            }

            _bookingStops.forEach((stop, index) => {
                const address = stop.address || stop;

                const connector = document.createElement('div');
                connector.className = 'timeline-connector';
                const legDist = _bookingLegs && _bookingLegs[index] ? _bookingLegs[index] : null;
                connector.innerHTML = `
                    <div class="line"></div>
                    ${legDist ? `<span class="dist-label">${legDist}</span>` : ''}
                    <div class="line" style="min-height:8px;"></div>
                `;

                const stopDiv = document.createElement('div');
                stopDiv.className = 'stop-item';
                stopDiv.innerHTML = `
                    <span class="stop-marker">${index + 1}</span>
                    <span class="stop-address">${escapeHtml(address)}</span>
                    <span class="stop-leg-dist">
                        ${legDist ? `<i class="ti ti-ruler me-1"></i>${legDist}` : ''}
                    </span>
                    <button type="button" class="btn-remove-stop" data-index="${index}" title="Remove stop">
                        <i class="ti ti-x"></i>
                    </button>
                `;

                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.alignItems = 'stretch';
                row.style.gap = '0';
                row.appendChild(connector);
                row.appendChild(stopDiv);
                stopDiv.style.flex = '1';

                stopsContainer.appendChild(row);
            });

            stopsContainer.querySelectorAll('.btn-remove-stop').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.dataset.index);
                    _bookingStops.splice(idx, 1);
                    refreshItinerary();
                });
            });
        }

        function geocode(address) {
            return new Promise((resolve, reject) => {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ address: address }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const loc = results[0].geometry.location;
                        resolve({ lat: loc.lat(), lng: loc.lng(), formatted: results[0].formatted_address });
                    } else {
                        reject(new Error('Geocoding failed: ' + status));
                    }
                });
            });
        }

        async function addStop(address) {
            if (typeof google === 'undefined' || !google.maps) {
                _bookingStops.push({ address: address, lat: null, lng: null });
                refreshItinerary();
                if (stopPicker) stopPicker.value = null;
                return;
            }
            try {
                const result = await geocode(address);
                _bookingStops.push({ address: result.formatted, lat: result.lat, lng: result.lng });
                refreshItinerary();
                if (stopPicker) stopPicker.value = null;
            } catch (e) {
                _bookingStops.push({ address: address, lat: null, lng: null });
                refreshItinerary();
                if (stopPicker) stopPicker.value = null;
            }
        }

        async function refreshItinerary() {
            renderStopsList();
            updateHiddenInputs();
            buildItineraryText();

            const origin = originInput.value || (originPicker && originPicker.value ? (originPicker.value.formattedAddress || originPicker.value.displayName || '') : '');
            pickupLocationHidden.value = origin;

            if (!origin || _bookingStops.length === 0) {
                distanceDisplay.innerHTML = '<i class="ti ti-ruler me-1"></i> 0.0 km';
                totalDistanceInput.value = '';
                if (_bookingDirectionsRenderer) {
                    _bookingDirectionsRenderer.set('directions', null);
                }
                return;
            }

            if (typeof google === 'undefined' || !google.maps || !_bookingDirectionsService) {
                return;
            }

            let originCoords;
            try {
                originCoords = await geocode(origin);
            } catch (e) {
                return;
            }

            const waypoints = _bookingStops.slice(0, -1)
                .filter(s => s.lat && s.lng)
                .map(s => ({
                    location: new google.maps.LatLng(s.lat, s.lng),
                    stopover: true,
                }));

            const destination = _bookingStops[_bookingStops.length - 1];
            if (!destination.lat || !destination.lng) return;

            try {
                const result = await new Promise((resolve, reject) => {
                    _bookingDirectionsService.route(
                        {
                            origin: new google.maps.LatLng(originCoords.lat, originCoords.lng),
                            destination: new google.maps.LatLng(destination.lat, destination.lng),
                            waypoints: waypoints,
                            travelMode: google.maps.TravelMode.DRIVING,
                        },
                        (res, status) => {
                            if (status === 'OK') resolve(res);
                            else reject(new Error('Directions failed: ' + status));
                        }
                    );
                });

                _bookingDirectionsRenderer.setDirections(result);

                let totalMeters = 0;
                const legs = result.routes[0].legs;

                _bookingLegs = legs.map(leg => {
                    const legKm = leg.distance.value / 1000;
                    return legKm.toFixed(1) + ' km';
                });

                legs.forEach(leg => { totalMeters += leg.distance.value; });

                const km = totalMeters / 1000;
                distanceDisplay.innerHTML = `<i class="ti ti-ruler me-1"></i> ${km.toFixed(1)} km`;
                totalDistanceInput.value = km.toFixed(2);

                renderStopsList();
            } catch (e) {
                console.warn('Directions request failed:', e);
            }
        }

        // ---- event wiring ----
        addStopBtn.addEventListener('click', function () {
            const place = stopPicker ? stopPicker.value : null;
            const address = place ? (place.formattedAddress || place.displayName || '') : '';
            if (!address) {
                document.getElementById('stopInputError').textContent = 'Please select a place from the autocomplete.';
                return;
            }
            document.getElementById('stopInputError').textContent = '';
            addStop(address);
        });

        document.getElementById('bookingForm').addEventListener('submit', function () {
            updateHiddenInputs();
            buildItineraryText();
        });

        // ---- initBookingMap must be on window for the Maps API callback ----
        window.initBookingMap = async function () {
            try {
                await customElements.whenDefined('gmp-map');
            } catch (e) {
                // gmp-map might not be defined if component library hasn't loaded yet
                console.warn('gmp-map not defined yet, retrying...', e);
            }

            const mapEl = document.getElementById('map');
            if (!mapEl) return;

            // Access the inner Google Map from the gmp-map web component
            _bookingMap = mapEl.innerMap || mapEl;
            if (_bookingMap.setOptions) {
                _bookingMap.setOptions({ mapTypeControl: false });
            }

            _bookingDirectionsService = new google.maps.DirectionsService();
            _bookingDirectionsRenderer = new google.maps.DirectionsRenderer({
                map: _bookingMap,
                suppressMarkers: false,
            });

            // ---- Origin place picker ----
            if (originPicker) {
                originPicker.addEventListener('gmpx-placechange', () => {
                    const place = originPicker.value;
                    if (!place || !place.location) return;

                    const selectedAddress = place.formattedAddress || place.displayName || '';

                    // Update BOTH hidden inputs used by the UI logic and by form submission.
                    // pickupLocationHidden is the actual <input name="pickup_location"> submitted to backend.
                    originInput.value = selectedAddress;
                    pickupLocationHidden.value = selectedAddress;

                    if (place.viewport) {
                        _bookingMap.fitBounds(place.viewport);
                    } else {
                        _bookingMap.setCenter(place.location);
                        _bookingMap.setZoom(15);
                    }

                    refreshItinerary();
                });
            }


            // ---- Stop place picker ----
            if (stopPicker) {
                stopPicker.addEventListener('gmpx-placechange', () => {
                    const place = stopPicker.value;
                    if (!place || !place.location) return;
                    stopInput.value = place.formattedAddress || place.displayName || '';
                });
            }

            // Restore old values if validation failed
            const oldStops = {!! json_encode(old('itinerary_stops')) !!};
            if (oldStops && Array.isArray(oldStops) && oldStops.length) {
                _bookingStops = oldStops;
                renderStopsList();
                refreshItinerary();
            }
        };
    </script>

    {{-- Load Google Maps API with callback -- MUST come AFTER all JS definitions --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initBookingMap&solution-channel=GMP_CCS_autocomplete_v5"
            defer>
    </script>
    @else
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addStopBtn = document.getElementById('addStopBtn');
            const stopInput = document.getElementById('stopInput');
            const stopsContainer = document.getElementById('stopsContainer');
            const noStopsMessage = document.getElementById('noStopsMessage');
            const itineraryStopsInput = document.getElementById('itineraryStopsInput');
            const originInput = document.getElementById('originInput');
            const pickupLocationHidden = document.getElementById('pickupLocationHidden');

            let stops = [];

            function renderStopsList() {
                stopsContainer.innerHTML = '';
                if (stops.length === 0) {
                    noStopsMessage.style.display = 'block';
                    return;
                }
                noStopsMessage.style.display = 'none';
                stops.forEach((stop, index) => {
                    const div = document.createElement('div');
                    div.className = 'stop-item';
                    div.innerHTML = `
                        <span class="stop-index">${index + 1}</span>
                        <span class="stop-address">${stop}</span>
                        <button type="button" class="btn-remove-stop" data-index="${index}" title="Remove stop">
                            <i class="ti ti-x"></i>
                        </button>
                    `;
                    stopsContainer.appendChild(div);
                });
                stopsContainer.querySelectorAll('.btn-remove-stop').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(this.dataset.index);
                        stops.splice(idx, 1);
                        renderStopsList();
                        updateHidden();
                    });
                });
            }

            function updateHidden() {
                itineraryStopsInput.value = JSON.stringify(stops);
            }

            addStopBtn.addEventListener('click', function () {
                const address = stopInput.value.trim();
                if (!address) return;
                stops.push(address);
                stopInput.value = '';
                renderStopsList();
                updateHidden();
            });

            stopInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addStopBtn.click();
                }
            });

            originInput.addEventListener('input', function () {
                pickupLocationHidden.value = this.value;
            });
        });
    </script>
    @endif
</body>
</html>