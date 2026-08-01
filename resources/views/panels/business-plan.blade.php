@extends('layouts.theme')

@section('title', 'Plan & Billing | Nogaru Car Rental')
@section('page-title', 'Plan & Billing')
@section('content')
    <div class="alert alert-info">Your current plan is <strong>{{ ($business->plan ?? \App\BusinessPlan::Free)->label() }}</strong>. Plan changes are managed by the platform administrator while payment integration is being set up.</div>
    <div class="row g-3">
        @foreach($plans as $plan)
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 {{ $business->plan === $plan ? 'border-primary' : '' }}">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between"><h4>{{ $plan->label() }}</h4>@if($business->plan === $plan)<span class="badge bg-primary">Current</span>@endif</div>
                        <h3 class="my-3">{{ $plan->price() }}</h3>
                        <p class="text-muted">{{ $plan->vehicleLimit() ? 'Up to '.$plan->vehicleLimit().' vehicles' : 'Unlimited vehicles' }}</p>
                        <ul class="small ps-3 mb-4">
                            <li>Basic fleet and booking management</li>
                            @foreach($plan->includedFeatures() as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <div class="mt-auto">
                            @if($business->plan === $plan)
                                <button class="btn btn-outline-secondary w-100" disabled>Current plan</button>
                            @else
                                <button class="btn btn-outline-primary w-100" disabled>Contact admin to change</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
