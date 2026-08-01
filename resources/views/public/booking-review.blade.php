<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Review</title>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2>Booking #{{ $booking->id }}</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card"><div class="card-body">
        <h5>{{ $booking->business->name }} · {{ $booking->preferred_vehicle }}</h5>
        <p><strong>Status:</strong> {{ str_replace('_', ' ', $booking->status) }}</p>
        <p><strong>Rental type:</strong> {{ str_replace('_', ' ', $booking->rental_type) }}</p>
        <p><strong>Itinerary:</strong> {{ $booking->destination_itinerary }}</p>

        @if ($booking->initial_rate)
            <p><strong>Initial self-drive estimate:</strong> ₱{{ number_format((float) $booking->initial_rate, 2) }}</p>
        @endif

        @if ($booking->final_rate)
            <hr>
            <h5>Final Booking Details</h5>
            <p><strong>Final rate:</strong> ₱{{ number_format((float) $booking->final_rate, 2) }}</p>
            @if ($booking->rental_type === 'self_drive')
                <p><strong>Delivery fee:</strong> ₱{{ number_format((float) $booking->delivery_fee, 2) }} · <strong>Pick-up fee:</strong> ₱{{ number_format((float) $booking->pickup_fee, 2) }}</p>
            @endif
            <p><strong>Reservation fee:</strong> ₱{{ number_format((float) $booking->reservation_fee, 2) }}</p>
            <p>{{ $booking->owner_notes }}</p>
        @endif

        @if ($booking->status === 'finalized')
            <a class="btn btn-primary" href="{{ route('bookings.payment', $booking) }}">Confirm Booking & Pay Reservation Fee</a>
        @elseif ($booking->status === 'payment_submitted')
            <p class="alert alert-info mb-0">Your payment proof is waiting for business review.</p>
        @elseif ($booking->status === 'confirmed')
            <p class="alert alert-success mb-0">Your reservation payment has been confirmed. Your booking is secured.</p>
        @else
            <p class="alert alert-warning mb-0">The business is reviewing your booking and will send the final rate.</p>
        @endif
    </div></div>
</div>
</body>
</html>
