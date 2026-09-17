<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        $business = $this->business();
        $drivers = Driver::query()
            ->where('approval_status', 'approved')
            ->with(['rates' => fn ($query) => $query->where('is_active', true)->orderBy('trip_type')])
            ->orderBy('full_name')
            ->get();

        return view('panels.drivers.index', compact('business', 'drivers'));
    }

    public function show(Driver $driver): View
    {
        $business = $this->business();
        $driver = Driver::query()
            ->whereKey($driver->getKey())
            ->where('approval_status', 'approved')
            ->with('rates')
            ->firstOrFail();

        $bookings = $driver->bookings()
            ->where('business_id', $business->id)
            ->with(['user.clientProfile', 'car'])
            ->orderBy('pickup_date')
            ->get();
        $upcomingTravels = $bookings->filter(fn ($booking): bool => ($booking->pickup_date?->isToday() || $booking->pickup_date?->isFuture()) && ! in_array($booking->status, ['completed', 'cancelled', 'rejected'], true));
        $completedTravels = $bookings->filter(fn ($booking): bool => $booking->status === 'completed');

        return view('panels.drivers.show', compact('business', 'driver', 'upcomingTravels', 'completedTravels'));
    }

    private function business(): Business
    {
        $business = Auth::user()->businesses()->first();

        abort_unless($business instanceof Business, 403);

        return $business;
    }
}
