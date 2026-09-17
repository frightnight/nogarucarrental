@extends('layouts.theme')

@section('title', 'Sales Agents')
@section('page-title', 'Sales Agents')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Create account</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.sales-agents.store') }}" class="vstack gap-3">
                    @csrf
                    <div><label class="form-label">Name</label><input name="name" class="form-control" required value="{{ old('name') }}"></div>
                    <div><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="{{ old('email') }}"></div>
                    <div><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div><label class="form-label">Confirm password</label><input type="password" name="password_confirmation" class="form-control" required></div>
                    <div><label class="form-label">Business</label><select name="business_id" class="form-select" required><option value="">Select business</option>@foreach($businesses as $business)<option value="{{ $business->id }}">{{ $business->name }}</option>@endforeach</select></div>
                    <div><label class="form-label">Commission rate (%)</label><input type="number" name="commission_rate_percent" class="form-control" min="0" max="100" step="0.01" value="0" required></div>
                    <button class="btn btn-primary" type="submit"><i class="ti ti-user-plus me-1"></i>Create sales agent</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Managed accounts</h5></div>
            <div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Agent</th><th>Business</th><th>Commission</th><th>Status</th><th class="text-end">Action</th></tr></thead><tbody>
            @forelse($agents as $agent)
                @php($membership = $agent->businesses->first()?->pivot)
                <tr><td><div class="fw-semibold">{{ $agent->name }}</div><small class="text-muted">{{ $agent->email }}</small></td><td>{{ $agent->businesses->first()?->name ?? 'Unassigned' }}</td><td>{{ number_format((float) ($membership?->commission_rate_percent ?? 0), 2) }}%</td><td><span class="badge bg-info-subtle text-info">{{ ucfirst($agent->sales_agent_status ?: 'legacy') }}</span></td><td class="text-end d-flex gap-1 justify-content-end">@if($agent->sales_agent_status === 'pending')<form method="POST" action="{{ route('admin.sales-agents.approve', $agent) }}" class="d-flex gap-1">@csrf @method('PUT')<select name="business_id" class="form-select form-select-sm" required><option value="">Business</option>@foreach($businesses as $business)<option value="{{ $business->id }}">{{ $business->name }}</option>@endforeach</select><input name="commission_rate_percent" type="number" class="form-control form-control-sm" style="width:85px" min="0" max="100" step="0.01" value="0"><button class="btn btn-sm btn-success">Approve</button></form><form method="POST" action="{{ route('admin.sales-agents.reject', $agent) }}">@csrf @method('PUT')<button class="btn btn-sm btn-outline-danger">Reject</button></form>@else<form method="POST" action="{{ route('admin.sales-agents.toggle', $agent) }}">@csrf @method('PUT')<button class="btn btn-sm btn-outline-secondary">{{ ($membership?->is_active ?? false) ? 'Deactivate' : 'Activate' }}</button></form>@endif</td></tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No sales agents yet.</td></tr>
            @endforelse
            </tbody></table></div>
        </div>
        <div class="card mt-3"><div class="card-header"><h5 class="mb-0">Pending commissions</h5></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Agent</th><th>Booking</th><th>Amount</th><th class="text-end">Action</th></tr></thead><tbody>
            @php($commissions = $agents->flatMap->salesCommissions->where('status', 'pending'))
            @forelse($commissions as $commission)<tr><td>{{ $commission->salesAgent?->name }}</td><td>#{{ $commission->booking_id }}</td><td>{{ number_format((float) $commission->amount, 2) }}</td><td class="text-end"><form method="POST" action="{{ route('admin.sales-commissions.approve', $commission) }}">@csrf @method('PUT')<button class="btn btn-sm btn-success">Approve</button></form></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">No pending commissions.</td></tr>@endforelse
        </tbody></table></div></div>
    </div>
</div>
@endsection
