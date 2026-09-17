@extends('layouts.theme')

@section('title', 'Business Profile | Nogaru Car Rental')
@section('page-title', 'Business Profile')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('business.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h5 class="mb-3">Business details</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Business name <span class="text-danger">*</span></label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $business->name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input id="city" name="city" type="text" class="form-control" value="{{ old('city', $business->city) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="business_type" class="form-label">Business type <span class="text-danger">*</span></label>
                                <select id="business_type" name="business_type" class="form-select" required>
                                    @foreach (['single_proprietorship' => 'Single Proprietorship', 'corporation' => 'Corporation', 'partnership' => 'Partnership', 'cooperative' => 'Cooperative', 'franchise' => 'Franchise', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('business_type', $business->business_type) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="business_category" class="form-label">Business category <span class="text-danger">*</span></label>
                                <select id="business_category" name="business_category" class="form-select" required>
                                    @foreach (['car_rental' => 'Car Rental', 'van_rental' => 'Van / Fleet Rental', 'travel_agency' => 'Travel & Tours', 'transport_service' => 'Transportation Service', 'corporate_fleet' => 'Corporate Fleet', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('business_category', $business->business_category) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="business_address" class="form-label">Business address <span class="text-danger">*</span></label>
                                <input id="business_address" name="business_address" type="text" class="form-control" value="{{ old('business_address', $business->business_address) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label">Business contact number <span class="text-danger">*</span></label>
                                <input id="contact_number" name="contact_number" type="text" class="form-control" value="{{ old('contact_number', $business->contact_number) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="registration_number" class="form-label">Registration number</label>
                                <input id="registration_number" name="registration_number" type="text" class="form-control" value="{{ old('registration_number', $business->registration_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="tin" class="form-label">TIN</label>
                                <input id="tin" name="tin" type="text" class="form-control" value="{{ old('tin', $business->tin) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="permit_issuer" class="form-label">Permit / license issuing office</label>
                                <input id="permit_issuer" name="permit_issuer" type="text" class="form-control" value="{{ old('permit_issuer', $business->permit_issuer) }}">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-2">Permit images</h5>
                        <p class="text-muted small">Upload up to 5 images total. JPG, PNG, or WebP; 4 MB maximum per image.</p>
                        <input id="permit_images" name="permit_images[]" type="file" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Save changes</button>
                            <a href="{{ route('business.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Uploaded permits</h5>
                    <div class="row g-3">
                        @forelse ($business->permit_images ?? [] as $permitImage)
                            <div class="col-6">
                                <a href="{{ asset('storage/'.$permitImage) }}" target="_blank" class="d-block mb-2">
                                    <img src="{{ asset('storage/'.$permitImage) }}" alt="Business permit" class="img-fluid rounded border" style="height: 120px; width: 100%; object-fit: cover;">
                                </a>
                                <form method="POST" action="{{ route('business.profile.permits.destroy') }}" onsubmit="return confirm('Remove this permit image?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="permit_path" value="{{ $permitImage }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="ti ti-trash me-1"></i> Remove</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No permit images uploaded.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
