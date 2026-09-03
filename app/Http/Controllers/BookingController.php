<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function details(Request $request): View
    {
        $car = Car::query()->with(['business', 'rates'])->findOrFail($request->integer('car_id', $request->integer('car')));
        $business = Business::query()->findOrFail($request->integer('business_id', $request->integer('business', $car->business_id)));

        abort_unless($business->id === $car->business_id, 404);

        return view('public.booking-details-simple', compact('business', 'car'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'rental_type' => ['required', 'in:self_drive,with_driver,airport_pickup,city_tour,out_of_town,wedding_event,corporate'],
            'passengers_count' => ['required', 'integer', 'min:1', 'max:20'],
            'destination_itinerary' => ['required', 'string', 'max:1000'],
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'pickup_location' => ['nullable', 'string', 'max:255', 'required_unless:rental_type,self_drive'],
            'return_date' => ['nullable', 'date', 'after_or_equal:pickup_date', 'required_if:rental_type,self_drive'],
            'return_time' => ['nullable', 'date_format:H:i', 'required_if:rental_type,self_drive'],
            'return_location' => ['nullable', 'string', 'max:255', 'required_if:rental_type,self_drive'],
            'handover_option' => ['nullable', 'in:customer_pickup,other', 'required_if:rental_type,self_drive'],
            'handover_other' => ['nullable', 'string', 'max:255', 'required_if:handover_option,other'],
        ]);

        $car = Car::query()->with('rates')->findOrFail($validated['car_id']);
        abort_unless($car->business_id === (int) $validated['business_id'], 422);

        $isUnavailable = $car->status !== 'available' || $car->bookings()
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->whereDate('pickup_date', '<=', $validated['return_date'] ?? $validated['pickup_date'])
            ->whereDate('return_date', '>=', $validated['pickup_date'])
            ->exists();

        if ($isUnavailable) {
            return back()->withInput()->withErrors(['car_id' => 'This vehicle is no longer available for the selected dates.']);
        }

        $initialRate = $this->initialRate($car, $validated) ?? 0;
        $carWashFee = (float) ($car->rates->firstWhere('name', 'Car Wash Fee')?->value ?? 0);
        $total = round(($initialRate + $carWashFee) * 1.12, 2);
        $securityDeposit = $this->securityDeposit($total);
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'business_id' => $car->business_id,
            'car_id' => $car->id,
            'rental_type' => $validated['rental_type'],
            'pickup_date' => $validated['pickup_date'],
            'pickup_time' => $validated['pickup_time'],
            'pickup_location' => $validated['pickup_location'] ?? 'Not applicable',
            'destination_itinerary' => $validated['destination_itinerary'],
            'preferred_vehicle' => $car->car_model ?: $car->vehicle_type,
            'passengers_count' => $validated['passengers_count'],
            'return_date' => $validated['return_date'] ?? $validated['pickup_date'],
            'return_time' => $validated['return_time'] ?? $validated['pickup_time'],
            'return_location' => $validated['return_location'] ?? null,
            'handover_option' => $validated['handover_option'] ?? 'not_applicable',
            'handover_other' => $validated['handover_other'] ?? null,
            'initial_rate' => $initialRate,
            'final_rate' => $total,
            'reservation_fee' => $securityDeposit,
            'status' => 'pending_payment',
        ]);

        return redirect()->route('bookings.payment', $booking);
    }

    public function review(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        return view('public.booking-review', ['booking' => $booking->load(['business', 'car', 'car.rates'])]);
    }

    public function payment(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::id() && in_array($booking->status, ['finalized', 'pending_payment'], true), 403);

        $paymentMethods = $booking->business->paymentMethods()->get();

        return view('public.booking-payment', compact('booking', 'paymentMethods'));
    }

    public function storePayment(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id() && in_array($booking->status, ['finalized', 'pending_payment'], true), 403);

        $validated = $request->validate([
            'business_payment_method_id' => ['required', 'integer'],
            'payment_reference_number' => ['required', 'string', 'max:100'],
            'payment_proof' => ['required', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'],
            'special_request' => ['nullable', 'string', 'max:2000'],
            'flight_details' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $paymentProof = $request->file('payment_proof');

        $paymentMethod = $booking->business->paymentMethods()->findOrFail($validated['business_payment_method_id']);

        if (! $paymentProof instanceof UploadedFile || ! $paymentProof->isValid() || ! is_file($paymentProof->getPathname())) {
            return back()
                ->withInput()
                ->withErrors(['payment_proof' => 'Please choose a valid payment screenshot and submit the form again.']);
        }

        $path = $this->storeImage($paymentProof, 'payment-proofs/'.$booking->id);

        $flightDetailsPath = $booking->flight_details_path;
        if ($request->hasFile('flight_details') && $request->file('flight_details')?->isValid()) {
            if ($flightDetailsPath !== null) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $flightDetailsPath));
            }

            $flightDetailsPath = '/storage/'.$this->storeImage($request->file('flight_details'), 'flight-details/'.$booking->id);
        }

        $booking->update([
            'payment_method' => $paymentMethod->payment_method,
            'business_payment_method_id' => $paymentMethod->id,
            'payment_reference_number' => $validated['payment_reference_number'],
            'payment_proof_path' => '/storage/'.$path,
            'payment_submitted_at' => now(),
            'special_request' => $validated['special_request'] ?? null,
            'flight_details_path' => $flightDetailsPath,
            'status' => 'payment_submitted',
        ]);

        return redirect()->route('bookings.review', $booking)->with('status', 'Payment proof submitted for review.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function initialRate(Car $car, array $validated): ?float
    {
        if ($validated['rental_type'] !== 'self_drive') {
            return null;
        }

        $pickupAt = Carbon::parse($validated['pickup_date'].' '.$validated['pickup_time']);
        $returnAt = Carbon::parse(($validated['return_date'] ?? $validated['pickup_date']).' '.($validated['return_time'] ?? $validated['pickup_time']));
        $rentalMinutes = $pickupAt->diffInMinutes($returnAt);
        $rateName = $rentalMinutes <= 720 ? '12hrs' : '24hrs';
        $rentalPeriods = $rentalMinutes <= 720 ? 1 : max(1, (int) ceil($rentalMinutes / 1440));
        $amount = (float) ($car->rates->firstWhere('name', $rateName)?->value ?? 0) * $rentalPeriods;
        $itinerary = strtolower($validated['destination_itinerary']);

        if (str_contains($itinerary, 'sorsogon') || str_contains($itinerary, 'cam sur') || str_contains($itinerary, 'camsur')) {
            $amount += (float) ($car->rates->firstWhere('name', 'Sorsogon/CamSur surcharge')?->value ?? 0);
        }

        return $amount ?: null;
    }

    private function securityDeposit(float $total): float
    {
        return round(($total * 0.2) / 100) * 100;
    }

    private function storeImage(UploadedFile $file, string $directory): string
    {
        $filename = Str::uuid().'.'.$file->extension();
        $path = $directory.'/'.$filename;

        Storage::disk('public')->put($path, file_get_contents($file->getPathname()));

        return $path;
    }
}
