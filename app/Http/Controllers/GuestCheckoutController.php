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
        if ($request->input('ride_type') === 'self_drive') {
            return $this->storeSelfDrive($request, $car);
        }

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

        $pickupDropoffFee = (float) ($car->rates()->where('name', 'Pick-up & Drop-off')->value('value') ?? 0);
        $rentalMinutes = $pickupAt->diffInMinutes($returnAt);
        $rentalPeriods = $rentalMinutes <= 720 ? 1 : max(1, (int) ceil($rentalMinutes / 1440));
        $vehicleRate = $rentalMinutes <= 720
            ? (float) ($car->rates()->where('name', '12hrs')->value('value') ?? 0)
            : (float) ($car->rates()->whereIn('name', ['24hrs', 'Daily'])->min('value') ?? 0);
        $driverDailyRate = (float) ($car->business->drivers()
            ->wherePivot('is_available', true)
            ->wherePivot('is_default', true)
            ->value('business_driver.daily_rate') ?? 0);
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
        $baseRate = $validated['trip_length'] === 'daily' ? $vehicleRate * $rentalPeriods : $pickupDropoffFee + $fuelCost + $driverDailyRate;
        $carWashFee = (float) ($car->rates()->where('name', 'Car Wash Fee')->value('value') ?? 0);
        $dailyDiscount = (float) ($car->rates()->where('name', 'Daily Discount')->value('value') ?? 0);
        $driverFee = $validated['trip_length'] === 'daily' ? $driverDailyRate * $rentalPeriods : 0;
        $carWashFee = $validated['trip_length'] === 'daily' ? $carWashFee : 0;
        $discount = $validated['trip_length'] === 'daily' ? $dailyDiscount * $rentalPeriods : 0;
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
            'reservation_fee' => $finalRate ? round(($finalRate * 0.2) / 100) * 100 : null,
            'status' => 'pending_payment',
        ]);

        return redirect()->route('guest-checkout.show', ['booking' => $booking, 'token' => $booking->guest_token]);
    }

    private function storeSelfDrive(Request $request, Car $car): RedirectResponse
    {
        $validated = $request->validate([
            'self_drive_start_at' => ['required', 'date', 'after:now'],
            'self_drive_end_at' => ['required', 'date', 'after:self_drive_start_at'],
            'self_drive_delivery_location' => ['required', 'string', 'max:255'],
            'self_drive_return_location' => ['required', 'string', 'max:255'],
            'self_drive_delivery_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'self_drive_delivery_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'self_drive_return_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'self_drive_return_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'self_drive_delivery_return_fee' => ['nullable', 'numeric', 'min:0'],
            'self_drive_delivery_return_distance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $pickupAt = Carbon::parse($validated['self_drive_start_at']);
        $returnAt = Carbon::parse($validated['self_drive_end_at']);
        $isUnavailable = $car->status !== 'available' || $car->bookings()
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->whereDate('pickup_date', '<=', $returnAt->toDateString())
            ->whereDate('return_date', '>=', $pickupAt->toDateString())
            ->exists();

        if ($isUnavailable) {
            return back()->withInput()->withErrors(['self_drive_start_at' => 'This vehicle is no longer available for the selected dates.']);
        }

        $rentalMinutes = $pickupAt->diffInMinutes($returnAt);
        $rentalPeriods = $rentalMinutes <= 720 ? 1 : max(1, (int) ceil($rentalMinutes / 1440));
        $vehicleRate = $rentalMinutes <= 720
            ? (float) ($car->rates()->where('name', '12hrs')->value('value') ?? 0)
            : (float) ($car->rates()->whereIn('name', ['24hrs', 'Daily'])->min('value') ?? 0);
        $baseRate = $vehicleRate * $rentalPeriods;
        $carWashFee = (float) ($car->rates()->where('name', 'Car Wash Fee')->value('value') ?? 0);
        $deliveryReturnFee = $this->selfDriveDeliveryReturnFee($validated);
        $finalRate = round(($baseRate + $carWashFee + $deliveryReturnFee) * 1.12, 2);
        $booking = Booking::create([
            'user_id' => $request->user()?->id,
            'guest_token' => (string) Str::uuid(),
            'business_id' => $car->business_id,
            'car_id' => $car->id,
            'rental_type' => 'self_drive',
            'pickup_date' => $pickupAt->toDateString(),
            'pickup_time' => $pickupAt->format('H:i'),
            'pickup_location' => $validated['self_drive_delivery_location'],
            'pickup_latitude' => $validated['self_drive_delivery_latitude'] ?? null,
            'pickup_longitude' => $validated['self_drive_delivery_longitude'] ?? null,
            'destination_itinerary' => 'Self-drive rental',
            'preferred_vehicle' => $car->car_model ?: $car->vehicle_type,
            'passengers_count' => $car->seats ?: 1,
            'return_date' => $returnAt->toDateString(),
            'return_time' => $returnAt->format('H:i'),
            'return_location' => $validated['self_drive_return_location'],
            'dropoff_latitude' => $validated['self_drive_return_latitude'] ?? null,
            'dropoff_longitude' => $validated['self_drive_return_longitude'] ?? null,
            'handover_option' => 'customer_pickup',
            'initial_rate' => $baseRate,
            'final_rate' => $finalRate,
            'delivery_fee' => $deliveryReturnFee,
            'total_distance_km' => $validated['self_drive_delivery_return_distance'] ?? 0,
            'reservation_fee' => round(($finalRate * 0.2) / 100) * 100,
            'status' => 'pending_payment',
        ]);

        return redirect()->route('guest-checkout.show', ['booking' => $booking, 'token' => $booking->guest_token]);
    }

    /** @param array<string, mixed> $validated */
    private function selfDriveDeliveryReturnFee(array $validated): float
    {
        $deliveryIsGarage = strtolower(trim($validated['self_drive_delivery_location'])) === 'pick-up to garage';
        $returnIsGarage = strtolower(trim($validated['self_drive_return_location'])) === 'return to garage';

        if ($deliveryIsGarage && $returnIsGarage) {
            return 0;
        }

        return (float) ($validated['self_drive_delivery_return_fee'] ?? 0);
    }

    public function show(Booking $booking, string $token): View
    {
        abort_unless(hash_equals((string) $booking->guest_token, $token), 404);

        return view('public.guest-checkout', ['booking' => $booking->load(['business', 'car']), 'token' => $token, 'paymentMethods' => $booking->business->paymentMethods]);
    }

    public function payment(Request $request, Booking $booking, string $token): RedirectResponse
    {
        abort_unless(hash_equals((string) $booking->guest_token, $token), 404);

        $validated = $request->validate([
            'guest_first_name' => ['required', 'string', 'max:100'],
            'guest_last_name' => ['required', 'string', 'max:100'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:50'],
            'pickup_at' => ['required', 'date'],
            'return_at' => ['nullable', 'date', 'after:pickup_at'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'return_location' => ['required', 'string', 'max:255'],
            'pickup_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'pickup_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'dropoff_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'dropoff_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'business_payment_method_id' => ['required', 'integer'],
            'payment_reference_number' => ['required', 'string', 'max:100'],
            'payment_proof' => ['required', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'],
            'special_request' => ['nullable', 'string', 'max:2000'],
        ]);

        $paymentMethod = $booking->business->paymentMethods()->findOrFail($validated['business_payment_method_id']);
        $pickupAt = Carbon::parse($validated['pickup_at']);
        $returnAt = isset($validated['return_at']) && $validated['return_at'] !== null
            ? Carbon::parse($validated['return_at'])
            : $pickupAt;
        $paymentProof = $request->file('payment_proof');
        if (! $paymentProof instanceof UploadedFile || ! $paymentProof->isValid()) {
            return back()->withErrors(['payment_proof' => 'Please choose a valid payment screenshot.']);
        }

        $path = 'payment-proofs/'.$booking->id.'/'.Str::uuid().'.'.$paymentProof->extension();
        Storage::disk('public')->put($path, file_get_contents($paymentProof->getPathname()));
        $booking->update([
            'guest_first_name' => $validated['guest_first_name'],
            'guest_last_name' => $validated['guest_last_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],
            'pickup_date' => $pickupAt->toDateString(),
            'pickup_time' => $pickupAt->format('H:i'),
            'return_date' => $returnAt->toDateString(),
            'return_time' => $returnAt->format('H:i'),
            'pickup_location' => $validated['pickup_location'],
            'return_location' => $validated['return_location'],
            'destination_itinerary' => $booking->total_distance_km !== null ? $validated['return_location'] : $booking->destination_itinerary,
            'pickup_latitude' => $validated['pickup_latitude'] ?? null,
            'pickup_longitude' => $validated['pickup_longitude'] ?? null,
            'dropoff_latitude' => $validated['dropoff_latitude'] ?? null,
            'dropoff_longitude' => $validated['dropoff_longitude'] ?? null,
            'payment_method' => $paymentMethod->payment_method,
            'business_payment_method_id' => $paymentMethod->id,
            'payment_reference_number' => $validated['payment_reference_number'],
            'special_request' => $validated['special_request'] ?? null,
            'payment_proof_path' => '/storage/'.$path,
            'payment_submitted_at' => now(),
            'status' => 'payment_submitted',
        ]);

        return redirect()->route('vehicles.show', $booking->car)->with('status', 'Payment submitted. The rental business will review your booking.');
    }
}
