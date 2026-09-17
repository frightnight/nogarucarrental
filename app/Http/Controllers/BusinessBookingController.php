<?php

namespace App\Http\Controllers;

use App\BusinessFeature;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Car;
use App\Models\DriverBookingApplication;
use App\Models\DriverEvaluation;
use App\Models\User;
use App\Notifications\BookingFinalizedNotification;
use App\Notifications\BookingPaymentConfirmedNotification;
use App\Services\DriverBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BusinessBookingController extends Controller
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $sort = $request->string('sort', 'booking_input')->value();
        abort_unless(in_array($sort, ['booking_input', 'booking_schedule'], true), 404);
        $bookingsQuery = $business->bookings()->with(['user', 'car', 'businessPaymentMethod']);

        if ($sort === 'booking_schedule') {
            $bookingsQuery->orderBy('pickup_date')->orderBy('pickup_time');
        } else {
            $bookingsQuery->latest();
        }

        $bookings = $bookingsQuery->get();

        return view('panels.bookings.index', compact('business', 'bookings', 'sort'));
    }

    public function calendar(): View
    {
        $business = $this->business();
        $bookings = $business->bookings()
            ->with('car')
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->orderBy('pickup_date')
            ->get();

        $events = $bookings->map(function (Booking $booking): array {
            $start = $booking->pickup_date?->format('Y-m-d').'T'.$booking->pickup_time;
            $end = $booking->return_date?->format('Y-m-d').'T'.$booking->return_time;

            return [
                'title' => ($booking->car?->car_model ?: $booking->preferred_vehicle).' · #'.$booking->id,
                'start' => $start,
                'end' => $end,
                'url' => route('business.bookings.edit', $booking),
                'classNames' => match ($booking->status) {
                    'confirmed', 'ongoing' => ['bg-success-subtle', 'text-success', 'border-start', 'border-3', 'border-success'],
                    'payment_submitted' => ['bg-warning-subtle', 'text-warning', 'border-start', 'border-3', 'border-warning'],
                    default => ['bg-primary-subtle', 'text-primary', 'border-start', 'border-3', 'border-primary'],
                },
            ];
        });

        return view('panels.bookings.calendar', compact('events'));
    }

    public function crm(Request $request): View
    {
        $business = $this->business();
        $search = trim($request->string('search')->value());
        $bookings = $business->bookings()
            ->with(['user.clientProfile', 'car'])
            ->latest('pickup_date')
            ->get();

        $clients = $bookings
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->filter(function (User $client) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                $profile = $client->clientProfile;
                $phoneNumbers = collect($profile?->mobile_numbers ?? [])->pluck('number')->implode(' ');
                $haystack = implode(' ', array_filter([
                    $client->name,
                    $client->email,
                    $profile?->full_name,
                    $profile?->email_address,
                    $phoneNumbers,
                ]));

                return str_contains(strtolower($haystack), strtolower($search));
            })
            ->values();

        $bookingCounts = $bookings->countBy('user_id');
        $lastBookings = $bookings->groupBy('user_id')->map->first();
        $clients->each(function (User $client) use ($bookingCounts, $lastBookings): void {
            $client->setAttribute('rental_history_count', $bookingCounts[$client->id] ?? 0);
            $client->setAttribute('last_booking', $lastBookings[$client->id] ?? null);
        });

        $activeStatuses = ['reserved', 'payment_submitted', 'confirmed', 'ongoing'];
        $metrics = [
            'customers' => $clients->count(),
            'repeat_customers' => $clients->filter(fn (User $client): bool => $client->rental_history_count > 1)->count(),
            'active_rentals' => $bookings->whereIn('status', $activeStatuses)->count(),
            'lifetime_revenue' => $bookings->sum(fn (Booking $booking): float => (float) ($booking->final_rate ?? $booking->initial_rate ?? 0)),
        ];
        $recentBookings = $bookings->take(8);

        return view('panels.crm.index', compact('business', 'clients', 'metrics', 'recentBookings', 'search'));
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

        $booking->load(['user.clientProfile.identityDocuments', 'car.rates', 'business', 'driver', 'driverEvaluations', 'driverApplications.driver', 'driverApplications.rate']);
        $vehicles = $booking->business->cars()->with('rates')->orderBy('car_model')->get();

        return view('panels.bookings.edit', compact('booking', 'vehicles'));
    }

    public function selectDriverApplication(Request $request, Booking $booking, DriverBookingApplication $application): RedirectResponse
    {
        $this->ensureBookingBelongsToBusiness($booking);
        abort_unless($application->booking_id === $booking->id && $application->status === 'pending', 404);

        $booking->update(['driver_license_number' => $application->driver_license_number, 'driver_fee' => $application->quoted_rate]);
        $booking->driverApplications()->whereKeyNot($application->id)->where('status', 'pending')->update(['status' => 'rejected']);
        $application->update(['status' => 'selected']);

        return back()->with('success', 'Driver selected for this booking.');
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
        $booking->user?->notify(new BookingFinalizedNotification($booking));

        return redirect()->route('business.bookings.index')->with('success', 'Booking finalized and sent to the client for payment.');
    }

    public function confirmPayment(Booking $booking, DriverBookingService $bookingService): RedirectResponse
    {
        $this->ensureBookingBelongsToBusiness($booking);
        abort_unless($this->business()->canUseFeature(BusinessFeature::PaymentTracking), 403, 'Payment tracking is not included in your current plan.');

        abort_unless($booking->status === 'payment_submitted', 422);

        $booking->update([
            'status' => 'confirmed',
            'payment_confirmed_at' => now(),
        ]);
        $bookingService->notifyEligibleDrivers($booking->fresh());
        $booking->user?->notify(new BookingPaymentConfirmedNotification($booking));

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

    public function evaluateDriver(Request $request, Booking $booking): RedirectResponse
    {
        $this->ensureBookingBelongsToBusiness($booking);
        abort_unless($booking->status === 'completed', 422, 'Only completed trips can be evaluated.');
        abort_unless($booking->driver_license_number, 422, 'This booking has no assigned driver.');

        $validated = $request->validate([
            'overall_rating' => ['required', 'integer', 'between:1,5'],
            'punctuality_rating' => ['nullable', 'integer', 'between:1,5'],
            'safety_rating' => ['nullable', 'integer', 'between:1,5'],
            'service_rating' => ['nullable', 'integer', 'between:1,5'],
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        DriverEvaluation::updateOrCreate(
            ['booking_id' => $booking->id, 'business_id' => $booking->business_id],
            $validated + [
                'driver_license_number' => $booking->driver_license_number,
                'evaluated_by' => Auth::id(),
            ],
        );

        return back()->with('success', 'Driver evaluation saved.');
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
