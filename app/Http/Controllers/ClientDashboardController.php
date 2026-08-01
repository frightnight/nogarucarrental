<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientDashboardController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::query()
            ->with(['business', 'car'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        $notifications = Auth::user()->unreadNotifications;

        return view('panels.client', ['role' => 'Client', 'bookings' => $bookings, 'notifications' => $notifications]);
    }
}
