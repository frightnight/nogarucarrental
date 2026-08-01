<?php

namespace App\Http\Controllers;

use App\BusinessFeature;
use App\Models\Booking;
use App\Models\Business;
use App\Notifications\BookingFinalizedNotification;
use App\Notifications\BookingPaymentConfirmedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BusinessBookingController extends Controller
{
    public function index(): View
    {
        $business = $this->business();
        $bookings = $business->bookings()->with(['user', 'car'])->latest()->get();

        return view('panels.bookings.index', compact('business', 'bookings'));
    }

    public function clients(): View
    {
        $business = $this->business();
        $clients = $business->bookings()
            ->with('user.clientProfile.identityDocuments')
            ->latest()
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->values();

        $bookingCounts = $business->bookings()
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $clients->each(function (User $client) use ($bookingCounts): void {
            $client->setAttribute('rental_history_count', $bookingCounts[$client->id] ?? 0);
        });

        return view('panels.clients.index', compact('clients'));
    }

    public function showClient(User $client): View
    {
        $business = $this->business();
        abort_unless($business->bookings()->where('user_id', $client->id)->exists(), 404);

        $client->load('clientProfile.identityDocuments');
        $bookings = $business->bookings()
            ->with('car')
            ->where('user_id', $client->id)
            ->latest('pickup_date')
            ->get();

        return view('panels.clients.show', compact('client', 'bookings'));
    }

    public function edit(Booking $booking): View
    {
        $this->ensureBookingBelongsToBusiness($booking);

        $booking->load(['user.clientProfile.identityDocuments', 'car.rates', 'business']);
        $vehicles = $booking->business->cars()->with('rates')->orderBy('car_model')->get();

        return view('panels.bookings.edit', compact('booking', 'vehicles'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $this->ensureBookingBelongsToBusiness($booking);

        $validated = $request->validate([
            'rental_type' => ['required', 'in:self_drive,with_driver,airport_pickup,city_tour,out_of_town,wedding_event,corporate'],
            'passengers_count' => ['required', 'integer', 'min:1', 'max:20'],
            'destination_itinerary' => ['required', 'string', 'max:1000'],
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'pickup_location' => ['nullable', 'string', 'max:255', 'required_unless:rental_type,self_drive'],
            'return_date' => ['nullable', 'date', 'after_or_equal:pickup_date', 'required_if:rental_type,self_drive'],
            'return_time' => ['nullable', 'date_format:H:i', 'required_if:rental_type,self_drive'],
            'return_location' => ['nullable', 'string', 'max:255', 'required_if:rental_type,self_drive'],
            'handover_option' => ['nullable', 'string', 'max:255', 'required_if:rental_type,self_drive'],
            'handover_other' => ['nullable', 'string', 'max:255'],
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'final_rate' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'pickup_fee' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'reservation_fee' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'owner_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $car = Car::query()->where('business_id', $booking->business_id)->findOrFail($validated['car_id']);

        $booking->update($validated + [
            'preferred_vehicle' => $car->car_model ?: $car->vehicle_type,
            'pickup_location' => $validated['pickup_location'] ?? 'Not applicable',
            'return_date' => $validated['return_date'] ?? $validated['pickup_date'],
            'return_time' => $validated['return_time'] ?? $validated['pickup_time'],
            'handover_option' => $validated['handover_option'] ?? 'not_applicable',
            'delivery_fee' => $validated['delivery_fee'] ?? 0,
            'pickup_fee' => $validated['pickup_fee'] ?? 0,
            'status' => 'finalized',
            'finalized_at' => now(),
        ]);
        $booking->user->notify(new BookingFinalizedNotification($booking));

        return redirect()->route('business.bookings.index')->with('success', 'Booking finalized and sent to the client for payment.');
    }

    public function confirmPayment(Booking $booking): RedirectResponse
    {
        $this->ensureBookingBelongsToBusiness($booking);
        abort_unless($this->business()->canUseFeature(BusinessFeature::PaymentTracking), 403, 'Payment tracking is not included in your current plan.');

        abort_unless($booking->status === 'payment_submitted', 422);

        $booking->update([
            'status' => 'confirmed',
            'payment_confirmed_at' => now(),
        ]);
        $booking->user->notify(new BookingPaymentConfirmedNotification($booking));

        return redirect()->route('business.bookings.index')->with('success', 'Reservation payment confirmed and client notified.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $this->ensureBookingBelongsToBusiness($booking);

        $validated = $request->validate([
            'status' => ['required', 'in:reserved,ongoing,completed,cancelled'],
        ]);

        $booking->update($validated);

        return back()->with('success', 'Booking status updated.');
    }

    private function ensureBookingBelongsToBusiness(Booking $booking): void
    {
        abort_unless($booking->business_id === $this->business()->id, 403);
    }

    private function business(): Business
    {
        $business = Auth::user()->businesses()->first();

        abort_unless($business instanceof Business, 403);

        return $business;
    }
}
