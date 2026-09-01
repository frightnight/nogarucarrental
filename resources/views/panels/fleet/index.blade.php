@extends('layouts.theme')

@section('title', 'Fleet Management | Nogaru Car Rental')
@section('page-title', 'Fleet Management')
@section('content')

                    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
                    <style>.garage-address-preview-map { height: 180px; border-radius: .5rem; overflow: hidden; } .garage-location-picker-map { height: min(55vh, 440px); border-radius: .5rem; overflow: hidden; }</style>

                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('business.fleet.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-plus me-1"></i> Add Vehicle
                                    </a>
                                    <a href="{{ route('business.dashboard') }}" class="btn btn-outline-secondary btn-sm me-1">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">Fleet Management</h4>
                                <p class="text-muted mb-0">Manage your vehicles — {{ $cars->count() }} vehicle(s) registered</p>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti ti-check me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-xl-4 mb-4 mb-xl-0">
                    <div class="card h-100">
                        <div class="card-header"><h5 class="mb-0"><i class="ti ti-gas-station me-2"></i>Gas Prices</h5></div>
                        <div class="card-body"><p class="text-muted small">These business-wide prices are visible for all vehicles in your fleet.</p><form method="POST" action="{{ route('business.fleet.fuel-prices.update') }}" class="row g-3">@csrf @method('PUT')
                            @foreach(['diesel_premium_price_per_liter' => 'Diesel Premium per Liter', 'diesel_regular_price_per_liter' => 'Diesel Regular per Liter', 'gasoline_premium_price_per_liter' => 'Gasoline Premium per Liter', 'gasoline_regular_price_per_liter' => 'Gasoline Regular per Liter'] as $field => $label)
                                <div class="col-md-3"><label for="{{ $field }}" class="form-label">{{ $label }}</label><div class="input-group"><span class="input-group-text">₱</span><input id="{{ $field }}" type="number" name="{{ $field }}" class="form-control" value="{{ old($field, $business->$field) }}" min="0" max="999.99" step="0.01" required></div></div>
                            @endforeach
                            <div class="col-12"><button class="btn btn-primary" type="submit">Save Gas Prices</button></div>
                        </form></div>
                    </div>

                        </div>
                        <div class="col-xl-4 mb-4 mb-xl-0">
                            <div class="card h-100">
                                <div class="card-header"><h5 class="mb-0"><i class="ti ti-discount-2 me-2"></i>Discounts</h5></div>
                                <div class="card-body"><p class="text-muted small">These long-term discounts apply to every vehicle in your fleet.</p><form method="POST" action="{{ route('business.fleet.long-term-discounts.update') }}" class="row g-3">@csrf @method('PUT')
                                    @foreach(['discount_7_to_14_days_percent' => '7 - 14 DAYS', 'discount_15_to_24_days_percent' => '15 - 24 DAYS', 'discount_25_to_31_days_percent' => '25 - 31 DAYS'] as $field => $label)
                                        <div class="col-12"><label for="{{ $field }}" class="form-label">{{ $label }}</label><div class="input-group"><input id="{{ $field }}" type="number" name="{{ $field }}" class="form-control" value="{{ old($field, $business->$field) }}" min="0" max="100" step="0.01" required><span class="input-group-text">%</span></div></div>
                                    @endforeach
                                    <div class="col-12"><button class="btn btn-primary" type="submit">Save Discounts</button></div>
                                </form></div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card h-100">
                                <div class="card-header"><h5 class="mb-0"><i class="ti ti-building-warehouse me-2"></i>Garage Address</h5></div>
                                <div class="card-body"><p class="text-muted small">Set the location where vehicles are collected and returned.</p><form id="garage-address-form" method="POST" action="{{ route('business.fleet.garage-address.update') }}">@csrf @method('PUT')
                                    <div class="mb-3"><label for="garage-address" class="form-label">Garage Address</label><input id="garage-address" name="garage_address" type="text" class="form-control" value="{{ old('garage_address', $business->garage_address) }}" placeholder="Choose a location on the map" readonly required></div>
                                    <input id="garage-latitude" name="garage_latitude" type="hidden" value="{{ old('garage_latitude', $business->garage_latitude) }}"><input id="garage-longitude" name="garage_longitude" type="hidden" value="{{ old('garage_longitude', $business->garage_longitude) }}">
                                    <div id="garage-address-preview-map" class="garage-address-preview-map"></div>
                                </form></div>
                            </div>
                        </div>
                    </div>

                    <!-- Fleet Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    @if($cars->isEmpty())
                                        <div class="text-center py-5">
                                            <i class="ti ti-car-off text-muted" style="font-size: 3rem;"></i>
                                            <h4 class="mt-3 text-muted">No vehicles in your fleet yet</h4>
                                            <p class="text-muted fs-sm">Add your first vehicle to start accepting bookings.</p>
                                            <a href="{{ route('business.fleet.create') }}" class="btn btn-primary mt-2">
                                                <i class="ti ti-plus me-1"></i> Add Vehicle
                                            </a>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover table-centered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Images</th>
                                                        <th>Model</th>
                                                        <th>Type</th>
                                                        <th>Transmission</th>
                                                        <th>Plate #</th>
                                                        <th>Year</th>
                                                        <th>Rates</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($cars as $car)
                                                        <tr>
                                                            <td>
                                                                @if($car->images->isNotEmpty())
                                                                    <div class="d-flex gap-1">
                                                                        @foreach($car->images->take(3) as $img)
                                                                            <img src="{{ $img->image_path }}" alt="Car image"
                                                                                 class="rounded" width="50" height="40"
                                                                                 style="object-fit: cover;">
                                                                        @endforeach
                                                                        @if($car->images->count() > 3)
                                                                            <span class="badge bg-secondary d-flex align-items-center">+{{ $car->images->count() - 3 }}</span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted fs-sm">No images</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <strong>{{ $car->car_model ?: '—' }}</strong>
                                                                @if($car->variant)
                                                                    <br><small class="text-muted">{{ $car->variant }}</small>
                                                                @endif
                                                            </td>
                                                            <td>{{ $car->vehicle_type ?: '—' }}</td>
                                                            <td>
                                                                @if($car->transmission)
                                                                    <span class="badge bg-info-subtle text-info rounded-pill">{{ $car->transmission }}</span>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td><code>{{ $car->plate_number ?: '—' }}</code></td>
                                                            <td>{{ $car->year_model ?: '—' }}</td>
                                                            <td>
                                                                @forelse($car->rates as $rate)
                                                                    <div class="small text-nowrap">
                                                                        {{ $rate->name }}: <strong>₱{{ number_format((float) $rate->value, 2) }}</strong>
                                                                    </div>
                                                                @empty
                                                                    <span class="text-muted fs-sm">No rates</span>
                                                                @endforelse
                                                            </td>
                                                            <td class="text-end">
                                                                <a href="{{ route('business.fleet.edit', $car) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="ti ti-edit"></i> Edit
                                                                </a>
                                                                <form method="POST" action="{{ route('business.fleet.destroy', $car) }}"
                                                                      class="d-inline"
                                                                      onsubmit="return confirm('Remove this vehicle from your fleet?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="ti ti-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="garage-location-picker-modal" tabindex="-1" aria-labelledby="garage-location-picker-title" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content border-0 shadow">
                            <div class="modal-header"><h2 id="garage-location-picker-title" class="modal-title fs-5">Choose garage location</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                            <div class="modal-body"><p class="small text-muted">Search for an address, click the map, or drag the pin to select an exact location.</p><label for="garage-location-search" class="visually-hidden">Search address</label><input id="garage-location-search" class="form-control mb-2" placeholder="Search address"><div id="garage-location-search-results" class="list-group mb-3"></div><div id="garage-location-picker-map" class="garage-location-picker-map"></div><div class="mt-3"><span class="small text-muted d-block mb-1">Selected address</span><div id="garage-location-picker-address" class="bg-light rounded p-3 small">Choose a point on the map.</div></div></div>
                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button id="confirm-garage-location-button" type="button" class="btn btn-primary" disabled>Confirm location</button></div>
                        </div></div>
                    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const garageAddressInput = document.getElementById('garage-address');
        const garagePickerMapElement = document.getElementById('garage-location-picker-map');
        const garagePreviewMapElement = document.getElementById('garage-address-preview-map');

        if (garagePickerMapElement && garagePreviewMapElement) {
            const latitudeInput = document.getElementById('garage-latitude');
            const longitudeInput = document.getElementById('garage-longitude');
            const defaultGarageLocation = [Number(latitudeInput.value) || 13.4543, Number(longitudeInput.value) || 123.3654];
            const garagePickerMap = L.map(garagePickerMapElement).setView(defaultGarageLocation, 14);
            const garageMarker = L.marker(defaultGarageLocation, { draggable: true }).addTo(garagePickerMap);
            const garagePreviewMap = L.map(garagePreviewMapElement, { dragging: false, scrollWheelZoom: false, doubleClickZoom: false, touchZoom: false }).setView(defaultGarageLocation, 14);
            const garagePreviewMarker = L.marker(defaultGarageLocation).addTo(garagePreviewMap);
            let selectedGarageLocation;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(garagePickerMap);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(garagePreviewMap);

            async function setGarageLocation(location) {
                garageMarker.setLatLng(location);
                garagePreviewMarker.setLatLng(location);
                garagePreviewMap.setView(location, garagePreviewMap.getZoom());
                garagePickerMap.panTo(location);
                selectedGarageLocation = location;
                document.getElementById('garage-location-picker-address').textContent = 'Getting address…';
                document.getElementById('confirm-garage-location-button').disabled = true;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${location.lat}&lon=${location.lng}&addressdetails=1`);
                    if (!response.ok) throw new Error('Address lookup failed.');
                    const place = await response.json();
                    selectedGarageLocation.address = place.display_name;
                    document.getElementById('garage-location-picker-address').textContent = selectedGarageLocation.address;
                    document.getElementById('confirm-garage-location-button').disabled = false;
                } catch (error) {
                    document.getElementById('garage-location-picker-address').textContent = 'We could not find an address for this pin. Please choose another point.';
                }
            }

            garagePickerMap.on('click', (event) => setGarageLocation(event.latlng));
            garageMarker.on('dragend', () => setGarageLocation(garageMarker.getLatLng()));
            garageAddressInput.addEventListener('click', () => {
                const initialLocation = garageMarker.getLatLng();
                garagePickerMap.setView(initialLocation, 15);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('garage-location-picker-modal')).show();
                setGarageLocation(initialLocation);
            });
            document.getElementById('garage-location-picker-modal').addEventListener('shown.bs.modal', () => garagePickerMap.invalidateSize());
            document.getElementById('confirm-garage-location-button').addEventListener('click', () => {
                if (!selectedGarageLocation?.address) return;
                garageAddressInput.value = selectedGarageLocation.address;
                latitudeInput.value = selectedGarageLocation.lat.toFixed(7);
                longitudeInput.value = selectedGarageLocation.lng.toFixed(7);
                garagePreviewMarker.setLatLng(selectedGarageLocation);
                garagePreviewMap.setView(selectedGarageLocation, 14);
                document.getElementById('garage-address-form').submit();
            });
            let searchTimeout;
            document.getElementById('garage-location-search').addEventListener('input', (event) => {
                clearTimeout(searchTimeout);
                const query = event.target.value.trim();
                const resultsElement = document.getElementById('garage-location-search-results');
                resultsElement.innerHTML = '';
                if (query.length < 3) return;
                searchTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&q=${encodeURIComponent(query)}`);
                        if (!response.ok) throw new Error('Address search failed.');
                        const results = await response.json();
                        results.forEach((place) => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'list-group-item list-group-item-action small';
                            button.textContent = place.display_name;
                            button.addEventListener('click', () => {
                                resultsElement.innerHTML = '';
                                event.target.value = place.display_name;
                                setGarageLocation({ lat: Number(place.lat), lng: Number(place.lon) });
                            });
                            resultsElement.appendChild(button);
                        });
                    } catch (error) {
                        resultsElement.innerHTML = '<div class="small text-muted px-2">Address search is unavailable. Please pin the location on the map.</div>';
                    }
                }, 350);
            });
        }
    </script>
@endpush
