@extends('layouts.theme')
@section('title', 'Admin Dashboard | Nogaru Car Rental')
@section('page-title', 'Administrator Dashboard')
@section('content')
    <div class="row">
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-users widget-icon float-end text-primary"></i><h5 class="text-muted fw-normal mt-0">Users</h5><h3 class="mt-3 mb-3">Manage</h3><p class="mb-0 text-muted">Platform accounts</p></div></div></div>
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-building-store widget-icon float-end text-success"></i><h5 class="text-muted fw-normal mt-0">Businesses</h5><h3 class="mt-3 mb-3">Monitor</h3><p class="mb-0 text-muted">Partner businesses</p></div></div></div>
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-calendar-check widget-icon float-end text-warning"></i><h5 class="text-muted fw-normal mt-0">Bookings</h5><h3 class="mt-3 mb-3">Track</h3><p class="mb-0 text-muted">Rental activity</p></div></div></div>
        <div class="col-xl-3 col-lg-6"><div class="card widget-flat"><div class="card-body"><i class="ti ti-chart-bar widget-icon float-end text-info"></i><h5 class="text-muted fw-normal mt-0">Reports</h5><h3 class="mt-3 mb-3">Review</h3><p class="mb-0 text-muted">Platform insights</p></div></div></div>
    </div>
    <div class="card"><div class="card-body"><h4 class="header-title">Platform Management</h4><p class="text-muted mb-0">Use the dashboard to oversee platform users, businesses, bookings, and reporting as those management sections are enabled.</p></div></div>
@endsection
