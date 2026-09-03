@extends('layouts.theme')
@section('title', 'Payment Methods | Nogaru Car Rental')
@section('page-title', 'Payment Methods')
@section('content')
    <div class="row g-4">
        <div class="col-lg-5"><div class="card"><div class="card-body">
            <h4 class="header-title mb-3">Add Payment Method</h4>
            <form method="POST" action="{{ route('business.payment-methods.store') }}" enctype="multipart/form-data">@csrf
                <div class="mb-3"><label class="form-label">Payment Method</label><input class="form-control" name="payment_method" value="{{ old('payment_method') }}" placeholder="e.g. GCash or BDO" required></div>
                <div class="mb-3"><label class="form-label">Account Type</label><input class="form-control" name="account_type" value="{{ old('account_type') }}" placeholder="e.g. E-Wallet or Bank Transfer" required></div>
                <div class="mb-3"><label class="form-label">Account Name</label><input class="form-control" name="account_name" value="{{ old('account_name') }}" required></div>
                <div class="mb-3"><label class="form-label">Account Number</label><input class="form-control" name="account_number" value="{{ old('account_number') }}" required></div>
                <div class="mb-3"><label class="form-label">Swift Code</label><input class="form-control" name="swift_code" value="{{ old('swift_code') }}"></div>
                <div class="mb-3"><label class="form-label">Account Type</label><select class="form-select" name="account_category" required><option value="Savings">Savings</option><option value="Checking">Checking</option></select></div>
                <div class="mb-3"><label class="form-label">Currency</label><input class="form-control" name="currency" value="{{ old('currency', 'PHP') }}" required></div>
                <div class="mb-3"><label class="form-label">QR Code</label><input class="form-control" type="file" name="qr_code" accept="image/*"></div>
                <button class="btn btn-primary" type="submit">Save Payment Method</button>
            </form>
        </div></div></div>
        <div class="col-lg-7"><div class="card"><div class="card-body"><h4 class="header-title mb-3">Configured Methods</h4>
            @forelse($paymentMethods as $paymentMethod)<div class="border rounded p-3 mb-3 d-flex justify-content-between gap-3"><div><strong>{{ $paymentMethod->payment_method }}</strong><span class="badge bg-light text-dark ms-2">{{ $paymentMethod->account_type }}</span><div class="small text-muted mt-2">{{ $paymentMethod->account_name }} · {{ $paymentMethod->account_number }} · {{ $paymentMethod->account_category }} · {{ $paymentMethod->currency }}</div></div><div class="d-flex gap-2"><a class="btn btn-outline-primary btn-sm" href="{{ route('business.payment-methods.edit', $paymentMethod) }}">Edit</a><form method="POST" action="{{ route('business.payment-methods.destroy', $paymentMethod) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Remove</button></form></div></div>@empty<p class="text-muted mb-0">No payment methods configured yet.</p>@endforelse
        </div></div></div>
    </div>
@endsection
