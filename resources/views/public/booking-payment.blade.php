<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Booking</title>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center g-4"><div class="col-lg-7">
        <div class="card"><div class="card-body p-4">
            <h2 class="h3">Payment for {{ $booking->preferred_vehicle }}</h2>
            <p class="text-muted">{{ ucfirst(str_replace('_', ' ', $booking->rental_type)) }} · {{ $booking->business->name }}</p>
            <div class="border rounded p-3 mb-4"><h5 class="mb-3">Rental Details</h5><div class="row g-3"><div class="col-md-6"><span class="small text-muted d-block">Pick-up</span><strong>{{ $booking->pickup_date?->format('M d, Y') }} · {{ $booking->pickup_time }}</strong></div><div class="col-md-6"><span class="small text-muted d-block">Return</span><strong>{{ $booking->return_date?->format('M d, Y') }} · {{ $booking->return_time }}</strong></div><div class="col-md-6"><span class="small text-muted d-block">Handover</span><strong>{{ ucfirst(str_replace('_', ' ', $booking->handover_option)) }}</strong></div><div class="col-md-6"><span class="small text-muted d-block">Return Location</span><strong>{{ $booking->return_location ?: 'To be arranged' }}</strong></div></div></div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($paymentMethods->isEmpty())<div class="alert alert-warning mb-0">This rental business has not configured payment methods yet. Please contact them to continue.</div>@else
            <form method="POST" action="{{ route('bookings.payment.store', $booking) }}" enctype="multipart/form-data">
                @csrf
                <h5 class="mb-3">Payment</h5>
                @include('public.partials.payment-method-fields')

                <label class="form-label">Special Request <span class="text-muted">(optional)</span></label>
                <textarea class="form-control mb-3" name="special_request" rows="3">{{ old('special_request', $booking->special_request) }}</textarea>

                <label class="form-label">Flight Details <span class="text-muted">(optional image)</span></label>
                <input class="form-control mb-3" type="file" name="flight_details" accept="image/*">

                <div class="row g-3"><div class="col-md-6"><label class="form-label">Security Deposit</label><input class="form-control" value="₱{{ number_format((float) $booking->reservation_fee, 2) }}" readonly><small class="text-muted">20% of the total, rounded to the nearest hundred.</small></div><div class="col-md-6"><label class="form-label">Reference Number</label><input class="form-control" name="payment_reference_number" value="{{ old('payment_reference_number') }}" placeholder="Transaction / Ref #" required></div></div>
                <label class="form-label mt-3">Upload Receipt / Proof of Payment</label><input class="form-control mb-3" type="file" name="payment_proof" accept="image/jpeg,image/png,image/webp,application/pdf" required>

                <button class="btn btn-primary">Submit Final Confirmation</button>
            </form>@endif
        </div></div>
    </div><aside class="col-lg-4"><div class="card"><div class="card-body"><h5>Price Summary</h5><div class="d-flex justify-content-between"><span>Total</span><strong>₱{{ number_format((float) $booking->final_rate, 2) }}</strong></div><div class="d-flex justify-content-between text-danger mt-2"><span>Security Deposit</span><strong>₱{{ number_format((float) $booking->reservation_fee, 2) }}</strong></div><hr><div class="d-flex justify-content-between fw-bold"><span>Remaining Balance</span><span>₱{{ number_format(max(0, (float) $booking->final_rate - (float) $booking->reservation_fee), 2) }}</span></div></div></div></aside></div>
</div>
</body>
</html>
