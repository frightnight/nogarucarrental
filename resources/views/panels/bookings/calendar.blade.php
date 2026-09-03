@extends('layouts.theme')
@section('title', 'Booking Calendar | Nogaru Car Rental')
@section('page-title', 'Booking Calendar')
@section('page-actions')<a href="{{ route('business.bookings.index') }}" class="btn btn-outline-secondary">Booking List</a>@endsection
@section('content')
    <div class="card"><div class="card-body"><div id="booking-calendar"></div></div></div>
@endsection
@push('scripts')
    <script src="{{ asset('theme/assets/plugins/fullcalendar/index.global.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendar = new FullCalendar.Calendar(document.getElementById('booking-calendar'), {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap',
                height: 'auto',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth' },
                events: @json($events),
            });
            calendar.render();
        });
    </script>
@endpush
