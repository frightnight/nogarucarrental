@extends('layouts.theme')

@section('title', 'Driver Registration | Nogaru Car Rental')
@section('page-title', 'Driver Registration')

@section('content')
<div class="row justify-content-center"><div class="col-xl-9"><div class="card"><div class="card-body p-lg-5">
    <div class="mb-4"><h4>Apply as a professional driver</h4><p class="text-muted mb-0">Complete your details. An administrator will verify your application before businesses can hire you.</p></div>
    @if (isset($errors) && $errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('driver.register.store') }}" enctype="multipart/form-data">@csrf
        <h5 class="mb-3">Personal information</h5><div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label">First name *</label><input name="first_name" class="form-control" value="{{ old('first_name') }}" required></div>
            <div class="col-md-3"><label class="form-label">Middle name</label><input name="middle_name" class="form-control" value="{{ old('middle_name') }}"></div>
            <div class="col-md-3"><label class="form-label">Last name *</label><input name="last_name" class="form-control" value="{{ old('last_name') }}" required></div>
            <div class="col-md-3"><label class="form-label">Suffix</label><input name="suffix" class="form-control" value="{{ old('suffix') }}"></div>
            <div class="col-md-4"><label class="form-label">Date of birth *</label><input name="date_of_birth" type="date" class="form-control" value="{{ old('date_of_birth') }}" required></div>
            <div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Prefer not to say</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
            <div class="col-md-4"><label class="form-label">City / Province *</label><input name="city" class="form-control" value="{{ old('city') }}" required></div>
            <div class="col-12"><label class="form-label">Complete address *</label><textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Mobile number *</label><input name="phone" class="form-control" value="{{ old('phone') }}" required></div>
            <div class="col-md-6"><label class="form-label">Email *</label><input name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
            <div class="col-md-6"><label class="form-label">Password *</label><input name="password" type="password" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Confirm password *</label><input name="password_confirmation" type="password" class="form-control" required></div>
        </div>
        <h5 class="mb-3">License information</h5><div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">License number *</label><input name="license_number" class="form-control" value="{{ old('license_number') }}" required></div>
            <div class="col-md-6"><label class="form-label">License classification *</label><input name="license_type" class="form-control" placeholder="e.g. Professional / Restriction 1,2,3" value="{{ old('license_type') }}" required></div>
            <div class="col-md-4"><label class="form-label">Date issued</label><input name="license_issued_at" type="date" class="form-control" value="{{ old('license_issued_at') }}"></div>
            <div class="col-md-4"><label class="form-label">Expiration date *</label><input name="license_expires_at" type="date" class="form-control" value="{{ old('license_expires_at') }}" required></div>
            <div class="col-md-4"><label class="form-label">Restrictions</label><input name="license_restrictions" class="form-control" value="{{ old('license_restrictions') }}"></div>
            <div class="col-md-6"><label class="form-label">License front image *</label><input name="license_front" type="file" accept="image/*" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">License back image *</label><input name="license_back" type="file" accept="image/*" class="form-control" required></div>
        </div>
        <h5 class="mb-3">Experience and emergency contact</h5><div class="row g-3 mb-4">
            <div class="col-md-4"><label class="form-label">Employment type *</label><select name="employment_type" class="form-select" required><option value="">Select</option>@foreach(['full_time' => 'Full-time', 'part_time' => 'Part-time', 'on_call' => 'On-call', 'contract' => 'Contract', 'freelance' => 'Freelance'] as $value => $label)<option value="{{ $value }}" @selected(old('employment_type') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label">Years driving *</label><input name="years_driving_experience" type="number" min="0" max="80" class="form-control" value="{{ old('years_driving_experience') }}" required></div>
            <div class="col-md-4"><label class="form-label">Professional driving years *</label><input name="professional_driving_experience" type="number" min="0" max="80" class="form-control" value="{{ old('professional_driving_experience') }}" required></div>
            <div class="col-md-6"><label class="form-label">Vehicle experience</label><div class="d-flex flex-wrap gap-3">@foreach(['sedan' => 'Sedan', 'suv' => 'SUV', 'van' => 'Van', 'pickup' => 'Pickup', 'manual' => 'Manual', 'automatic' => 'Automatic'] as $value => $label)<label class="form-check"><input class="form-check-input" type="checkbox" name="vehicle_experience[]" value="{{ $value }}" @checked(in_array($value, old('vehicle_experience', []), true))><span class="form-check-label">{{ $label }}</span></label>@endforeach</div></div>
            <div class="col-md-6"><label class="form-label">Service experience</label><div class="d-flex flex-wrap gap-3">@foreach(['airport_transfer' => 'Airport transfer', 'city_tour' => 'City tour', 'long_distance' => 'Long distance', 'night_driving' => 'Night driving', 'vip_service' => 'VIP service'] as $value => $label)<label class="form-check"><input class="form-check-input" type="checkbox" name="service_experience[]" value="{{ $value }}" @checked(in_array($value, old('service_experience', []), true))><span class="form-check-label">{{ $label }}</span></label>@endforeach</div></div>
            <div class="col-md-6"><label class="form-label">Emergency contact name *</label><input name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}" required></div>
            <div class="col-md-6"><label class="form-label">Relationship *</label><input name="emergency_contact_relationship" class="form-control" value="{{ old('emergency_contact_relationship') }}" required></div>
            <div class="col-md-6"><label class="form-label">Emergency contact phone *</label><input name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}" required></div>
            <div class="col-md-6"><label class="form-label">Emergency contact address *</label><input name="emergency_contact_address" class="form-control" value="{{ old('emergency_contact_address') }}" required></div>
        </div>
        <button class="btn btn-primary" type="submit">Submit application</button><a href="{{ route('login') }}" class="btn btn-light ms-2">Already have an account?</a>
    </form>
</div></div></div></div>
@endsection
