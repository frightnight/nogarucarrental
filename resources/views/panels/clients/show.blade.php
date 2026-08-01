@extends('layouts.theme')

@section('title', 'Customer Profile | Nogaru Car Rental')
@section('page-title', 'Customer Profile')
@section('page-actions')<a href="{{ route('business.clients.index') }}" class="btn btn-outline-secondary">Back to customers</a>@endsection

@section('content')
    @php($profile = $client->clientProfile)
    @php($documents = $profile?->identityDocuments)
    <div class="row g-3">
        <div class="col-lg-4"><div class="card"><div class="card-body"><h4>{{ $profile?->full_name ?? $client->name }}</h4><p class="text-muted">{{ $profile?->email_address ?? $client->email }}</p><dl class="mb-0"><dt>Address</dt><dd>{{ $profile?->permanent_address ?? 'Not provided' }}</dd><dt>Contact</dt><dd>{{ collect($profile?->mobile_numbers ?? [])->pluck('number')->join(', ') ?: 'Not provided' }}</dd></dl></div></div></div>
        <div class="col-lg-8"><div class="card"><div class="card-header"><h4 class="header-title mb-0">Driver's license and uploaded IDs</h4></div><div class="card-body"><div class="row g-3"><div class="col-md-6"><strong>License status</strong><p class="mb-0 text-muted">{{ $documents?->ltms_license_front_path && $documents?->ltms_license_back_path ? 'Front and back uploaded' : 'Not fully uploaded' }}</p></div><div class="col-md-6"><strong>Verification status</strong><p class="mb-0 text-muted">{{ $documents?->is_complete ? 'All self-drive documents uploaded' : 'Documents incomplete' }}</p></div></div><hr><div class="d-flex flex-wrap gap-2">@foreach(['valid_id_1_path' => 'Valid ID 1', 'valid_id_2_path' => 'Valid ID 2', 'ltms_license_front_path' => 'License front', 'ltms_license_back_path' => 'License back', 'proof_of_billing_path' => 'Proof of billing'] as $field => $label)@if($documents?->{$field})<a href="{{ asset('storage/'.$documents->{$field}) }}" target="_blank" class="btn btn-sm btn-outline-primary">{{ $label }}</a>@endif@endforeach</div>@if(! $documents)<p class="text-muted mb-0">No identification documents uploaded.</p>@endif</div></div></div>
    </div>
    <div class="card mt-3"><div class="card-header"><h4 class="header-title mb-0">Rental history</h4></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Booking</th><th>Vehicle</th><th>Rental period</th><th>Status</th><th></th></tr></thead><tbody>@forelse($bookings as $booking)<tr><td>#{{ $booking->id }}</td><td>{{ $booking->car?->car_model ?? $booking->preferred_vehicle }}</td><td>{{ $booking->pickup_date?->format('M d, Y') }} – {{ $booking->return_date?->format('M d, Y') }}</td><td><span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', $booking->status) }}</span></td><td><a href="{{ route('business.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-secondary">View booking</a></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">No rental history.</td></tr>@endforelse</tbody></table></div></div>
@endsection
