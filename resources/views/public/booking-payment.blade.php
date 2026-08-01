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
    <div class="card mx-auto" style="max-width: 600px">
        <div class="card-body">
            <h2>Confirm Your Booking</h2>
            <p>Reservation fee to secure this booking: <strong>₱{{ number_format((float) $booking->reservation_fee, 2) }}</strong></p>
            <p class="text-muted">
                Quoted rental rate: ₱{{ number_format((float) $booking->final_rate, 2) }}
                @if ($booking->rental_type === 'self_drive')
                    + delivery/pick-up fees
                @endif
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('bookings.payment.store', $booking) }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label">Payment Method</label>
                <select class="form-select mb-3" name="payment_method" required>
                    <option value="gcash">GCash</option>
                    <option value="paymaya">PayMaya</option>
                    <option value="bank_transaction">Bank Transaction</option>
                </select>

                <label class="form-label">Special Request <span class="text-muted">(optional)</span></label>
                <textarea class="form-control mb-3" name="special_request" rows="3">{{ old('special_request', $booking->special_request) }}</textarea>

                <label class="form-label">Flight Details <span class="text-muted">(optional image)</span></label>
                <input class="form-control mb-3" type="file" name="flight_details" accept="image/*">

                <label class="form-label">Reservation Payment Screenshot</label>
                <input class="form-control mb-3" type="file" name="payment_proof" accept="image/*" required>

                <button class="btn btn-primary">Submit Final Confirmation</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
