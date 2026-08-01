@extends('layouts.theme')

@section('title', 'New Booking | Nogaru Car Rental')
@section('page-title', 'Create Manual Booking')
@section('page-actions')<a href="{{ route('business.bookings.index') }}" class="btn btn-outline-secondary">Back to bookings</a>@endsection

@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('business.bookings.manual.store') }}" class="row g-3">@csrf
            <div class="col-md-6"><label class="form-label">Customer</label><select name="user_id" class="form-select" required><option value="">Select customer</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('user_id') == $customer->id)>{{ $customer->clientProfile?->full_name ?? $customer->name }} — {{ $customer->email }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Vehicle</label><select name="car_id" class="form-select" required><option value="">Select vehicle</option>@foreach($cars as $car)<option value="{{ $car->id }}" @selected(old('car_id') == $car->id)>{{ $car->car_model ?: $car->vehicle_type }} {{ $car->plate_number ? '('.$car->plate_number.')' : '' }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">Pickup date</label><input type="date" name="pickup_date" value="{{ old('pickup_date', today()->format('Y-m-d')) }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Pickup time</label><input type="time" name="pickup_time" value="{{ old('pickup_time', '09:00') }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Return date</label><input type="date" name="return_date" value="{{ old('return_date', today()->format('Y-m-d')) }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Return time</label><input type="time" name="return_time" value="{{ old('return_time', '18:00') }}" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Pickup location</label><input type="text" name="pickup_location" value="{{ old('pickup_location') }}" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Return location</label><input type="text" name="return_location" value="{{ old('return_location') }}" class="form-control"></div>
            <div class="col-md-8"><label class="form-label">Destination / itinerary</label><input type="text" name="destination_itinerary" value="{{ old('destination_itinerary') }}" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Total rate</label><input type="number" name="final_rate" min="0" step="0.01" value="{{ old('final_rate') }}" class="form-control" required></div>
            <div class="col-12"><button class="btn btn-primary">Create reserved booking</button></div>
        </form>
        @if($errors->any())<div class="alert alert-danger mt-3 mb-0">{{ $errors->first() }}</div>@endif
    </div></div>
@endsection
