@extends('layouts.theme')

@section('title', 'CRM | Nogaru Car Rental')
@section('page-title', 'CRM')
@section('page-actions')
    <a href="{{ route('business.clients.index') }}" class="btn btn-outline-secondary"><i class="ti ti-users me-1"></i>Customer directory</a>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card h-100"><div class="card-body"><span class="text-muted">Customers</span><h2 class="mt-2 mb-0">{{ $metrics['customers'] }}</h2><small class="text-muted">Matching your search</small></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card h-100"><div class="card-body"><span class="text-muted">Repeat customers</span><h2 class="mt-2 mb-0">{{ $metrics['repeat_customers'] }}</h2><small class="text-muted">More than one rental</small></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card h-100"><div class="card-body"><span class="text-muted">Active rentals</span><h2 class="mt-2 mb-0">{{ $metrics['active_rentals'] }}</h2><small class="text-muted">Reserved or in progress</small></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card h-100"><div class="card-body"><span class="text-muted">Lifetime revenue</span><h2 class="mt-2 mb-0">₱{{ number_format($metrics['lifetime_revenue'], 2) }}</h2><small class="text-muted">From recorded bookings</small></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-3"><div><h4 class="header-title mb-1">Customer relationships</h4><p class="text-muted mb-0">Search contact details and rental engagement.</p></div><form method="GET" class="d-flex gap-2"><label for="crm-search" class="visually-hidden">Search customers</label><input id="crm-search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Name, email, or phone"><button class="btn btn-sm btn-primary" type="submit"><i class="ti ti-search"></i></button></form></div>
                <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Customer</th><th>Contact</th><th>Rentals</th><th>Last activity</th><th></th></tr></thead><tbody>@forelse($clients as $client)<tr><td><strong>{{ $client->clientProfile?->full_name ?? $client->name }}</strong><small class="d-block text-muted">{{ $client->clientProfile?->email_address ?? $client->email }}</small></td><td>{{ collect($client->clientProfile?->mobile_numbers ?? [])->pluck('number')->join(', ') ?: 'No phone' }}</td><td><span class="badge bg-primary-subtle text-primary">{{ $client->rental_history_count }}</span></td><td>{{ $client->last_booking?->pickup_date?->format('M d, Y') ?? 'No date' }}</td><td><a href="{{ route('business.clients.show', $client) }}" class="btn btn-sm btn-outline-primary" title="View customer"><i class="ti ti-arrow-up-right"></i></a></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">No customers match this search.</td></tr>@endforelse</tbody></table></div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card h-100"><div class="card-header"><h4 class="header-title mb-0">Recent activity</h4></div><div class="list-group list-group-flush">@forelse($recentBookings as $booking)<a href="{{ route('business.bookings.edit', $booking) }}" class="list-group-item list-group-item-action"><div class="d-flex justify-content-between gap-3"><div><strong>{{ $booking->user?->clientProfile?->full_name ?? $booking->user?->name ?? 'Guest renter' }}</strong><small class="d-block text-muted">{{ $booking->car?->car_model ?? $booking->preferred_vehicle }}</small></div><span class="badge bg-primary-subtle text-primary align-self-start">{{ str_replace('_', ' ', $booking->status) }}</span></div><small class="d-block text-muted mt-2">{{ $booking->pickup_date?->format('M d, Y') }} · ₱{{ number_format((float) ($booking->final_rate ?? $booking->initial_rate ?? 0), 2) }}</small></a>@empty<div class="list-group-item text-muted">No booking activity yet.</div>@endforelse</div></div>
        </div>
    </div>
@endsection