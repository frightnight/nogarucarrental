<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout | Nogaru</title>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="card mx-auto" style="max-width: 620px;">
        <div class="card-body p-4 p-lg-5">
            <p class="text-primary fw-semibold mb-1">With Driver</p>
            <h1 class="h3 fw-bold">Complete your checkout</h1>
            <p class="text-muted">{{ $booking->preferred_vehicle }} · {{ $booking->business->name }}</p>
            <div class="bg-light rounded-3 p-3 mb-4"><div class="d-flex justify-content-between"><span>Estimated rental</span><strong>{{ $booking->final_rate ? '₱'.number_format((float) $booking->final_rate, 2) : 'To be confirmed' }}</strong></div><div class="d-flex justify-content-between mt-2"><span>Reservation payment</span><strong>{{ $booking->reservation_fee ? '₱'.number_format((float) $booking->reservation_fee, 2) : 'To be confirmed' }}</strong></div></div>
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form method="POST" action="{{ route('guest-checkout.payment', ['booking' => $booking, 'token' => $token]) }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label">Payment Method</label><select class="form-select mb-3" name="payment_method" required><option value="gcash">GCash</option><option value="paymaya">PayMaya</option><option value="bank_transaction">Bank Transaction</option></select>
                <label class="form-label">Reservation Payment Screenshot</label><input class="form-control mb-4" type="file" name="payment_proof" accept="image/*" required>
                <button class="btn btn-primary w-100" type="submit">Submit Payment</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>
