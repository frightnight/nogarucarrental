@extends('layouts.theme')

@section('title', 'Application Submitted | Nogaru Car Rental')
@section('page-title', 'Application Submitted')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><div class="card"><div class="card-body text-center py-5"><i class="ti ti-check text-success display-4"></i><h3 class="mt-3">Application received</h3><p class="text-muted">Your driver application <strong>{{ $driver->driver_code }}</strong> is waiting for administrator review. You will be able to sign in after approval.</p><a href="{{ route('login') }}" class="btn btn-primary">Go to sign in</a></div></div></div></div>
@endsection
