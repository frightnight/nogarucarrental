@extends('layouts.theme')

@section('title', 'Quotation '.$quotation->quotation_number.' | Nogaru Car Rental')
@section('page-title', 'Quotation '.$quotation->quotation_number)
@section('page-actions')<a href="{{ route('business.quotations.index') }}" class="btn btn-outline-secondary me-2">Back</a><a href="{{ route('business.quotations.index', ['edit' => $quotation->id]) }}" class="btn btn-outline-primary me-2"><i class="ti ti-edit me-1"></i>Edit</a><button class="btn btn-primary" onclick="window.print()"><i class="ti ti-printer me-1"></i>Print preview</button>@endsection

@section('content')
    <link href="{{ asset('theme/assets/plugins/quill/quill.snow.css') }}" rel="stylesheet">
    <style>
        .quotation-intro-grid { display: grid; gap: 1.5rem; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
        .quotation-address { font-size: 80%; }
        @media (max-width: 767.98px) { .quotation-intro-grid { grid-template-columns: 1fr; } }
        @media print { html { font-size: 80%; } .app-topbar, .app-menu, .page-title-box, .footer { display: none !important; } .content-page { margin-left: 0 !important; } .card { border: 0 !important; box-shadow: none !important; } }
    </style>
    <div class="card"><div class="card-body p-md-5">
        <div class="d-flex justify-content-between mb-3"><div><h2 class="mb-1">{{ $quotation->business->name }}</h2><p class="text-muted mb-0">Car rental quotation</p></div><div class="text-end"><h4 class="mb-1">{{ $quotation->quotation_number }}</h4><p class="text-muted mb-0">Quotation date: {{ $quotation->quotation_date?->format('M d, Y') ?? $quotation->created_at->format('M d, Y') }}</p></div></div>
        <div class="quotation-intro-grid mb-4">
            <div><h3 class="mb-1">{{ $quotation->title }}</h3>@if($quotation->client_name)<p class="text-muted mb-0">Prepared for: {{ $quotation->client_name }}</p>@endif</div>
            <div class="row g-3"><div class="col-6"><h5>Package</h5><p class="mb-1"><strong>{{ $quotation->package_type === 'all_in' ? 'ALL IN PACKAGE' : 'ALL OUT PACKAGE' }}</strong></p><p class="text-muted mb-0">{{ $quotation->package_type === 'all_in' ? 'Fuel / gas consumption included; based on the listed itinerary.' : 'Fuel / gas consumption is not included; ideal for flexible trips.' }}</p></div><div class="col-6"><h5>Vehicle and driver</h5><p class="mb-1">{{ $quotation->car->car_model }} <span class="text-muted">({{ $quotation->car->fuel_type }})</span></p><p class="text-muted mb-0">Driver: {{ $quotation->driver?->full_name ?? 'Not included' }}</p></div></div>
        </div>
        <h5>Itinerary</h5><ol class="mb-4">@foreach($quotation->itinerary as $stop)<li class="mb-2">@if(!empty($stop['title']))<strong class="d-block">{{ $stop['title'] }}</strong>@endif<span class="quotation-address">{{ \Illuminate\Support\Str::limit($stop['address'], 50, '...') }}</span></li>@endforeach</ol>
        @if(!empty($quotation->other_payments))<h5>Other Payments</h5><ol class="mb-4">@foreach($quotation->other_payments as $payment)<li class="mb-1"><span>{{ $payment['name'] }}</span><strong class="float-end">₱{{ number_format((float) $payment['amount'], 2) }}</strong></li>@endforeach</ol>@endif
        @if($quotation->footnote_content)<div class="border-top pt-4 mb-4"><div class="ql-editor p-0">{!! $quotation->footnote_content !!}</div></div>@endif
        @php($displayVehicleRate = (float) $quotation->vehicle_rate + (float) $quotation->driver_rate + (float) $quotation->distance_rate + (float) $quotation->hidden_charges)
        @php($otherPaymentsTotal = collect($quotation->other_payments ?? [])->sum(fn (array $payment): float => (float) ($payment['amount'] ?? 0)))
        <div class="row justify-content-end"><div class="col-md-6"><div class="border rounded p-3"><div class="d-flex justify-content-between mb-2"><span>Vehicle rate{{ $quotation->vehicle_rate_name ? ' ('.$quotation->vehicle_rate_name.')' : '' }}</span><strong>₱{{ number_format((float) $quotation->vehicle_rate, 2) }}</strong></div><div class="d-flex justify-content-between mb-3"><span>Total itinerary distance</span><strong>{{ number_format((float) $quotation->total_distance_km, 2) }} km</strong></div><div class="d-flex justify-content-between mb-2"><span>Others</span><strong>₱{{ number_format($otherPaymentsTotal, 2) }}</strong></div><hr><div class="d-flex justify-content-between fs-4"><strong>Total amount</strong><strong>₱{{ number_format((float) $quotation->total_amount, 2) }}</strong></div></div></div></div>
    </div></div>
@endsection
