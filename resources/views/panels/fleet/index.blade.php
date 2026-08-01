@extends('layouts.theme')

@section('title', 'Fleet Management | Nogaru Car Rental')
@section('page-title', 'Fleet Management')
@section('content')

                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('business.fleet.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-plus me-1"></i> Add Vehicle
                                    </a>
                                    <a href="{{ route('business.dashboard') }}" class="btn btn-outline-secondary btn-sm me-1">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">Fleet Management</h4>
                                <p class="text-muted mb-0">Manage your vehicles — {{ $cars->count() }} vehicle(s) registered</p>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti ti-check me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Fleet Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    @if($cars->isEmpty())
                                        <div class="text-center py-5">
                                            <i class="ti ti-car-off text-muted" style="font-size: 3rem;"></i>
                                            <h4 class="mt-3 text-muted">No vehicles in your fleet yet</h4>
                                            <p class="text-muted fs-sm">Add your first vehicle to start accepting bookings.</p>
                                            <a href="{{ route('business.fleet.create') }}" class="btn btn-primary mt-2">
                                                <i class="ti ti-plus me-1"></i> Add Vehicle
                                            </a>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover table-centered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Images</th>
                                                        <th>Model</th>
                                                        <th>Type</th>
                                                        <th>Transmission</th>
                                                        <th>Plate #</th>
                                                        <th>Year</th>
                                                        <th>Rates</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($cars as $car)
                                                        <tr>
                                                            <td>
                                                                @if($car->images->isNotEmpty())
                                                                    <div class="d-flex gap-1">
                                                                        @foreach($car->images->take(3) as $img)
                                                                            <img src="{{ $img->image_path }}" alt="Car image"
                                                                                 class="rounded" width="50" height="40"
                                                                                 style="object-fit: cover;">
                                                                        @endforeach
                                                                        @if($car->images->count() > 3)
                                                                            <span class="badge bg-secondary d-flex align-items-center">+{{ $car->images->count() - 3 }}</span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted fs-sm">No images</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <strong>{{ $car->car_model ?: '—' }}</strong>
                                                                @if($car->variant)
                                                                    <br><small class="text-muted">{{ $car->variant }}</small>
                                                                @endif
                                                            </td>
                                                            <td>{{ $car->vehicle_type ?: '—' }}</td>
                                                            <td>
                                                                @if($car->transmission)
                                                                    <span class="badge bg-info-subtle text-info rounded-pill">{{ $car->transmission }}</span>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td><code>{{ $car->plate_number ?: '—' }}</code></td>
                                                            <td>{{ $car->year_model ?: '—' }}</td>
                                                            <td>
                                                                @forelse($car->rates as $rate)
                                                                    <div class="small text-nowrap">
                                                                        {{ $rate->name }}: <strong>₱{{ number_format((float) $rate->value, 2) }}</strong>
                                                                    </div>
                                                                @empty
                                                                    <span class="text-muted fs-sm">No rates</span>
                                                                @endforelse
                                                            </td>
                                                            <td class="text-end">
                                                                <a href="{{ route('business.fleet.edit', $car) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="ti ti-edit"></i> Edit
                                                                </a>
                                                                <form method="POST" action="{{ route('business.fleet.destroy', $car) }}"
                                                                      class="d-inline"
                                                                      onsubmit="return confirm('Remove this vehicle from your fleet?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="ti ti-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

@endsection
