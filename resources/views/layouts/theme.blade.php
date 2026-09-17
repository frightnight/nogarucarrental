<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nogaru Car Rental')</title>
    <link rel="shortcut icon" href="{{ asset('theme/assets/images/favicon.ico') }}">
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>
    <link href="{{ asset('theme/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link id="app-style" href="{{ asset('theme/assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/assets/css/account.css') }}" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <header class="app-topbar">
        <div class="container-fluid topbar-menu">
            <div class="d-flex align-items-center gap-2">
                <a class="text-white fs-4 fw-bold text-decoration-none" href="{{ route('dashboard') }}">Nogaru</a>
                <button class="sidenav-toggle-button btn btn-default btn-icon"><i class="ti ti-menu-4"></i></button>
            </div>
            <div class="d-flex align-items-center gap-2">
                @auth
                    <span class="d-none d-sm-inline text-muted">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-danger"><i class="ti ti-logout me-1"></i>Log out</button></form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Log in</a>
                @endauth
            </div>
        </div>
    </header>
    <div class="app-menu">
        <div class="sidenav-menu">
            <a href="{{ route('dashboard') }}" class="logo"><span class="logo-lg text-white fw-bold fs-4">Nogaru</span></a>
            <ul class="side-nav">
                <li class="side-nav-item"><a href="{{ route('dashboard') }}" class="side-nav-link"><i class="ti ti-layout-dashboard"></i><span>Dashboard</span></a></li>
                @auth
                @if(auth()->user()->hasRole('client'))
                    <li class="side-nav-item"><a href="{{ route('businesses.index') }}" class="side-nav-link"><i class="ti ti-car"></i><span>Book a Car</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('client.profile.edit') }}" class="side-nav-link"><i class="ti ti-user"></i><span>My Profile</span></a></li>
                @endif
                @if(auth()->user()->hasRole('driver'))
                    <li class="side-nav-item"><a href="{{ route('driver.dashboard') }}" class="side-nav-link"><i class="ti ti-id-badge-2"></i><span>Driver Portal</span></a></li>
                @endif
                @if(auth()->user()->hasRole('sales_agent'))
                    <li class="side-nav-item"><a href="{{ route('sales-agent.dashboard') }}" class="side-nav-link"><i class="ti ti-target-arrow"></i><span>Sales Workspace</span></a></li>
                @endif
                @if(auth()->user()->hasAnyRole(['business_owner', 'booker', 'agent']))
                    <li class="side-nav-item"><a href="{{ route('business.bookings.index') }}" class="side-nav-link"><i class="ti ti-calendar-check"></i><span>Bookings</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.bookings.calendar') }}" class="side-nav-link"><i class="ti ti-calendar-month"></i><span>Booking Calendar</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.crm') }}" class="side-nav-link"><i class="ti ti-address-book"></i><span>CRM</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.quotations.index') }}" class="side-nav-link"><i class="ti ti-file-invoice"></i><span>Quotation</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.clients.index') }}" class="side-nav-link"><i class="ti ti-users"></i><span>Clients</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.fleet.index') }}" class="side-nav-link"><i class="ti ti-steering-wheel"></i><span>Fleet</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.drivers.index') }}" class="side-nav-link"><i class="ti ti-id-badge-2"></i><span>Drivers</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.partner-fleets.index') }}" class="side-nav-link"><i class="ti ti-users-group"></i><span>Partner Fleets</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.bookings.create') }}" class="side-nav-link"><i class="ti ti-calendar-plus"></i><span>New Booking</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.reports') }}" class="side-nav-link"><i class="ti ti-chart-bar"></i><span>Reports</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.plan') }}" class="side-nav-link"><i class="ti ti-credit-card"></i><span>Plan & Billing</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.payment-methods.index') }}" class="side-nav-link"><i class="ti ti-wallet"></i><span>Payment Methods</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.profile.edit') }}" class="side-nav-link"><i class="ti ti-building"></i><span>Business Profile</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('business.landing.content.edit') }}" class="side-nav-link"><i class="ti ti-world"></i><span>Landing Page</span></a></li>
                @endif
                @if(auth()->user()->hasAnyRole(['administrator', 'moderator']))
                    <li class="side-nav-item"><a href="{{ route('admin.landing.edit') }}" class="side-nav-link"><i class="ti ti-world"></i><span>Public Landing Page</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('admin.business-plans.index') }}" class="side-nav-link"><i class="ti ti-building-store"></i><span>Business Plans</span></a></li>
                    @if(auth()->user()->hasRole('administrator'))
                        <li class="side-nav-item"><a href="{{ route('admin.drivers.index') }}" class="side-nav-link"><i class="ti ti-user-check"></i><span>Driver Applications</span></a></li>
                        <li class="side-nav-item"><a href="{{ route('admin.sales-agents.index') }}" class="side-nav-link"><i class="ti ti-user-dollar"></i><span>Sales Agents</span></a></li>
                    @endif
                @endif
                @endauth
            </ul>
        </div>
    </div>
    <div class="content-page"><div class="content"><div class="container-fluid">
        <div class="page-title-box"><h4 class="page-title">@yield('page-title')</h4>@yield('page-actions')</div>
        @yield('content')
    </div></div>
    <footer class="footer"><div class="container-fluid"><span>{{ date('Y') }} © Nogaru Car Rental</span></div></footer>
    </div>
</div>
<script src="{{ asset('theme/assets/js/vendors.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
