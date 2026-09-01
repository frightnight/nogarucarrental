<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestCheckoutController extends Controller
{
    public function store(Request $request, Car $car): RedirectResponse
    {
        $validated = $request->validate([
            'trip_length' => ['required', 'in:dropoff,daily'],
            'pickup_at' => ['required', 'date', 'after:now'],
            'return_at' => ['nullable', 'date', 'after:pickup_at', 'required_if:trip_length,daily'],
            'pickup_location' => ['nullable', 'string', 'max:255', 'required_if:trip_length,dropoff'],
            'dropoff_location' => ['nullable', 'string', 'max:255', 'required_if:trip_length,dropoff'],
            'pickup_latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_if:trip_length,dropoff'],
            'pickup_longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_if:trip_length,dropoff'],
            'dropoff_latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_if:trip_length,dropoff'],
            'dropoff_longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_if:trip_length,dropoff'],
            'total_distance_km' => ['nullable', 'numeric', 'min:0', 'required_if:trip_length,dropoff'],
        ]);

        $pickupAt = Carbon::parse($validated['pickup_at']);
        $returnAt = $validated['trip_length'] === 'daily'
            ? Carbon::parse($validated['return_at'])
            : $pickupAt;
        $isUnavailable = $car->status !== 'available' || $car->bookings()
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->whereDate('pickup_date', '<=', $returnAt->toDateString())
            ->whereDate('return_date', '>=', $pickupAt->toDateString())
            ->exists();

        if ($isUnavailable) {
            return back()->withInput()->withErrors(['pickup_at' => 'This vehicle is no longer available for the selected date.']);
        }

        $rate = (float) ($car->rates()->whereIn('name', ['24hrs', 'Daily'])->min('value') ?? 0);
        $pickupDropoffFee = (float) ($car->rates()->where('name', 'Pick-up & Drop-off')->value('value') ?? 0);
        $rentalDays = max(1, (int) ceil($pickupAt->diffInMinutes($returnAt) / 1440));
        $fuelPricePerLiter = match ($car->fuel_type) {
            'Diesel Premium' => (float) $car->business->diesel_premium_price_per_liter,
            'Diesel Regular' => (float) $car->business->diesel_regular_price_per_liter,
            'Gasoline Premium' => (float) $car->business->gasoline_premium_price_per_liter,
            'Gasoline Regular' => (float) $car->business->gasoline_regular_price_per_liter,
            default => 0,
        };
        $fuelCost = $validated['trip_length'] === 'dropoff' && (float) $car->fuel_consumption_km_per_liter > 0
            ? ((float) $validated['total_distance_km'] / (float) $car->fuel_consumption_km_per_liter) * $fuelPricePerLiter
            : 0;
        $distanceFee = $validated['trip_length'] === 'dropoff' ? $pickupDropoffFee : 0;
        $baseRate = $validated['trip_length'] === 'daily' ? $rate * $rentalDays : $pickupDropoffFee + $fuelCost + $driverDailyRate;
        $driverDailyRate = (float) ($car->business->drivers()
            ->wherePivot('is_available', true)
            ->wherePivot('is_default', true)
            ->value('business_driver.daily_rate') ?? 0);
        $carWashFee = (float) ($car->rates()->where('name', 'Car Wash Fee')->value('value') ?? 0);
        $dailyDiscount = (float) ($car->rates()->where('name', 'Daily Discount')->value('value') ?? 0);
        $driverFee = $validated['trip_length'] === 'daily' ? $driverDailyRate * $rentalDays : 0;
        $carWashFee = $validated['trip_length'] === 'daily' ? $carWashFee : 0;
        $discount = $validated['trip_length'] === 'daily' ? $dailyDiscount * $rentalDays : 0;
        $subtotal = $baseRate + $driverFee + $carWashFee - $discount;
        $finalRate = round($subtotal * 1.12, 2);
        $booking = Booking::create([
            'user_id' => $request->user()?->id,
            'guest_token' => (string) Str::uuid(),
            'business_id' => $car->business_id,
            'car_id' => $car->id,
            'driver_license_number' => $car->business->drivers()
                ->wherePivot('is_default', true)
                ->wherePivot('is_available', true)
                ->value('drivers.license_number'),
            'rental_type' => 'with_driver',
            'pickup_date' => $pickupAt->toDateString(),
            'pickup_time' => $pickupAt->format('H:i'),
            'pickup_location' => $validated['pickup_location'] ?? 'To be arranged',
            'pickup_latitude' => $validated['pickup_latitude'] ?? null,
            'pickup_longitude' => $validated['pickup_longitude'] ?? null,
            'destination_itinerary' => $validated['dropoff_location'] ?? 'Daily rental',
            'preferred_vehicle' => $car->car_model ?: $car->vehicle_type,
            'passengers_count' => $car->seats ?: 1,
            'return_date' => $returnAt->toDateString(),
            'return_time' => $returnAt->format('H:i'),
            'return_location' => $validated['dropoff_location'] ?? 'To be arranged',
            'dropoff_latitude' => $validated['dropoff_latitude'] ?? null,
            'dropoff_longitude' => $validated['dropoff_longitude'] ?? null,
            'handover_option' => 'with_driver',
            'initial_rate' => $baseRate,
            'final_rate' => $finalRate,
            'delivery_fee' => $distanceFee + $fuelCost + $driverFee + ($validated['trip_length'] === 'dropoff' ? $driverDailyRate : 0) + $carWashFee - $discount,
            'total_distance_km' => $validated['total_distance_km'] ?? null,
            'reservation_fee' => $finalRate ? round($finalRate * 0.2, 2) : null,
            'status' => 'pending_payment',
        ]);

        return redirect()->route('guest-checkout.show', ['booking' => $booking, 'token' => $booking->guest_token]);
    }

    public function show(Booking $booking, string $token): View
    {
        abort_unless(hash_equals((string) $booking->guest_token, $token), 404);

        return view('public.guest-checkout', ['booking' => $booking->load(['business', 'car']), 'token' => $token]);
    }

    public function payment(Request $request, Booking $booking, string $token): RedirectResponse
    {
        abort_unless(hash_equals((string) $booking->guest_token, $token), 404);

        $validated = $request->validate([
            'payment_method' => ['required', 'in:gcash,paymaya,bank_transaction'],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $paymentProof = $request->file('payment_proof');
        if (! $paymentProof instanceof UploadedFile || ! $paymentProof->isValid()) {
            return back()->withErrors(['payment_proof' => 'Please choose a valid payment screenshot.']);
        }

        $path = 'payment-proofs/'.$booking->id.'/'.Str::uuid().'.'.$paymentProof->extension();
        Storage::disk('public')->put($path, file_get_contents($paymentProof->getPathname()));
        $booking->update([
            'payment_method' => $validated['payment_method'],
            'payment_proof_path' => '/storage/'.$path,
            'payment_submitted_at' => now(),
            'status' => 'payment_submitted',
        ]);

        return redirect()->route('vehicles.show', $booking->car)->with('status', 'Payment submitted. The rental business will review your booking.');
    }
}
