@extends('layouts.theme')

@section('title', 'Sales Workspace')
@section('page-title', 'Sales Workspace')

@section('content')
<div class="row g-3 mb-3">
    @foreach([['New Leads', $performance['new_leads'], 'ti-users-plus'], ['Quotations', $performance['quotations'], 'ti-file-invoice'], ['Bookings', $performance['bookings'], 'ti-calendar-check'], ['Estimated Sales', number_format((float) $performance['estimated_sales'], 2), 'ti-chart-line']] as [$label, $value, $icon])
        <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="d-flex justify-content-between"><span class="text-muted">{{ $label }}</span><i class="ti {{ $icon }} text-primary fs-4"></i></div><div class="fs-3 fw-semibold mt-2">{{ $value }}</div></div></div></div>
    @endforeach
</div>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card"><div class="card-header"><h5 class="mb-0">Add lead</h5></div><div class="card-body">
            <form method="POST" action="{{ route('sales-agent.leads.store') }}" class="vstack gap-3">
                @csrf
                <div><label class="form-label">Customer name</label><input name="name" class="form-control" required></div>
                <div><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                <div><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
                <div><label class="form-label">Source</label><input name="source" class="form-control" placeholder="Facebook, referral, walk-in"></div>
                <div class="row g-2"><div class="col-6"><label class="form-label">Rental start</label><input type="date" name="rental_start_date" class="form-control"></div><div class="col-6"><label class="form-label">Rental end</label><input type="date" name="rental_end_date" class="form-control"></div></div>
                <div><label class="form-label">Preferred vehicle</label><input name="preferred_vehicle" class="form-control"></div>
                <div><label class="form-label">Estimated value</label><input type="number" name="estimated_value" class="form-control" min="0" step="0.01"></div>
                <div><label class="form-label">Next follow-up</label><input type="date" name="next_follow_up_at" class="form-control"></div>
                <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
                <button class="btn btn-primary" type="submit"><i class="ti ti-user-plus me-1"></i>Add lead</button>
            </form>
        </div></div>
        <div class="card mt-3"><div class="card-header"><h5 class="mb-0">Commission summary</h5></div><div class="card-body"><div class="fs-3 fw-semibold">{{ number_format((float) $commissions->sum('amount'), 2) }}</div><small class="text-muted">{{ $commissions->where('status', 'approved')->count() }} approved commissions</small></div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header"><h5 class="mb-0">My leads</h5></div><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Lead</th><th>Status</th><th>Follow-up</th><th class="text-end">Update</th></tr></thead><tbody>
            @forelse($leads as $lead)
                <tr><td><div class="fw-semibold">{{ $lead->name }}</div><small class="text-muted">{{ $lead->email ?: $lead->phone }}</small><small class="d-block text-muted">{{ $lead->preferred_vehicle ?: 'Vehicle not selected' }} · {{ $lead->rental_start_date?->format('M d').' - '.$lead->rental_end_date?->format('M d') }}</small></td><td><span class="badge bg-primary-subtle text-primary">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span></td><td>{{ $lead->next_follow_up_at?->format('M d, Y') ?: 'Not set' }}</td><td class="text-end"><div class="d-flex gap-2 justify-content-end"><a href="mailto:{{ $lead->email }}" class="btn btn-sm btn-outline-secondary" title="Contact customer"><i class="ti ti-mail"></i></a><form method="POST" action="{{ route('sales-agent.leads.update', $lead) }}" class="d-flex gap-2">@csrf @method('PUT')<select name="status" class="form-select form-select-sm" style="width: 135px">@foreach(['new', 'contacted', 'quoted', 'negotiating', 'follow_up', 'confirmed', 'lost', 'cancelled', 'booked'] as $status)<option value="{{ $status }}" @selected($lead->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select><input type="hidden" name="notes" value="{{ $lead->notes }}"><button class="btn btn-sm btn-outline-primary">Save</button></form></div></td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No leads yet. Add your first prospect.</td></tr>
            @endforelse
        </tbody></table></div></div>
        <div class="card mt-3"><div class="card-header"><h5 class="mb-0">Convert a lead to booking</h5></div><div class="card-body"><p class="text-muted small">Choose a booking after the reservation is created to record the commission.</p>
            @forelse($leads->whereIn('status', ['new', 'contacted', 'quoted', 'negotiating', 'follow_up']) as $lead)
                <div class="border-top py-3"><div class="d-flex align-items-center gap-2 mb-2"><span class="flex-grow-1"><strong>{{ $lead->name }}</strong><small class="d-block text-muted">{{ $lead->source ?: 'No source' }} · {{ $lead->preferred_vehicle ?: 'Any vehicle' }}</small></span><form method="POST" action="{{ route('sales-agent.leads.reservation', $lead) }}" class="d-flex gap-2">@csrf<select name="car_id" class="form-select form-select-sm" required><option value="">Reserve vehicle</option>@foreach($business->cars as $car)<option value="{{ $car->id }}">{{ $car->car_model ?: $car->vehicle_type }}</option>@endforeach</select><input type="number" name="final_rate" class="form-control form-control-sm" placeholder="Total" min="0" step="0.01" required><button class="btn btn-sm btn-primary" type="submit">Reserve</button></form></div><form method="POST" action="{{ route('sales-agent.leads.quotation', $lead) }}" class="row g-2">@csrf<div class="col-md-3"><select name="car_id" class="form-select form-select-sm" required><option value="">Quote vehicle</option>@foreach($business->cars as $car)<option value="{{ $car->id }}">{{ $car->car_model ?: $car->vehicle_type }}</option>@endforeach</select></div><div class="col-md-2"><select name="vehicle_rate_name" class="form-select form-select-sm" required><option value="">Rate</option>@foreach($business->cars->flatMap->rates->unique('name') as $rate)<option value="{{ $rate->name }}">{{ $rate->name }}</option>@endforeach</select></div><div class="col-md-2"><input type="date" name="rental_start_date" class="form-control form-control-sm" value="{{ $lead->rental_start_date?->toDateString() }}" required></div><div class="col-md-2"><input type="date" name="rental_end_date" class="form-control form-control-sm" value="{{ $lead->rental_end_date?->toDateString() }}" required></div><div class="col-md-2"><input type="number" name="add_on_amount" class="form-control form-control-sm" placeholder="Add-on" min="0" step="0.01"></div><div class="col-md-1"><button class="btn btn-sm btn-outline-primary" type="submit">Quote</button></div></form></div>
            @empty
                <p class="text-muted mb-0">No leads are ready for conversion.</p>
            @endforelse
        </div></div>
    </div>
</div>
@endsection
