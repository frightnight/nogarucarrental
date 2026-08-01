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
        <div class="card-header"><h4 class="header-title">Booking Requests</h4></div>
        <div class="table-responsive">
            <table class="table table-hover table-centered mb-0">
                <thead><tr><th>Reference</th><th>Client</th><th>Vehicle</th><th>Schedule</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td class="fw-semibold">#{{ $booking->id }}</td>
                        <td><div class="d-flex align-items-center"><span class="avatar-sm me-2"><span class="avatar-title rounded-circle bg-primary-subtle text-primary">{{ strtoupper(substr($booking->user->name, 0, 1)) }}</span></span>{{ $booking->user->name }}</div></td>
                        <td>{{ $booking->preferred_vehicle }}</td>
                        <td>{{ $booking->pickup_date?->format('M d, Y') }}<small class="d-block text-muted">{{ $booking->pickup_time }}</small></td>
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
                    <tr><td colspan="6" class="text-center text-muted py-5">No booking requests yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
