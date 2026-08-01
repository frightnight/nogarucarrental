@extends('layouts.theme')

@section('title', 'Self-Drive Documents | Nogaru Car Rental')
@section('page-title', 'Self-Drive Documents')
@section('content')
                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('client.profile.edit') }}" class="btn btn-outline-secondary btn-sm me-1">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">Self-Drive Requirements</h4>
                                <p class="text-muted mb-0">Upload the required documents for self-drive booking eligibility.</p>
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

                    <!-- Requirements Checklist -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-2">Required Documents for Self-Drive Rental</h5>
                                    <p class="text-muted mb-0">All documents must be clear, legible, and up-to-date. Each image should be in JPEG, PNG, or WebP format (max 5MB each).</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('client.profile.documents.upload') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Section 1: Valid IDs -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-primary-subtle">
                                        <h5 class="mb-0">1. Two (2) Valid ID's</h5>
                                        <small class="text-muted">Must show your current address. These IDs will be surrendered upon delivery.</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Valid ID #1 <span class="text-danger">*</span></label>
                                                <div class="border rounded p-3 text-center bg-light">
                                                    @if($documents->valid_id_1_path)
                                                        <div class="mb-2">
                                                            <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                                            <a href="{{ asset('storage/' . $documents->valid_id_1_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                                <i class="ti ti-eye"></i> View
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <input class="form-control @error('valid_id_1') is-invalid @enderror"
                                                           type="file" name="valid_id_1" accept="image/jpeg,image/png,image/webp">
                                                    <small class="text-muted">Government-issued ID with current address</small>
                                                    @error('valid_id_1')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Valid ID #2 <span class="text-danger">*</span></label>
                                                <div class="border rounded p-3 text-center bg-light">
                                                    @if($documents->valid_id_2_path)
                                                        <div class="mb-2">
                                                            <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                                            <a href="{{ asset('storage/' . $documents->valid_id_2_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                                <i class="ti ti-eye"></i> View
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <input class="form-control @error('valid_id_2') is-invalid @enderror"
                                                           type="file" name="valid_id_2" accept="image/jpeg,image/png,image/webp">
                                                    <small class="text-muted">Another valid ID showing your address</small>
                                                    @error('valid_id_2')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Selfie with IDs -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-primary-subtle">
                                        <h5 class="mb-0">2. Selfie with ID's (Clear Copy)</h5>
                                        <small class="text-muted">A clear photo of yourself holding your identification.</small>
                                    </div>
                                    <div class="card-body">
                                        @if($documents->selfie_with_ids_path)
                                            <div class="mb-2">
                                                <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                                <a href="{{ asset('storage/' . $documents->selfie_with_ids_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                    <i class="ti ti-eye"></i> View
                                                </a>
                                            </div>
                                        @endif
                                        <input class="form-control @error('selfie_with_ids') is-invalid @enderror"
                                               type="file" name="selfie_with_ids" accept="image/jpeg,image/png,image/webp">
                                        <small class="text-muted">Hold your IDs clearly visible next to your face</small>
                                        @error('selfie_with_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: LTMS Screenshots -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-primary-subtle">
                                        <h5 class="mb-0">3. LTMS Screenshots (4 total)</h5>
                                        <small class="text-muted">
                                            Log in/Register at the
                                            <a href="https://portal.lto.gov.ph/" target="_blank">LTO Portal</a>
                                            and provide the following screenshots:
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Welcome Screen (with your name)</label>
                                                <div class="border rounded p-3 text-center bg-light">
                                                    @if($documents->ltms_welcome_path)
                                                        <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                                        <a href="{{ asset('storage/' . $documents->ltms_welcome_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2"><i class="ti ti-eye"></i></a>
                                                    @endif
                                                    <input class="form-control @error('ltms_welcome') is-invalid @enderror" type="file" name="ltms_welcome" accept="image/jpeg,image/png,image/webp">
                                                    @error('ltms_welcome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">LTO Client ID</label>
                                                <div class="border rounded p-3 text-center bg-light">
                                                    @if($documents->ltms_client_id_path)
                                                        <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                                        <a href="{{ asset('storage/' . $documents->ltms_client_id_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2"><i class="ti ti-eye"></i></a>
                                                    @endif
                                                    <input class="form-control @error('ltms_client_id') is-invalid @enderror" type="file" name="ltms_client_id" accept="image/jpeg,image/png,image/webp">
                                                    @error('ltms_client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Front of License (in portal)</label>
                                                <div class="border rounded p-3 text-center bg-light">
                                                    @if($documents->ltms_license_front_path)
                                                        <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                                        <a href="{{ asset('storage/' . $documents->ltms_license_front_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2"><i class="ti ti-eye"></i></a>
                                                    @endif
                                                    <input class="form-control @error('ltms_license_front') is-invalid @enderror" type="file" name="ltms_license_front" accept="image/jpeg,image/png,image/webp">
                                                    @error('ltms_license_front')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Back of License & QR Code (in portal)</label>
                                                <div class="border rounded p-3 text-center bg-light">
                                                    @if($documents->ltms_license_back_path)
                                                        <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                                        <a href="{{ asset('storage/' . $documents->ltms_license_back_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2"><i class="ti ti-eye"></i></a>
                                                    @endif
                                                    <input class="form-control @error('ltms_license_back') is-invalid @enderror" type="file" name="ltms_license_back" accept="image/jpeg,image/png,image/webp">
                                                    @error('ltms_license_back')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Proof of Billing -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-primary-subtle">
                                        <h5 class="mb-0">4. Latest Proof of Billing</h5>
                                        <small class="text-muted">Ensure the document is recent and legible.</small>
                                    </div>
                                    <div class="card-body">
                                        @if($documents->proof_of_billing_path)
                                            <span class="badge bg-success"><i class="ti ti-check"></i> Uploaded</span>
                                            <a href="{{ asset('storage/' . $documents->proof_of_billing_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2 mb-2"><i class="ti ti-eye"></i> View</a>
                                        @endif
                                        <input class="form-control @error('proof_of_billing') is-invalid @enderror"
                                               type="file" name="proof_of_billing" accept="image/jpeg,image/png,image/webp">
                                        <small class="text-muted">Electric bill, water bill, or any recent utility bill (within 3 months)</small>
                                        @error('proof_of_billing')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($documents && $documents->allDocumentsUploaded())
                                                <span class="badge bg-success fs-6 p-2">
                                                    <i class="ti ti-check-circle me-1"></i> All documents uploaded!
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="ti ti-info-circle me-1"></i>
                                                    Upload all required documents to qualify for self-drive rentals.
                                                </span>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="ti ti-cloud-upload me-1"></i> Upload Documents
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
@endsection

