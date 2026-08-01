@extends('layouts.theme')

@section('title', 'Profile Summary | Nogaru Car Rental')
@section('page-title', 'Profile Summary')
@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('client.profile.edit') }}" class="btn btn-outline-primary btn-sm me-1">
                                        <i class="ti ti-edit me-1"></i> Edit Profile
                                    </a>
                                    <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary btn-sm me-1">
                                        <i class="ti ti-arrow-left me-1"></i> Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">Profile Summary</h4>
                                <p class="text-muted mb-0">Review your profile information and document status.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Personal Info Card -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Personal Information</h5>
                                    <a href="{{ route('client.profile.edit') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th class="ps-0" style="width: 140px;">Full Name</th>
                                            <td>{{ $profile->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Address</th>
                                            <td>{{ $profile->permanent_address ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Email</th>
                                            <td>{{ $profile->email_address ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Mobile Numbers</th>
                                            <td>
                                                @if(!empty($profile->mobile_numbers))
                                                    @foreach($profile->mobile_numbers as $mobile)
                                                        <span class="badge bg-info me-1 mb-1">
                                                            {{ $mobile['telecom'] ?? 'N/A' }}: {{ $mobile['number'] ?? 'N/A' }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Facebook</th>
                                            <td>
                                                @if($profile->facebook_url)
                                                    <a href="{{ $profile->facebook_url }}" target="_blank">{{ $profile->facebook_url }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">WhatsApp</th>
                                            <td>{{ $profile->whatsapp_number ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Viber</th>
                                            <td>{{ $profile->viber_number ?: '—' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Status Card -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Self-Drive Documents</h5>
                                    <a href="{{ route('client.profile.documents') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-file"></i> Manage
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($profile->identityDocuments)
                                        @php $docs = $profile->identityDocuments; @endphp
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <th class="ps-0">Valid ID #1</th>
                                                <td>{!! $docs->valid_id_1_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0">Valid ID #2</th>
                                                <td>{!! $docs->valid_id_2_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0">Selfie with IDs</th>
                                                <td>{!! $docs->selfie_with_ids_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0">LTMS - Welcome</th>
                                                <td>{!! $docs->ltms_welcome_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0">LTMS - Client ID</th>
                                                <td>{!! $docs->ltms_client_id_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0">LTMS - License Front</th>
                                                <td>{!! $docs->ltms_license_front_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0">LTMS - License Back</th>
                                                <td>{!! $docs->ltms_license_back_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0">Proof of Billing</th>
                                                <td>{!! $docs->proof_of_billing_path ? '<span class="badge bg-success">Uploaded</span>' : '<span class="badge bg-secondary">Missing</span>' !!}</td>
                                            </tr>
                                            <tr class="border-top">
                                                <th class="ps-0">Overall Status</th>
                                                <td>
                                                    @if($docs->is_complete)
                                                        <span class="badge bg-success fs-6">Complete</span>
                                                    @else
                                                        <span class="badge bg-warning fs-6">Incomplete</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="ti ti-file-off text-muted fs-1"></i>
                                            <p class="text-muted mt-2">No documents uploaded yet.</p>
                                            <a href="{{ route('client.profile.documents') }}" class="btn btn-primary btn-sm">
                                                <i class="ti ti-upload me-1"></i> Upload Documents
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
@endsection

