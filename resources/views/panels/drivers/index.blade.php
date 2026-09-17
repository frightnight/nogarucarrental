@extends('layouts.theme')

@section('title', 'Approved Drivers | Nogaru Car Rental')
@section('page-title', 'Approved Drivers')
@section('page-actions')
    <a href="{{ route('business.bookings.index') }}" class="btn btn-primary"><i class="ti ti-calendar-check me-1"></i>Select for a booking</a>
    <a href="{{ route('business.fleet.index') }}" class="btn btn-outline-secondary"><i class="ti ti-car me-1"></i>Fleet</a>
@endsection

@section('content')
    <div class="alert alert-info"><i class="ti ti-info-circle me-1"></i> This directory shows drivers approved by the system administrator. To choose a driver, open a confirmed booking and select from its driver applicants.</div>
    <div class="row g-4">
        @forelse ($drivers as $driver)
            <div class="col-xl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div><h4 class="mb-1">{{ $driver->full_name }}</h4><p class="text-muted mb-0">{{ $driver->driver_code ?: $driver->license_number }}</p></div>
                            <span class="badge bg-success-subtle text-success">Approved</span>
                        </div>
                        <dl class="row small mb-3">
                            <dt class="col-5">License</dt><dd class="col-7">{{ $driver->license_number }}</dd>
                            <dt class="col-5">Classification</dt><dd class="col-7">{{ $driver->license_type ?: 'Not specified' }}</dd>
                            <dt class="col-5">Experience</dt><dd class="col-7">{{ $driver->years_driving_experience ?: 0 }} years</dd>
                            <dt class="col-5">Contact</dt><dd class="col-7">{{ $driver->phone ?: $driver->email ?: 'Not provided' }}</dd>
                        </dl>
                        <div class="border-top pt-3"><strong class="small">Published trip rates</strong>@forelse($driver->rates as $rate)<div class="d-flex justify-content-between small mt-2"><span>{{ str($rate->trip_type)->replace('_', ' ')->headline() }}</span><span>₱{{ number_format((float) $rate->amount, 2) }} <span class="text-muted">{{ str($rate->rate_type)->replace('_', ' ') }}</span></span></div>@empty<p class="small text-muted mt-2 mb-0">No published rates.</p>@endforelse</div>
                        <a href="{{ route('business.drivers.show', $driver) }}" class="btn btn-outline-primary btn-sm mt-3">View profile</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card"><div class="card-body text-center py-5 text-muted">No administrator-approved drivers are available.</div></div></div>
        @endforelse
    </div>
@endsection
