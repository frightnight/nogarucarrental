@extends('layouts.theme')

@section('title', 'My Profile | Nogaru Car Rental')
@section('page-title', 'My Profile')
@section('content')
                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary btn-sm me-1">
                                        <i class="ti ti-arrow-left me-1"></i> Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">My Profile</h4>
                                <p class="text-muted mb-0">Manage your personal information and contact details.</p>
                            </div>
                        </div>
                    </div>

                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti ti-check-circle me-1"></i> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('client.profile.update') }}">
                                        @csrf

                                        <h5 class="mb-3">Personal Information</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input class="form-control @error('full_name') is-invalid @enderror"
                                                   type="text" name="full_name"
                                                   value="{{ old('full_name', $profile->full_name) }}" required>
                                            @error('full_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Permanent Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('permanent_address') is-invalid @enderror"
                                                      name="permanent_address" rows="3" required>{{ old('permanent_address', $profile->permanent_address) }}</textarea>
                                            @error('permanent_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <h5 class="mb-3 mt-4">Contact Information</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input class="form-control @error('email_address') is-invalid @enderror"
                                                   type="email" name="email_address"
                                                   value="{{ old('email_address', $profile->email_address) }}">
                                            @error('email_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                Mobile Numbers <span class="text-danger">*</span>
                                                <small class="text-muted">(Add at least one telecom and number)</small>
                                            </label>
                                            <div id="mobile-numbers-container">
                                                @php
                                                    $mobiles = old('mobile_numbers', $profile->mobile_numbers ?? []);
                                                @endphp
                                                @if(!empty($mobiles) && is_array($mobiles))
                                                    @foreach($mobiles as $index => $mobile)
                                                        <div class="row mb-2 mobile-number-row">
                                                            <div class="col-md-4">
                                                                <select class="form-select" name="mobile_numbers[{{ $index }}][telecom]">
                                                                    <option value="">Select Telecom</option>
                                                                    <option value="Globe" @selected(($mobile['telecom'] ?? '') === 'Globe')>Globe</option>
                                                                    <option value="Smart" @selected(($mobile['telecom'] ?? '') === 'Smart')>Smart</option>
                                                                    <option value="Sun" @selected(($mobile['telecom'] ?? '') === 'Sun')>Sun</option>
                                                                    <option value="Dito" @selected(($mobile['telecom'] ?? '') === 'Dito')>Dito</option>
                                                                    <option value="TM" @selected(($mobile['telecom'] ?? '') === 'TM')>TM</option>
                                                                    <option value="TNT" @selected(($mobile['telecom'] ?? '') === 'TNT')>TNT</option>
                                                                    <option value="Other" @selected(($mobile['telecom'] ?? '') === 'Other')>Other</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text"
                                                                       name="mobile_numbers[{{ $index }}][number]"
                                                                       placeholder="09XX-XXX-XXXX"
                                                                       value="{{ $mobile['number'] ?? '' }}">
                                                            </div>
                                                            <div class="col-md-2 d-flex align-items-center">
                                                                <button type="button" class="btn btn-outline-danger btn-sm remove-mobile" tabindex="-1">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="row mb-2 mobile-number-row">
                                                        <div class="col-md-4">
                                                            <select class="form-select" name="mobile_numbers[0][telecom]">
                                                                <option value="">Select Telecom</option>
                                                                <option value="Globe">Globe</option>
                                                                <option value="Smart">Smart</option>
                                                                <option value="Sun">Sun</option>
                                                                <option value="Dito">Dito</option>
                                                                <option value="TM">TM</option>
                                                                <option value="TNT">TNT</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input class="form-control" type="text"
                                                                   name="mobile_numbers[0][number]"
                                                                   placeholder="09XX-XXX-XXXX">
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-center">
                                                            <button type="button" class="btn btn-outline-success btn-sm add-mobile" tabindex="-1">
                                                                <i class="ti ti-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <small class="text-muted">Format: 0912-345-6789</small>
                                            @error('mobile_numbers')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <h5 class="mb-3 mt-4">Social / Messaging</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Facebook Account URL</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ti ti-brand-facebook"></i></span>
                                                <input class="form-control @error('facebook_url') is-invalid @enderror"
                                                       type="url" name="facebook_url"
                                                       value="{{ old('facebook_url', $profile->facebook_url) }}"
                                                       placeholder="https://facebook.com/username">
                                            </div>
                                            @error('facebook_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">WhatsApp Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="ti ti-brand-whatsapp"></i></span>
                                                    <input class="form-control @error('whatsapp_number') is-invalid @enderror"
                                                           type="text" name="whatsapp_number"
                                                           value="{{ old('whatsapp_number', $profile->whatsapp_number) }}"
                                                           placeholder="09XX-XXX-XXXX">
                                                </div>
                                                @error('whatsapp_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Viber Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="ti ti-message-circle"></i></span>
                                                    <input class="form-control @error('viber_number') is-invalid @enderror"
                                                           type="text" name="viber_number"
                                                           value="{{ old('viber_number', $profile->viber_number) }}"
                                                           placeholder="09XX-XXX-XXXX">
                                                </div>
                                                @error('viber_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-success">
                                                <i class="ti ti-device-floppy me-1"></i> Save Profile
                                            </button>
                                            <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-3">Profile Status</h5>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 me-2">
                                            @if($profile->isPersonalInfoComplete())
                                                <span class="badge bg-success rounded-pill fs-5"><i class="ti ti-check"></i></span>
                                            @else
                                                <span class="badge bg-warning rounded-pill fs-5"><i class="ti ti-alert-circle"></i></span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>Personal Info</strong>
                                            <small class="text-muted d-block">
                                                @if($profile->isPersonalInfoComplete())
                                                    Completed
                                                @else
                                                    Please fill out your details
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 me-2">
                                            @if($profile->isSelfDriveReady())
                                                <span class="badge bg-success rounded-pill fs-5"><i class="ti ti-check"></i></span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill fs-5"><i class="ti ti-x"></i></span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>Self-Drive Documents</strong>
                                            <small class="text-muted d-block">
                                                @if($profile->isSelfDriveReady())
                                                    Verified
                                                @else
                                                    <a href="{{ route('client.profile.documents') }}" class="text-decoration-underline">Upload documents</a>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <hr>
                                    <a href="{{ route('client.profile.documents') }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="ti ti-file me-1"></i> Manage Self-Drive Documents
                                    </a>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-3">Quick Links</h5>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="ti ti-dashboard me-1"></i> Dashboard
                                        </a>
                                        <a href="{{ route('client.profile.show') }}" class="btn btn-outline-info btn-sm">
                                            <i class="ti ti-eye me-1"></i> View Profile Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('mobile-numbers-container');
            let index = {{ !empty($mobiles) && is_array($mobiles) ? count($mobiles) : 1 }};

            container.addEventListener('click', function(e) {
                // Add mobile number row
                if (e.target.closest('.add-mobile')) {
                    const row = document.querySelector('.mobile-number-row').cloneNode(true);
                    const inputs = row.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        input.name = input.name.replace(/\d+/, index);
                        if (input.tagName === 'INPUT') input.value = '';
                        if (input.tagName === 'SELECT') input.selectedIndex = 0;
                    });
                    const actionBtn = row.querySelector('.col-md-2');
                    if (actionBtn) {
                        actionBtn.innerHTML = `
                            <button type="button" class="btn btn-outline-danger btn-sm remove-mobile" tabindex="-1">
                                <i class="ti ti-trash"></i>
                            </button>`;
                    }
                    container.appendChild(row);
                    index++;
                }

                // Remove mobile number row
                if (e.target.closest('.remove-mobile')) {
                    const rows = container.querySelectorAll('.mobile-number-row');
                    if (rows.length > 1) {
                        e.target.closest('.mobile-number-row').remove();
                    } else {
                        // Clear values instead of removing last row
                        const inputs = rows[0].querySelectorAll('input, select');
                        inputs.forEach(input => {
                            if (input.tagName === 'INPUT') input.value = '';
                            if (input.tagName === 'SELECT') input.selectedIndex = 0;
                        });
                    }
                }
            });
        });
    </script>
@endpush

