<?php

namespace App\Http\Controllers;

use App\BusinessFeature;
use App\Models\Booking;
use App\Models\Business;
use App\Models\User;
use App\Models\VehicleInspection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RentalManagementController extends Controller
{
    public function dashboard(): View
    {
        $business = $this->business();
        $today = today();
        $activeStatuses = ['reserved', 'confirmed', 'ongoing', 'finalized', 'payment_submitted'];
        $cars = $business->cars()->get();

        return view('panels.rental.dashboard', [
            'business' => $business,
            'todayBookings' => $business->bookings()->with(['car', 'user'])->whereDate('pickup_date', $today)->orderBy('pickup_time')->get(),
            'upcomingReturns' => $business->bookings()->with(['car', 'user'])->whereIn('status', $activeStatuses)->whereDate('return_date', '>=', $today)->orderBy('return_date')->take(5)->get(),
            'vehicleCount' => $cars->count(),
            'rentedCount' => $business->bookings()->whereIn('status', ['ongoing', 'confirmed'])->whereDate('pickup_date', '<=', $today)->whereDate('return_date', '>=', $today)->distinct('car_id')->count('car_id'),
            'expiringVehicles' => $cars->filter(fn ($car) => $car->registration_expires_at?->isBefore(now()->addDays(30)) || $car->insurance_expires_at?->isBefore(now()->addDays(30))),
        ]);
    }

    public function createBooking(): View
    {
        $business = $this->business();

        return view('panels.rental.booking-create', [
            'business' => $business,
            'cars' => $business->cars()->where('status', 'available')->orderBy('car_model')->get(),
            'customers' => User::role('client')->with('clientProfile')->orderBy('name')->get(),
        ]);
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $business = $this->business();
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'], 'car_id' => ['required', 'exists:cars,id'],
            'pickup_date' => ['required', 'date'], 'pickup_time' => ['required', 'date_format:H:i'],
            'return_date' => ['required', 'date', 'after_or_equal:pickup_date'], 'return_time' => ['required', 'date_format:H:i'],
            'pickup_location' => ['required', 'string', 'max:255'], 'return_location' => ['nullable', 'string', 'max:255'],
            'destination_itinerary' => ['nullable', 'string', 'max:1000'], 'final_rate' => ['required', 'numeric', 'min:0'],
        ]);
        $car = $business->cars()->findOrFail($validated['car_id']);

        Booking::create($validated + [
            'business_id' => $business->id, 'rental_type' => 'self_drive', 'preferred_vehicle' => $car->car_model ?: $car->vehicle_type,
            'passengers_count' => 1, 'handover_option' => 'manual', 'return_location' => $validated['return_location'] ?? $validated['pickup_location'],
            'destination_itinerary' => $validated['destination_itinerary'] ?? 'Not specified', 'initial_rate' => $validated['final_rate'],
            'reservation_fee' => 0, 'delivery_fee' => 0, 'pickup_fee' => 0, 'status' => 'reserved',
        ]);

        return redirect()->route('business.bookings.index')->with('success', 'Manual booking created.');
    }

    public function agreement(Booking $booking): View
    {
        $this->ensureBooking($booking);
        $this->ensureFeature(BusinessFeature::RentalAgreement);
        $booking->load(['business', 'car', 'user.clientProfile']);

        return view('panels.rental.agreement', compact('booking'));
    }

    public function inspection(Booking $booking): View
    {
        $this->ensureBooking($booking);
        $this->ensureFeature(BusinessFeature::VehicleInspection);
        $booking->load(['car', 'inspections']);

        return view('panels.rental.inspection', compact('booking'));
    }

    public function storeInspection(Request $request, Booking $booking): RedirectResponse
    {
        $this->ensureBooking($booking);
        $this->ensureFeature(BusinessFeature::VehicleInspection);
        $validated = $request->validate([
            'stage' => ['required', 'in:before,after'], 'checklist' => ['nullable', 'array'], 'damage_notes' => ['nullable', 'string', 'max:4000'],
            'photos' => ['nullable', 'array'], 'photos.*' => ['image', 'max:5120'],
        ]);
        $photos = [];
        foreach ($request->file('photos', []) as $photo) {
            $photos[] = $photo->store('inspections/'.$booking->id, 'public');
        }
        VehicleInspection::updateOrCreate(
            ['booking_id' => $booking->id, 'stage' => $validated['stage']],
            ['car_id' => $booking->car_id, 'inspected_by' => Auth::id(), 'checklist' => $validated['checklist'] ?? [], 'damage_notes' => $validated['damage_notes'] ?? null, 'photo_paths' => $photos]
        );

        return back()->with('success', ucfirst($validated['stage']).' inspection saved.');
    }

    public function reports(): View
    {
        $business = $this->business();
        $this->ensureFeature(BusinessFeature::Reports);
        $monthStart = now()->startOfMonth();
        $monthBookings = $business->bookings()->whereBetween('pickup_date', [$monthStart, now()->endOfMonth()]);
        $completedRevenue = (clone $monthBookings)->whereIn('status', ['completed', 'ongoing'])->sum('final_rate');
        $cars = $business->cars()->withCount(['bookings as monthly_bookings' => fn ($query) => $query->whereBetween('pickup_date', [$monthStart, now()->endOfMonth()])])->get();

        return view('panels.rental.reports', ['business' => $business, 'monthlyBookings' => $monthBookings->count(), 'monthlyRevenue' => $completedRevenue, 'cars' => $cars]);
    }

    private function business(): Business
    {
        $business = Auth::user()->businesses()->first();
        abort_unless($business instanceof Business, 403);

        return $business;
    }

    private function ensureBooking(Booking $booking): void
    {
        abort_unless($booking->business_id === $this->business()->id, 403);
    }

    private function ensureFeature(BusinessFeature $feature): void
    {
        abort_unless($this->business()->canUseFeature($feature), 403, 'This feature is not included in your current plan.');
    }
}
