@extends('layouts.auth')

@section('title', 'Application Submitted')

@section('content')
<div class="card shadow-sm"><div class="card-body p-4 text-center"><i class="ti ti-clock-check fs-1 text-primary"></i><h1 class="h4 mt-3">Application submitted</h1><p class="text-muted">A system administrator will review your sales-agent application. You can log in after the account is approved.</p><a href="{{ route('login') }}" class="btn btn-primary">Return to login</a></div></div>
@endsection
