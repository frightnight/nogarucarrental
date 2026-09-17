<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complete your business profile | Nogaru Car Rental</title>
    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}">
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
</head>
<body class="authentication-bg position-relative">
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-7 col-lg-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-header py-4 text-center bg-primary">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('theme/assets/images/logo.png') }}" alt="Nogaru" height="28">
                            </a>
                        </div>

                        <div class="card-body p-4 p-lg-5">
                            <div class="text-center mb-4">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-3">Step 2 of 3</span>
                                <h4 class="text-dark-50 mt-0 fw-bold">Complete your business profile</h4>
                                <p class="text-muted mb-0">Set up the legal and operational details for your business before you can continue using the app.</p>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between small text-muted mb-2">
                                    <span class="text-primary fw-semibold">1. Register account</span>
                                    <span class="text-primary fw-semibold">2. Business profile</span>
                                    <span>3. Manage business</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: 66%;" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('business.profile.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="business_type" class="form-label">Business type</label>
                                        <select id="business_type" name="business_type" class="form-select" required>
                                            <option value="">Select business type</option>
                                            <option value="single_proprietorship" {{ old('business_type') === 'single_proprietorship' ? 'selected' : '' }}>Single Proprietorship</option>
                                            <option value="corporation" {{ old('business_type') === 'corporation' ? 'selected' : '' }}>Corporation</option>
                                            <option value="partnership" {{ old('business_type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                                            <option value="cooperative" {{ old('business_type') === 'cooperative' ? 'selected' : '' }}>Cooperative</option>
                                            <option value="franchise" {{ old('business_type') === 'franchise' ? 'selected' : '' }}>Franchise</option>
                                            <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="business_category" class="form-label">Business category</label>
                                        <select id="business_category" name="business_category" class="form-select" required>
                                            <option value="">Select category</option>
                                            <option value="car_rental" {{ old('business_category') === 'car_rental' ? 'selected' : '' }}>Car Rental</option>
                                            <option value="van_rental" {{ old('business_category') === 'van_rental' ? 'selected' : '' }}>Van / Fleet Rental</option>
                                            <option value="travel_agency" {{ old('business_category') === 'travel_agency' ? 'selected' : '' }}>Travel & Tours</option>
                                            <option value="transport_service" {{ old('business_category') === 'transport_service' ? 'selected' : '' }}>Transportation Service</option>
                                            <option value="corporate_fleet" {{ old('business_category') === 'corporate_fleet' ? 'selected' : '' }}>Corporate Fleet</option>
                                            <option value="other" {{ old('business_category') === 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="registration_number" class="form-label">Registration number</label>
                                        <input id="registration_number" name="registration_number" type="text" class="form-control" value="{{ old('registration_number') }}" placeholder="e.g. DTI/SEC/Reg No.">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="tin" class="form-label">TIN</label>
                                        <input id="tin" name="tin" type="text" class="form-control" value="{{ old('tin') }}" placeholder="e.g. 123-456-789-000">
                                    </div>

                                    <div class="col-12">
                                        <label for="business_address" class="form-label">Business address</label>
                                        <input id="business_address" name="business_address" type="text" class="form-control" value="{{ old('business_address', $business->city ? $business->city : '') }}" placeholder="Complete business address" required>
                                    </div>

                                    <div class="col-12">
                                        <label for="contact_number" class="form-label">Business contact number</label>
                                        <input id="contact_number" name="contact_number" type="text" class="form-control" value="{{ old('contact_number') }}" placeholder="e.g. +63 917 123 4567" required>
                                    </div>

                                    <div class="col-12">
                                        <label for="permit_issuer" class="form-label">Permit / License issuing office</label>
                                        <input id="permit_issuer" name="permit_issuer" type="text" class="form-control" value="{{ old('permit_issuer') }}" placeholder="e.g. City Mayor's Office, Department of Trade and Industry">
                                    </div>

                                    <div class="col-12">
                                        <label for="permit_images" class="form-label">Permit / business registration images</label>
                                        <input id="permit_images" name="permit_images[]" type="file" class="form-control" accept="image/*" multiple required>
                                        <div class="form-text">Upload your business registration, mayor's permit, or other supporting permit photos.</div>
                                    </div>
                                </div>

                                <div class="mt-4 d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Save business profile</button>
                                    <a href="{{ route('business.register') }}" class="btn btn-outline-secondary">Back to registration</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer footer-alt">
        <p class="text-muted mb-0">{{ date('Y') }} &copy; Nogaru Car Rental</p>
    </footer>

    <script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/app.js') }}"></script>
</body>
</html>
