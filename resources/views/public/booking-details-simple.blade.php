<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Details | Nogaru Car Rental</title>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div><h2 class="mb-1">Booking Details</h2><p class="text-muted mb-0">{{ $business->name }} · {{ $car->car_model ?: $car->vehicle_type }}</p></div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
    </div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="row g-4">
        <div class="col-lg-4"><div class="card"><div class="card-body"><h5>Available Rates</h5>@forelse($car->rates as $rate)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $rate->name }}</span><strong>₱{{ number_format((float) $rate->value, 2) }}</strong></div>@empty<p class="text-muted mb-0">Rates will be confirmed by the business.</p>@endforelse</div></div></div>
        <div class="col-lg-8"><div class="card"><div class="card-body">
            <form method="POST" action="{{ route('bookings.store') }}">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}"><input type="hidden" name="car_id" value="{{ $car->id }}">
                <h5 class="mb-3">Booking Details</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Rental Type</label><select class="form-select" id="rental-type" name="rental_type" required><option value="self_drive">Self-Drive Rental</option><option value="with_driver">With Driver / Chauffeur Service</option><option value="airport_pickup">Airport Pick-Up & Drop-Off</option><option value="city_tour">City Tours (Albay · Sorsogon · Cam Sur)</option><option value="out_of_town">Out-of-Town Trips</option><option value="wedding_event">Wedding / Events Car Rental</option><option value="corporate">Corporate & Business Transport</option></select></div>
                    <div class="col-md-6"><label class="form-label">Number of Passengers</label><input class="form-control" type="number" name="passengers_count" min="1" max="20" value="{{ old('passengers_count') }}" required></div>
                    <div class="col-12"><label class="form-label">Destination / Itinerary</label><textarea class="form-control" name="destination_itinerary" rows="3" placeholder="Destination 1, Destination 2, Destination 3..." required>{{ old('destination_itinerary') }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Pickup Date</label><input class="form-control" type="date" name="pickup_date" value="{{ old('pickup_date') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Pickup Time</label><input class="form-control" type="time" name="pickup_time" value="{{ old('pickup_time') }}" required></div>
                    <div class="col-12" id="pickup-location"><label class="form-label">Pickup Location</label><input class="form-control" name="pickup_location" value="{{ old('pickup_location') }}"><small class="text-muted">Leave blank for self-drive rental.</small></div>
                </div>
                <div id="self-drive-fields" class="mt-4"><h5>For Self-Drive Rental Only</h5><div class="row g-3"><div class="col-md-6"><label class="form-label">Vehicle Handover Option</label><select class="form-select" name="handover_option"><option value="customer_pickup">Customer Pick-Up</option><option value="other">Other</option></select></div><div class="col-md-6"><label class="form-label">Other Handover Option</label><input class="form-control" name="handover_other" value="{{ old('handover_other') }}"></div><div class="col-md-4"><label class="form-label">Return Date</label><input class="form-control" type="date" name="return_date" value="{{ old('return_date') }}"></div><div class="col-md-4"><label class="form-label">Return Time</label><input class="form-control" type="time" name="return_time" value="{{ old('return_time') }}"></div><div class="col-md-4"><label class="form-label">Return Location</label><input class="form-control" name="return_location" value="{{ old('return_location') }}"></div></div></div>
                <button class="btn btn-primary mt-4" type="submit">Submit Booking Request</button>
            </form>
        </div></div></div>
    </div>
</div>
<script>const type=document.getElementById('rental-type'),fields=document.getElementById('self-drive-fields'),pickup=document.getElementById('pickup-location');function toggle(){const self=type.value==='self_drive';fields.classList.toggle('d-none',!self);fields.querySelectorAll('input, select, textarea').forEach((field)=>{field.disabled=!self});pickup.classList.toggle('d-none',self)}type.addEventListener('change',toggle);toggle();</script>
</body>
</html>
