@extends('layouts.theme')
@section('title', 'Client Dashboard | Nogaru Car Rental')
@section('page-title', 'Client Dashboard')
@section('content')
    @foreach($notifications as $notification)
        <div class="alert alert-info"><a href="{{ $notification->data['url'] }}" class="alert-link">{{ $notification->data['message'] }}</a></div>
    @endforeach
    <div class="row">
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-car widget-icon float-end text-success"></i><h5 class="text-muted fw-normal mt-0">Bookings</h5><h3 class="mt-3 mb-3">{{ $bookings->count() }}</h3><p class="mb-0 text-muted">All reservations</p></div></div></div>
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-calendar-check widget-icon float-end text-primary"></i><h5 class="text-muted fw-normal mt-0">Upcoming</h5><h3 class="mt-3 mb-3">{{ $bookings->whereIn('status', ['finalized', 'payment_submitted', 'confirmed'])->count() }}</h3><p class="mb-0 text-muted">Active reservations</p></div></div></div>
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-clock widget-icon float-end text-warning"></i><h5 class="text-muted fw-normal mt-0">Pending</h5><h3 class="mt-3 mb-3">{{ $bookings->where('status', 'pending_review')->count() }}</h3><p class="mb-0 text-muted">Awaiting review</p></div></div></div>
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-bell widget-icon float-end text-info"></i><h5 class="text-muted fw-normal mt-0">Notifications</h5><h3 class="mt-3 mb-3">{{ $notifications->count() }}</h3><p class="mb-0 text-muted">Unread updates</p></div></div></div>
    </div>
    <div class="row">
        <div class="col-lg-8"><div class="card"><div class="card-header d-flex justify-content-between align-items-center"><h4 class="header-title">My Bookings</h4><a href="{{ route('businesses.index') }}" class="btn btn-sm btn-primary">Book a Car</a></div><div class="table-responsive"><table class="table table-hover table-centered mb-0"><thead><tr><th>Reference</th><th>Business</th><th>Vehicle</th><th>Schedule</th><th>Status</th><th></th></tr></thead><tbody>@forelse($bookings as $booking)<tr><td>#{{ $booking->id }}</td><td>{{ $booking->business->name }}</td><td>{{ $booking->preferred_vehicle }}</td><td>{{ $booking->pickup_date?->format('M d, Y') }}</td><td><span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', $booking->status) }}</span></td><td><a href="{{ route('bookings.review', $booking) }}" class="btn btn-sm btn-light">View</a></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">You have no bookings yet.</td></tr>@endforelse</tbody></table></div></div></div>
        <div class="col-lg-4"><div class="card"><div class="card-body"><h4 class="header-title">Quick actions</h4><a href="{{ route('businesses.index') }}" class="btn btn-success w-100 mb-2"><i class="ti ti-car me-1"></i>Browse vehicles</a><a href="{{ route('client.profile.edit') }}" class="btn btn-outline-secondary w-100 mb-2">Update profile</a><a href="{{ route('client.profile.documents') }}" class="btn btn-outline-secondary w-100">Self-drive requirements</a></div></div></div>
    </div>
@endsection
