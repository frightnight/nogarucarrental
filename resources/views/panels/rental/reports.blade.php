@extends('layouts.theme')

@section('title', 'Reports | Nogaru Car Rental')
@section('page-title', 'Monthly Reports')
@section('content')
    <div class="row g-3 mb-3"><div class="col-md-6"><div class="card"><div class="card-body"><p class="text-muted mb-1">Monthly bookings</p><h2 class="mb-0">{{ $monthlyBookings }}</h2><small>{{ now()->format('F Y') }}</small></div></div></div><div class="col-md-6"><div class="card"><div class="card-body"><p class="text-muted mb-1">Monthly revenue</p><h2 class="mb-0">₱{{ number_format((float) $monthlyRevenue, 2) }}</h2><small>Completed and ongoing rentals</small></div></div></div></div>
    <div class="card"><div class="card-header"><h4 class="header-title mb-0">Vehicle utilization</h4></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Vehicle</th><th>Bookings this month</th><th>Utilization</th></tr></thead><tbody>@forelse($cars as $car)<tr><td>{{ $car->car_model ?: $car->vehicle_type }} <small class="text-muted">{{ $car->plate_number }}</small></td><td>{{ $car->monthly_bookings }}</td><td>{{ $monthlyBookings ? number_format(($car->monthly_bookings / $monthlyBookings) * 100, 1) : 0 }}%</td></tr>@empty<tr><td colspan="3" class="text-center text-muted py-4">No vehicles registered.</td></tr>@endforelse</tbody></table></div></div>
@endsection
