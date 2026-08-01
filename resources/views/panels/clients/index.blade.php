@extends('layouts.theme')
@section('title', 'Business Clients | Nogaru Car Rental')
@section('page-title', 'Clients')
@section('page-actions')
    <a href="{{ route('business.bookings.index') }}" class="btn btn-outline-secondary">View Bookings</a>
@endsection
@section('content')
    <div class="row">
        @forelse($clients as $client)
            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar-lg mx-auto mb-3"><span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-24">{{ strtoupper(substr($client->name, 0, 1)) }}</span></div>
                        <h4 class="mb-1">{{ $client->clientProfile?->full_name ?? $client->name }}</h4>
                        <p class="text-muted mb-3">{{ $client->clientProfile?->email_address ?? $client->email }}</p>
                        @if($client->clientProfile?->mobile_numbers)
                            <p class="mb-3"><i class="ti ti-phone me-1"></i>{{ collect($client->clientProfile->mobile_numbers)->pluck('number')->implode(', ') }}</p>
                        @endif
                        <div class="border-top pt-3 mt-3 text-start small">
                            <div><strong>Driver's license:</strong> {{ $client->clientProfile?->identityDocuments?->ltms_license_front_path ? 'Uploaded' : 'Not uploaded' }}</div>
                            <div><strong>IDs:</strong> {{ $client->clientProfile?->identityDocuments?->is_complete ? 'Complete' : 'Incomplete' }}</div>
                            <div><strong>Rental history:</strong> {{ $client->rental_history_count }} booking(s)</div>
                        </div>
                        <span class="badge bg-success-subtle text-success mt-3">Client</span>
                        <div class="mt-3"><a href="{{ route('business.clients.show', $client) }}" class="btn btn-outline-primary btn-sm">View profile</a></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card"><div class="card-body text-center py-5 text-muted">Clients will appear here after they submit a booking request.</div></div></div>
        @endforelse
    </div>
@endsection
