@extends('layouts.theme')

@section('title', 'Review Booking | Nogaru Car Rental')
@section('page-title', 'Review Booking')
@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div><h2 class="mb-1">Review Booking #{{ $booking->id }}</h2><p class="text-muted mb-0">Renter: {{ $booking->user?->name ?? 'Guest renter' }}</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('business.bookings.index') }}">Back</a>
    </div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if($booking->status === 'payment_submitted')
        <div class="card border-success mb-4"><div class="card-body"><h5 class="text-success">Reservation Payment Submitted</h5><p>Review the payment screenshot before confirming this booking.</p><div class="row g-3"><div class="col-md-6"><strong>Payment method:</strong> {{ $booking->businessPaymentMethod?->payment_method ?? strtoupper(str_replace('_', ' ', $booking->payment_method)) }}<br><strong>Reference number:</strong> {{ $booking->payment_reference_number ?: 'Not provided' }}<br><strong>Security deposit:</strong> ₱{{ number_format((float) $booking->reservation_fee, 2) }}</div><div class="col-md-6">@if($booking->payment_proof_path)<a class="btn btn-outline-primary" href="{{ asset($booking->payment_proof_path) }}" target="_blank">View Payment Screenshot</a>@endif @if($booking->flight_details_path)<a class="btn btn-outline-secondary" href="{{ asset($booking->flight_details_path) }}" target="_blank">View Flight Details</a>@endif</div>@if($booking->special_request)<div class="col-12"><strong>Special Request:</strong><p class="mb-0">{{ $booking->special_request }}</p></div>@endif</div><form class="mt-3" method="POST" action="{{ route('business.bookings.payment.confirm', $booking) }}">@csrf @method('PUT')<button class="btn btn-success" type="submit">Confirm Reservation Payment</button></form></div></div>
    @endif
    <form method="POST" action="{{ route('business.bookings.update', $booking) }}">@csrf @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card"><div class="card-body"><div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Rental Type</label><select class="form-select" id="rental-type" name="rental_type">@foreach(['self_drive'=>'Self-Drive Rental','with_driver'=>'With Driver / Chauffeur Service','airport_pickup'=>'Airport Pick-Up & Drop-Off','city_tour'=>'City Tours','out_of_town'=>'Out-of-Town Trips','wedding_event'=>'Wedding / Events','corporate'=>'Corporate & Business'] as $value => $label)<option value="{{ $value }}" @selected(old('rental_type', $booking->rental_type) === $value)>{{ $label }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Vehicle</label><select class="form-select" id="car-id" name="car_id">@foreach($vehicles as $vehicle)<option value="{{ $vehicle->id }}" @selected(old('car_id', $booking->car_id) === $vehicle->id)>{{ $vehicle->car_model ?: $vehicle->vehicle_type }}</option>@endforeach</select><small class="text-muted">Select an available replacement if needed.</small></div>
                    <div class="col-md-6"><label class="form-label">Passengers</label><input class="form-control" type="number" name="passengers_count" value="{{ old('passengers_count', $booking->passengers_count) }}" required></div>
                    <div class="col-12"><label class="form-label">Destination / Itinerary</label><textarea class="form-control" name="destination_itinerary" required>{{ old('destination_itinerary', $booking->destination_itinerary) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Pickup Date</label><input class="form-control" id="pickup-date" type="date" name="pickup_date" value="{{ old('pickup_date', $booking->pickup_date?->format('Y-m-d')) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Pickup Time</label><input class="form-control" id="pickup-time" type="time" name="pickup_time" value="{{ old('pickup_time', $booking->pickup_time) }}" required></div>
                    <div class="col-12" id="pickup-location-field"><label class="form-label">Pickup Location</label><input class="form-control" name="pickup_location" value="{{ old('pickup_location', $booking->pickup_location === 'Not applicable' ? '' : $booking->pickup_location) }}"></div>
                </div>
                <div id="self-drive-fields" class="mt-4"><h5>Self-Drive Rental Details</h5><div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Vehicle Handover Option</label><input class="form-control" name="handover_option" value="{{ old('handover_option', $booking->handover_option) }}"></div>
                    <div class="col-md-6"><label class="form-label">Other Handover Option</label><input class="form-control" name="handover_other" value="{{ old('handover_other', $booking->handover_other) }}"></div>
                    <div class="col-md-4"><label class="form-label">Return Date</label><input class="form-control" id="return-date" type="date" name="return_date" value="{{ old('return_date', $booking->return_date?->format('Y-m-d')) }}"></div>
                    <div class="col-md-4"><label class="form-label">Return Time</label><input class="form-control" id="return-time" type="time" name="return_time" value="{{ old('return_time', $booking->return_time) }}"></div>
                    <div class="col-md-4"><label class="form-label">Return Location</label><input class="form-control" name="return_location" value="{{ old('return_location', $booking->return_location) }}"></div>
                    <div class="col-12"><div class="alert alert-info mb-0">Requested rental duration: <strong id="duration">—</strong></div></div>
                    <div class="col-md-4"><label class="form-label">Delivery Fee (₱)</label><input class="form-control" type="number" name="delivery_fee" min="0" step="0.01" value="{{ old('delivery_fee', $booking->delivery_fee) }}"></div>
                    <div class="col-md-4"><label class="form-label">Pick-Up Fee (₱)</label><input class="form-control" type="number" name="pickup_fee" min="0" step="0.01" value="{{ old('pickup_fee', $booking->pickup_fee) }}"></div>
                </div></div>
                <div class="row g-3 mt-1"><div class="col-md-6"><label class="form-label">Final Rental Rate (₱)</label><input class="form-control" type="number" name="final_rate" min="0" step="0.01" value="{{ old('final_rate', $booking->final_rate ?? $booking->initial_rate) }}" required></div><div class="col-md-6"><label class="form-label">Reservation Fee (₱)</label><input class="form-control" type="number" name="reservation_fee" min="0" step="0.01" value="{{ old('reservation_fee', $booking->reservation_fee) }}" required></div><div class="col-12"><label class="form-label">Notes for Client</label><textarea class="form-control" name="owner_notes" rows="3">{{ old('owner_notes', $booking->owner_notes) }}</textarea></div></div>
                </div><div class="card-footer"><button class="btn btn-primary">Finalize Booking & Notify Client</button></div></div>
            </div>
            <div class="col-lg-4">
                @php($selectedVehicle = $vehicles->firstWhere('id', (int) old('car_id', $booking->car_id)))
                <div class="card mb-4"><div class="card-body"><h5 class="mb-1">Selected Vehicle Rates</h5><p class="text-muted small">{{ $selectedVehicle?->car_model ?: $selectedVehicle?->vehicle_type }}</p><div id="vehicle-rates">@forelse($selectedVehicle?->rates ?? [] as $rate)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $rate->name }}</span><strong>₱{{ number_format((float) $rate->value, 2) }}</strong></div>@empty<p class="text-muted mb-0">No rates have been added for this vehicle.</p>@endforelse</div></div></div>
                @if($booking->rental_type === 'self_drive')<div class="card"><div class="card-body"><h5>Client Profile & Self-Drive Requirements</h5>@php($profile = $booking->user?->clientProfile)<p class="mb-1"><strong>Name:</strong> {{ $profile?->full_name ?? $booking->user?->name ?? 'Guest renter' }}</p><p class="mb-1"><strong>Address:</strong> {{ $profile?->permanent_address ?? 'Not completed' }}</p><p class="mb-3"><strong>Contact:</strong> {{ $profile?->email_address ?? $booking->user?->email ?? 'Not provided' }}</p><p class="mb-0"><strong>Requirements:</strong> @if($profile?->isSelfDriveReady())<span class="text-success">Complete</span>@else<span class="text-danger">Incomplete</span>@endif</p></div></div>@endif
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
const vehicles = @json($vehicles->map(fn ($vehicle) => ['id' => $vehicle->id, 'rates' => $vehicle->rates->map(fn ($rate) => ['name' => $rate->name, 'value' => $rate->value])]));
const rentalType = document.getElementById('rental-type'); const selfDriveFields = document.getElementById('self-drive-fields'); const pickupLocation = document.getElementById('pickup-location-field');
function toggleSelfDrive() { const selfDrive = rentalType.value === 'self_drive'; selfDriveFields.classList.toggle('d-none', !selfDrive); selfDriveFields.querySelectorAll('input, select, textarea').forEach((field) => { field.disabled = !selfDrive; }); pickupLocation.classList.toggle('d-none', selfDrive); }
function duration() { const start = new Date(`${document.getElementById('pickup-date').value}T${document.getElementById('pickup-time').value}`); const end = new Date(`${document.getElementById('return-date').value}T${document.getElementById('return-time').value}`); const output = document.getElementById('duration'); if (Number.isNaN(start) || Number.isNaN(end) || end < start) { output.textContent = '—'; return; } const hours = Math.round((end - start) / 3600000); output.textContent = `${Math.floor(hours / 24)} day(s) and ${hours % 24} hour(s)`; }
function renderRates() { const vehicle = vehicles.find((item) => item.id === Number(document.getElementById('car-id').value)); document.getElementById('vehicle-rates').innerHTML = vehicle?.rates.length ? vehicle.rates.map((rate) => `<div class="d-flex justify-content-between border-bottom py-2"><span>${rate.name}</span><strong>₱${Number(rate.value).toLocaleString(undefined, {minimumFractionDigits: 2})}</strong></div>`).join('') : '<p class="text-muted mb-0">No rates have been added for this vehicle.</p>'; }
rentalType.addEventListener('change', toggleSelfDrive); ['pickup-date','pickup-time','return-date','return-time'].forEach((id) => document.getElementById(id).addEventListener('input', duration)); document.getElementById('car-id').addEventListener('change', renderRates); toggleSelfDrive(); duration(); renderRates();
</script>
@endpush
