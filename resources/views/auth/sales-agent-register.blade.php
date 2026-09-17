@extends('layouts.auth')

@section('title', 'Sales Agent Registration')

@section('content')
<div class="card shadow-sm"><div class="card-body p-4">
    <h1 class="h4 mb-1">Apply as a sales agent</h1>
    <p class="text-muted mb-4">Your account will be reviewed and assigned to a rental business by a system administrator.</p>
    <form method="POST" action="{{ route('sales-agent.register.store') }}" class="vstack gap-3">
        @csrf
        <div><label class="form-label">Full name</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
        <div><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone') }}" required></div>
        <div><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div><label class="form-label">Confirm password</label><input type="password" name="password_confirmation" class="form-control" required></div>
        <button class="btn btn-primary" type="submit">Submit application</button>
    </form>
</div></div>
@endsection
