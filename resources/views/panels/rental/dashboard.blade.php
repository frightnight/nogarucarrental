@extends('layouts.theme')

@section('title', 'Business Dashboard | Nogaru Car Rental')
@section('page-title', 'Business Dashboard')
@section('page-actions')
    <a href="{{ route('business.bookings.create') }}" class="btn btn-primary"><i class="ti ti-calendar-plus me-1"></i> New Booking</a>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Today's bookings</p><h2 class="mb-0">{{ $todayBookings->count() }}</h2></div></div></div>
        <div class="col-md-4"><div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Available vs. rented</p><h2 class="mb-0">{{ max($vehicleCount - $rentedCount, 0) }} <small class="fs-6 text-muted">available</small> / {{ $rentedCount }} <small class="fs-6 text-muted">rented</small></h2></div></div></div>
        <div class="col-md-4"><div class="card h-100"><div class="card-body"><p class="text-muted mb-1">Upcoming returns</p><h2 class="mb-0">{{ $upcomingReturns->count() }}</h2></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7"><div class="card h-100"><div class="card-header d-flex justify-content-between"><h4 class="header-title mb-0">Today's bookings</h4><a href="{{ route('business.bookings.index') }}">View all</a></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Customer</th><th>Vehicle</th><th>Pickup</th><th>Status</th></tr></thead><tbody>@forelse($todayBookings as $booking)@php($renterName = $booking->user?->name ?? trim($booking->guest_first_name.' '.$booking->guest_last_name) ?: 'Guest renter')<tr><td>{{ $renterName }}</td><td>{{ $booking->car?->car_model ?? $booking->preferred_vehicle }}</td><td>{{ $booking->pickup_time }}</td><td><span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', $booking->status) }}</span></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No bookings scheduled today.</td></tr>@endforelse</tbody></table></div></div></div>
        <div class="col-lg-5"><div class="card h-100"><div class="card-header"><h4 class="header-title mb-0">Upcoming returns</h4></div><div class="list-group list-group-flush">@forelse($upcomingReturns as $booking)@php($renterName = $booking->user?->name ?? trim($booking->guest_first_name.' '.$booking->guest_last_name) ?: 'Guest renter')<div class="list-group-item"><div class="fw-semibold">{{ $booking->car?->car_model ?? $booking->preferred_vehicle }}</div><small class="text-muted">{{ $renterName }} · {{ $booking->return_date?->format('M d') }} {{ $booking->return_time }}</small></div>@empty<div class="list-group-item text-muted">No upcoming returns.</div>@endforelse</div></div></div>
    </div>

    @if($expiringVehicles->isNotEmpty())
        <div class="alert alert-warning mt-3 mb-0"><strong>Expiry reminders:</strong> {{ $expiringVehicles->pluck('car_model')->filter()->join(', ') }} have registration or insurance due within 30 days. <a href="{{ route('business.fleet.index') }}" class="alert-link">Review fleet</a>.</div>
    @endif
@endsection
