@extends('layouts.theme')

@section('title', 'Business Plans | Nogaru Car Rental')
@section('page-title', 'Business Plans')
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="card-header"><h4 class="header-title mb-0">Assign a business plan</h4></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Business</th><th>Current plan</th><th>Vehicle limit</th><th class="text-end">Plan</th></tr></thead><tbody>@foreach($businesses as $business)<tr><td>{{ $business->name }}</td><td>{{ ($business->plan ?? \App\BusinessPlan::Free)->label() }}</td><td>{{ ($business->plan ?? \App\BusinessPlan::Free)->vehicleLimit() ?? 'Unlimited' }}</td><td class="text-end"><form method="POST" action="{{ route('admin.business-plans.update', $business) }}" class="d-inline-flex gap-2">@csrf @method('PUT')<select name="plan" class="form-select form-select-sm">@foreach($plans as $plan)<option value="{{ $plan->value }}" @selected($business->plan === $plan)>{{ $plan->label() }} — {{ $plan->price() }}</option>@endforeach</select><button class="btn btn-sm btn-primary">Save</button></form></td></tr>@endforeach</tbody></table></div></div>
@endsection
