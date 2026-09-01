@extends('layouts.theme')

@section('title', 'Drivers | Nogaru Car Rental')
@section('page-title', 'Drivers')
@section('content')
    <div class="page-title-box"><div class="page-title-right"><a href="{{ route('business.fleet.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-car me-1"></i>Fleet</a></div><h4 class="page-title">Business Drivers</h4><p class="text-muted mb-0">Add a driver by licence number. Drivers already registered by another business are reused.</p></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="row g-4">
        <div class="col-lg-4"><div class="card"><div class="card-header"><h5 class="mb-0">Add or hire a driver</h5></div><div class="card-body"><form method="POST" action="{{ route('business.drivers.store') }}">@csrf
            <div class="mb-3"><label for="license-number" class="form-label">Driver's licence number</label><input id="license-number" name="license_number" class="form-control" value="{{ old('license_number') }}" list="available-driver-licences" required><datalist id="available-driver-licences">@foreach($availableExternalDrivers as $driver)<option value="{{ $driver->license_number }}">{{ $driver->full_name }}</option>@endforeach</datalist><div class="form-text">Used as the unique driver ID across all businesses.</div></div>
            <div class="mb-3"><label for="full-name" class="form-label">Full name</label><input id="full-name" name="full_name" class="form-control" value="{{ old('full_name') }}"><div class="form-text">Required only for a new licence. Existing available drivers are reused.</div></div>
            <div class="mb-3"><label for="phone" class="form-label">Phone <span class="text-muted">(new driver only)</span></label><input id="phone" name="phone" class="form-control" value="{{ old('phone') }}"></div>
            <div class="mb-3"><label for="email" class="form-label">Email <span class="text-muted">(new driver only)</span></label><input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
            <div class="mb-3"><label for="daily-rate" class="form-label">Your daily driver rate (₱)</label><input id="daily-rate" type="number" name="daily_rate" class="form-control" min="0" step="0.01" value="{{ old('daily_rate', 0) }}" required></div>
            <button class="btn btn-primary w-100" type="submit">Save Driver</button>
        </form></div></div></div>
        <div class="col-lg-8"><div class="card mb-4"><div class="card-header"><h5 class="mb-0">Available to outsource</h5></div><div class="card-body">@if($availableExternalDrivers->isEmpty())<p class="text-muted mb-0">No external drivers are currently available.</p>@else<div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Driver</th><th>Licence</th><th></th></tr></thead><tbody>@foreach($availableExternalDrivers as $driver)<tr><td>{{ $driver->full_name }}</td><td>{{ $driver->license_number }}</td><td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary hire-driver" data-licence="{{ $driver->license_number }}">Hire</button></td></tr>@endforeach</tbody></table></div>@endif</div></div><div class="card"><div class="card-header"><h5 class="mb-0">Your driver assignments</h5></div><div class="card-body">@if($drivers->isEmpty())<p class="text-muted mb-0">No drivers assigned yet.</p>@else<div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Driver</th><th>Licence</th><th>Daily Rate</th><th>Availability</th><th>Default</th><th></th></tr></thead><tbody>@foreach($drivers as $driver)<tr><td><strong>{{ $driver->full_name }}</strong><small class="d-block text-muted">{{ $driver->phone ?: $driver->email ?: 'No contact details' }}</small></td><td>{{ $driver->license_number }}</td><td><form id="driver-{{ $driver->license_number }}" method="POST" action="{{ route('business.drivers.update', $driver) }}">@csrf @method('PUT')<input type="number" name="daily_rate" class="form-control form-control-sm" min="0" step="0.01" value="{{ $driver->pivot->daily_rate }}"></form></td><td><select form="driver-{{ $driver->license_number }}" name="is_available" class="form-select form-select-sm"><option value="1" @selected($driver->pivot->is_available)>Available</option><option value="0" @selected(! $driver->pivot->is_available)>Unavailable</option></select></td><td><div class="form-check"><input form="driver-{{ $driver->license_number }}" id="default-driver-{{ $loop->index }}" class="form-check-input default-driver-radio" type="radio" name="default_driver" value="{{ $driver->license_number }}" @checked($driver->pivot->is_default)><label class="form-check-label" for="default-driver-{{ $loop->index }}">Default</label></div></td><td class="text-end text-nowrap"><a href="{{ route('business.drivers.show', $driver) }}" class="btn btn-sm btn-outline-secondary">View Profile</a><button form="driver-{{ $driver->license_number }}" class="btn btn-sm btn-outline-primary">Save</button><form method="POST" class="d-inline" action="{{ route('business.drivers.destroy', $driver) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this driver from your business?')">Remove</button></form></td></tr>@endforeach</tbody></table></div>@endif</div></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.hire-driver').forEach((button) => button.addEventListener('click', () => {
            document.getElementById('license-number').value = button.dataset.licence;
            document.getElementById('daily-rate').focus();
        }));
        document.querySelectorAll('.default-driver-radio').forEach((radio) => radio.addEventListener('change', () => {
            document.querySelectorAll('.default-driver-radio').forEach((input) => input.checked = input === radio);
            document.querySelectorAll('form[id^="driver-"]').forEach((form) => {
                const isDefault = form.id === `driver-${radio.value}`;
                let defaultInput = form.querySelector('input[name="is_default"]');
                if (!defaultInput) {
                    defaultInput = document.createElement('input');
                    defaultInput.type = 'hidden';
                    defaultInput.name = 'is_default';
                    form.appendChild(defaultInput);
                }
                defaultInput.value = isDefault ? '1' : '0';
            });
        }));
    </script>
@endpush
