@extends('layouts.theme')

@section('title', 'Add Vehicle | Nogaru Car Rental')
@section('page-title', 'Add Vehicle')
@section('content')

                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('business.fleet.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Fleet
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">Add Vehicle</h4>
                                <p class="text-muted mb-0">Register a new vehicle to your fleet</p>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="ti ti-alert-circle me-1"></i> Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('business.fleet.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Main Details -->
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="mb-0 text-white"><i class="ti ti-car me-2"></i>Vehicle Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="car_model" class="form-label fw-semibold">Car Model *</label>
                                                <input type="text" class="form-control @error('car_model') is-invalid @enderror"
                                                       id="car_model" name="car_model" value="{{ old('car_model') }}"
                                                       placeholder="e.g. Toyota Camry">
                                                @error('car_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="vehicle_type" class="form-label fw-semibold">Vehicle Type</label>
                                                <input type="text" class="form-control @error('vehicle_type') is-invalid @enderror"
                                                       id="vehicle_type" name="vehicle_type" value="{{ old('vehicle_type') }}"
                                                       placeholder="e.g. Sedan, SUV, Truck">
                                                @error('vehicle_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="variant" class="form-label fw-semibold">Variant</label>
                                                <input type="text" class="form-control @error('variant') is-invalid @enderror"
                                                       id="variant" name="variant" value="{{ old('variant') }}"
                                                       placeholder="e.g. LE, XLE, Sport">
                                                @error('variant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="transmission" class="form-label fw-semibold">Transmission</label>
                                                <select class="form-select @error('transmission') is-invalid @enderror"
                                                        id="transmission" name="transmission">
                                                    <option value="">Select...</option>
                                                    <option value="Automatic" @selected(old('transmission') === 'Automatic')>Automatic</option>
                                                    <option value="Manual" @selected(old('transmission') === 'Manual')>Manual</option>
                                                    <option value="CVT" @selected(old('transmission') === 'CVT')>CVT</option>
                                                </select>
                                                @error('transmission')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="year_model" class="form-label fw-semibold">Year Model</label>
                                                <input type="number" class="form-control @error('year_model') is-invalid @enderror"
                                                       id="year_model" name="year_model" value="{{ old('year_model') }}"
                                                       min="1900" max="2100" placeholder="e.g. 2024">
                                                @error('year_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="plate_number" class="form-label fw-semibold">Plate Number</label>
                                                <input type="text" class="form-control @error('plate_number') is-invalid @enderror"
                                                       id="plate_number" name="plate_number" value="{{ old('plate_number') }}"
                                                       placeholder="e.g. ABC-1234">
                                                @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="rental_type" class="form-label fw-semibold">Rental Type</label>
                                                <select class="form-select @error('rental_type') is-invalid @enderror"
                                                        id="rental_type" name="rental_type">
                                                    <option value="">Select...</option>
                                                    <option value="Daily" @selected(old('rental_type') === 'Daily')>Daily</option>
                                                    <option value="Weekly" @selected(old('rental_type') === 'Weekly')>Weekly</option>
                                                    <option value="Monthly" @selected(old('rental_type') === 'Monthly')>Monthly</option>
                                                </select>
                                                @error('rental_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="registration_expires_at" class="form-label fw-semibold">Registration Expiry</label>
                                                <input type="date" class="form-control" id="registration_expires_at" name="registration_expires_at" value="{{ old('registration_expires_at') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="insurance_expires_at" class="form-label fw-semibold">Insurance Expiry</label>
                                                <input type="date" class="form-control" id="insurance_expires_at" name="insurance_expires_at" value="{{ old('insurance_expires_at') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="seats" class="form-label fw-semibold">Seats</label>
                                                <input type="number" class="form-control @error('seats') is-invalid @enderror" id="seats" name="seats" value="{{ old('seats') }}" min="1" max="60">
                                                @error('seats')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="status" class="form-label fw-semibold">Availability</label>
                                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                    <option value="available" @selected(old('status', 'available') === 'available')>Available</option>
                                                    <option value="maintenance" @selected(old('status') === 'maintenance')>In maintenance</option>
                                                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                                                </select>
                                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="mb-0 text-white"><i class="ti ti-photo me-2"></i>Vehicle Images</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="images" class="form-label fw-semibold">Upload Images</label>
                                            <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                                   id="images" name="images[]" multiple accept="image/*">
                                            <div class="form-text">JPEG, PNG, WebP up to 5MB each. Multiple files allowed.</div>
                                            @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div id="image-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white"><i class="ti ti-cash me-2"></i>Vehicle Rates</h5>
                                <button id="add-rate" type="button" class="btn btn-sm btn-light">Add Rate</button>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Add rental periods and destination surcharges, such as 24hrs or Sorsogon/CamSur surcharge.</p>
                                <div id="rate-rows" class="d-flex flex-column gap-2">
                                    @foreach(old('rates', [['name' => '', 'value' => '']]) as $index => $rate)
                                        <div class="rate-row row g-2 align-items-end">
                                            <div class="col-md-6">
                                                <label class="form-label">Rate Name</label>
                                                <input type="text" class="form-control @error("rates.$index.name") is-invalid @enderror" name="rates[{{ $index }}][name]" value="{{ $rate['name'] }}" placeholder="e.g. 24hrs">
                                                @error("rates.$index.name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Amount (₱)</label>
                                                <input type="number" class="form-control @error("rates.$index.value") is-invalid @enderror" name="rates[{{ $index }}][value]" value="{{ $rate['value'] }}" min="0" max="99999999.99" step="0.01" placeholder="0.00">
                                                @error("rates.$index.value")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="remove-rate btn btn-outline-danger w-100">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary fw-semibold px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Save Vehicle
                                </button>
                                <a href="{{ route('business.fleet.index') }}" class="btn btn-outline-secondary fw-semibold px-4 ms-2">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>

@endsection

@push('scripts')
    <script>
        // Image preview
        document.getElementById('images').addEventListener('change', function(e) {
            const container = document.getElementById('image-preview');
            container.innerHTML = '';
            for (const file of e.target.files) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.className = 'rounded border';
                    img.style.width = '80px';
                    img.style.height = '60px';
                    img.style.objectFit = 'cover';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });

        const rateRows = document.getElementById('rate-rows');
        const addRateButton = document.getElementById('add-rate');
        let nextRateIndex = {{ count(old('rates', [['name' => '', 'value' => '']])) }};

        addRateButton.addEventListener('click', function() {
            const index = nextRateIndex++;
            const row = document.createElement('div');
            row.className = 'rate-row row g-2 align-items-end';
            row.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label">Rate Name</label>
                    <input type="text" class="form-control" name="rates[${index}][name]" placeholder="e.g. 24hrs">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" class="form-control" name="rates[${index}][value]" min="0" max="99999999.99" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <button type="button" class="remove-rate btn btn-outline-danger w-100">Remove</button>
                </div>`;
            rateRows.appendChild(row);
        });

        rateRows.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-rate')) {
                event.target.closest('.rate-row').remove();
            }
        });
    </script>
@endpush
