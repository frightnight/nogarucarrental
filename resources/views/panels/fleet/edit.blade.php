@extends('layouts.theme')

@section('title', 'Edit Vehicle | Nogaru Car Rental')
@section('page-title', 'Edit Vehicle')
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
                                <h4 class="page-title">Edit Vehicle</h4>
                                <p class="text-muted mb-0">{{ $car->car_model ?: 'Update vehicle details' }}</p>
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

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti ti-check me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('business.fleet.update', $car) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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
                                                       id="car_model" name="car_model" value="{{ old('car_model', $car->car_model) }}"
                                                       placeholder="e.g. Toyota Camry">
                                                @error('car_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="vehicle_type" class="form-label fw-semibold">Vehicle Type</label>
                                                <input type="text" class="form-control @error('vehicle_type') is-invalid @enderror"
                                                       id="vehicle_type" name="vehicle_type" value="{{ old('vehicle_type', $car->vehicle_type) }}"
                                                       placeholder="e.g. Sedan, SUV, Truck">
                                                @error('vehicle_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="variant" class="form-label fw-semibold">Variant</label>
                                                <input type="text" class="form-control @error('variant') is-invalid @enderror"
                                                       id="variant" name="variant" value="{{ old('variant', $car->variant) }}"
                                                       placeholder="e.g. LE, XLE, Sport">
                                                @error('variant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="transmission" class="form-label fw-semibold">Transmission</label>
                                                <select class="form-select @error('transmission') is-invalid @enderror"
                                                        id="transmission" name="transmission">
                                                    <option value="">Select...</option>
                                                    <option value="Automatic" @selected(old('transmission', $car->transmission) === 'Automatic')>Automatic</option>
                                                    <option value="Manual" @selected(old('transmission', $car->transmission) === 'Manual')>Manual</option>
                                                    <option value="CVT" @selected(old('transmission', $car->transmission) === 'CVT')>CVT</option>
                                                </select>
                                                @error('transmission')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="year_model" class="form-label fw-semibold">Year Model</label>
                                                <input type="number" class="form-control @error('year_model') is-invalid @enderror"
                                                       id="year_model" name="year_model" value="{{ old('year_model', $car->year_model) }}"
                                                       min="1900" max="2100" placeholder="e.g. 2024">
                                                @error('year_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="plate_number" class="form-label fw-semibold">Plate Number</label>
                                                <input type="text" class="form-control @error('plate_number') is-invalid @enderror"
                                                       id="plate_number" name="plate_number" value="{{ old('plate_number', $car->plate_number) }}"
                                                       placeholder="e.g. ABC-1234">
                                                @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="rental_type" class="form-label fw-semibold">Rental Type</label>
                                                <select class="form-select @error('rental_type') is-invalid @enderror"
                                                        id="rental_type" name="rental_type">
                                                    <option value="">Select...</option>
                                                    <option value="Daily" @selected(old('rental_type', $car->rental_type) === 'Daily')>Daily</option>
                                                    <option value="Weekly" @selected(old('rental_type', $car->rental_type) === 'Weekly')>Weekly</option>
                                                    <option value="Monthly" @selected(old('rental_type', $car->rental_type) === 'Monthly')>Monthly</option>
                                                </select>
                                                @error('rental_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="seats" class="form-label fw-semibold">Seats</label>
                                                <input type="number" class="form-control @error('seats') is-invalid @enderror" id="seats" name="seats" value="{{ old('seats', $car->seats) }}" min="1" max="60">
                                                @error('seats')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="status" class="form-label fw-semibold">Availability</label>
                                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                    <option value="available" @selected(old('status', $car->status) === 'available')>Available</option>
                                                    <option value="maintenance" @selected(old('status', $car->status) === 'maintenance')>In maintenance</option>
                                                    <option value="inactive" @selected(old('status', $car->status) === 'inactive')>Inactive</option>
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
                                        <!-- Existing images -->
                                        @if($car->images->isNotEmpty())
                                            <label class="form-label fw-semibold">Current Images</label>
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                @foreach($car->images as $img)
                                                    <div class="position-relative" style="width: 80px; height: 60px;">
                                                        <img src="{{ $img->image_path }}" alt="Car image"
                                                             class="rounded border" style="width: 100%; height: 100%; object-fit: cover;">
                                                        <div class="form-check position-absolute top-0 end-0 m-1">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="delete_images[]" value="{{ $img->id }}"
                                                                   id="del-img-{{ $img->id }}"
                                                                   style="border: 2px solid #dc3545;">
                                                            <label class="form-check-label visually-hidden" for="del-img-{{ $img->id }}">Delete</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-danger fs-12 mb-2"><i class="ti ti-alert-triangle me-1"></i>Check images to delete</p>
                                        @endif

                                        <div class="mb-3">
                                            <label for="images" class="form-label fw-semibold">Upload New Images</label>
                                            <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                                   id="images" name="images[]" multiple accept="image/*">
                                            <div class="form-text">JPEG, PNG, WebP up to 5MB each.</div>
                                            @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div id="image-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary fw-semibold px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Update Vehicle
                                </button>
                                <a href="{{ route('business.fleet.index') }}" class="btn btn-outline-secondary fw-semibold px-4 ms-2">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>

                    <div id="rates" class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0 text-white"><i class="ti ti-cash me-2"></i>Vehicle Rates</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('business.fleet.rates.store', $car) }}" class="row g-2 align-items-end mb-4">
                                @csrf
                                <div class="col-md-6">
                                    <label for="rate-name" class="form-label fw-semibold">Rate Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="rate-name" name="name" value="{{ old('name') }}" placeholder="e.g. 24hrs" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="rate-value" class="form-label fw-semibold">Amount (₱)</label>
                                    <input type="number" class="form-control @error('value') is-invalid @enderror" id="rate-value" name="value" value="{{ old('value') }}" min="0" max="99999999.99" step="0.01" required>
                                    @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus me-1"></i>Add Rate</button>
                                </div>
                            </form>

                            @if($car->rates->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Rate Name</th>
                                                <th>Amount</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($car->rates as $rate)
                                                <tr>
                                                    <td>
                                                        <form id="rate-update-{{ $rate->id }}" method="POST" action="{{ route('business.fleet.rates.update', [$car, $rate]) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="text" class="form-control form-control-sm" name="name" value="{{ $rate->name }}" required>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <input form="rate-update-{{ $rate->id }}" type="number" class="form-control form-control-sm" name="value" value="{{ $rate->value }}" min="0" max="99999999.99" step="0.01" required>
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <button form="rate-update-{{ $rate->id }}" type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                        <form method="POST" action="{{ route('business.fleet.rates.destroy', [$car, $rate]) }}" class="d-inline" onsubmit="return confirm('Remove this rate?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No rates have been added for this vehicle.</p>
                            @endif
                        </div>
                    </div>

@endsection

@push('scripts')
    <script>
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
    </script>
@endpush
