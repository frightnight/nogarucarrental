<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverBookingApplication;
use App\Models\DriverRate;
use App\Services\DriverBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverPortalController extends Controller
{
    public function index(Request $request, DriverBookingService $bookingService): View
    {
        $driver = Driver::query()->where('user_id', $request->user()->id)->firstOrFail();
        $bookings = $driver->bookings()->with(['business', 'car'])->latest('pickup_date')->get();
        $evaluations = $driver->evaluations()->with('business')->latest()->get();
        $rates = $driver->rates()->where('is_active', true)->orderBy('trip_type')->get();
        $openBookings = Booking::query()
            ->with(['business', 'car'])
            ->whereNull('driver_license_number')
            ->whereIn('status', ['reserved', 'finalized', 'payment_submitted', 'confirmed'])
            ->where('rental_type', '!=', 'self_drive')
            ->latest('pickup_date')
            ->get()
            ->map(function ($booking) use ($driver, $bookingService) {
                $booking->setAttribute('trip_type', $bookingService->tripType($booking));
                $booking->setAttribute('driver_application', $booking->driverApplications()->where('driver_license_number', $driver->license_number)->first());

                return $booking;
            });

        return view('panels.driver-portal', compact('driver', 'bookings', 'evaluations', 'rates', 'openBookings'));
    }

    public function storeRate(Request $request): RedirectResponse
    {
        $driver = Driver::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($driver->approval_status === 'approved', 403);
        $validated = $request->validate([
            'trip_type' => ['required', 'in:airport_transfer,point_to_point,city_tour,full_day,out_of_town,multi_day,wedding_event,corporate'],
            'rate_type' => ['required', 'in:per_trip,per_day,per_hour,per_event'],
            'amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'overtime_rate' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ]);
        DriverRate::updateOrCreate(
            ['driver_license_number' => $driver->license_number, 'trip_type' => $validated['trip_type']],
            ['rate_type' => $validated['rate_type'], 'amount' => $validated['amount'], 'overtime_rate' => $validated['overtime_rate'] ?? 0, 'is_active' => true],
        );

        return back()->with('success', 'Driver trip rate saved.');
    }

    public function destroyRate(Request $request, DriverRate $rate): RedirectResponse
    {
        $driver = Driver::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($rate->driver_license_number === $driver->license_number, 404);
        $rate->delete();

        return back()->with('success', 'Driver trip rate removed.');
    }

    public function apply(Request $request, Booking $booking): RedirectResponse
    {
        $driver = Driver::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($driver->approval_status === 'approved', 403);
        abort_unless($booking->driver_license_number === null && in_array($booking->status, ['reserved', 'finalized', 'payment_submitted', 'confirmed'], true) && $booking->rental_type !== 'self_drive', 422);
        $validated = $request->validate([
            'driver_rate_id' => ['nullable', 'integer'],
            'quoted_rate' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'quoted_overtime_rate' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);
        $rate = null;
        if ($validated['driver_rate_id'] ?? null) {
            $rate = $driver->rates()->whereKey($validated['driver_rate_id'])->firstOrFail();
        }
        DriverBookingApplication::updateOrCreate(
            ['booking_id' => $booking->id, 'driver_license_number' => $driver->license_number],
            ['driver_rate_id' => $rate?->id, 'quoted_rate' => $validated['quoted_rate'], 'quoted_overtime_rate' => $validated['quoted_overtime_rate'] ?? $rate?->overtime_rate ?? 0, 'message' => $validated['message'] ?? null, 'status' => 'pending'],
        );

        return back()->with('success', 'Application submitted to the business.');
    }
}
