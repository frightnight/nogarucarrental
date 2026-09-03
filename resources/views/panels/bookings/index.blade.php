@extends('layouts.theme')
@section('title', 'Bookings | Nogaru Car Rental')
@section('page-title', 'Incoming Bookings')
@section('page-actions')
    <a href="{{ route('business.bookings.create') }}" class="btn btn-primary">New Booking</a>
    <a href="{{ route('business.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
@endsection
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between"><h4 class="header-title mb-0">Booking Requests</h4><form method="GET"><label for="booking-sort" class="visually-hidden">Sort bookings</label><select id="booking-sort" name="sort" class="form-select form-select-sm" onchange="this.form.submit()"><option value="booking_input" @selected($sort === 'booking_input')>By booking input</option><option value="booking_schedule" @selected($sort === 'booking_schedule')>By booking schedule</option></select></form></div>
        <div class="table-responsive">
            <table class="table table-hover table-centered mb-0">
                <thead><tr><th>Reference</th><th>Renter</th><th>Vehicle</th><th>Schedule</th><th>Payment</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td class="fw-semibold">#{{ $booking->id }}</td>
                        @php($renterName = $booking->user?->name ?? trim($booking->guest_first_name.' '.$booking->guest_last_name) ?: 'Guest renter')
                        <td><div class="d-flex align-items-center"><span class="avatar-sm me-2"><span class="avatar-title rounded-circle bg-primary-subtle text-primary">{{ strtoupper(substr($renterName, 0, 1)) }}</span></span>{{ $renterName }}<small class="d-block text-muted">{{ $booking->user?->email ?? $booking->guest_email ?? 'Guest checkout' }}{{ $booking->guest_phone ? ' · '.$booking->guest_phone : '' }}</small></div></td>
                        <td>{{ $booking->car?->car_model ?: $booking->preferred_vehicle }}<small class="d-block text-muted">{{ $booking->car?->plate_number }}</small></td>
                        <td>{{ $booking->pickup_date?->format('M d, Y') }}<small class="d-block text-muted">{{ $booking->pickup_time }} – {{ $booking->return_date?->format('M d, Y') }} {{ $booking->return_time }}</small></td>
                        <td>@if($booking->payment_submitted_at)<strong>₱{{ number_format((float) $booking->reservation_fee, 2) }}</strong><small class="d-block text-muted">{{ $booking->businessPaymentMethod?->payment_method ?? $booking->payment_method }} · Ref: {{ $booking->payment_reference_number ?: '—' }}</small>@if($booking->payment_proof_path)<a class="small" href="{{ asset($booking->payment_proof_path) }}" target="_blank">View proof</a>@endif @else<span class="text-muted">Not submitted</span>@endif</td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', $booking->status) }}</span></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('business.bookings.agreement', $booking) }}">Agreement</a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('business.bookings.inspection', $booking) }}">Inspect</a>
                            @if(in_array($booking->status, ['reserved', 'confirmed', 'ongoing']))
                                <form method="POST" action="{{ route('business.bookings.status.update', $booking) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="reserved" @selected($booking->status === 'reserved')>Reserved</option>
                                        <option value="ongoing" @selected($booking->status === 'ongoing')>Ongoing</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </form>
                            @endif
                            <a class="btn btn-sm btn-primary" href="{{ route('business.bookings.edit', $booking) }}">{{ $booking->status === 'payment_submitted' ? 'Review Payment' : 'Review' }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No booking requests yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
