<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Car;
use App\Models\Quotation;
use App\Models\SalesCommission;
use App\Models\SalesLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SalesAgentController extends Controller
{
    public function index(Request $request): View
    {
        $business = $this->business()->load(['bookings.car', 'cars.rates']);
        $leads = $business->salesLeads()->where('sales_agent_id', Auth::id())->with(['quotation', 'convertedBooking'])->latest()->get();
        $commissions = Auth::user()->salesCommissions()->where('business_id', $business->id)->latest()->get();
        $performance = [
            'new_leads' => $leads->where('status', 'new')->count(),
            'quotations' => $leads->whereNotNull('quotation_id')->count(),
            'bookings' => $leads->whereNotNull('converted_booking_id')->count(),
            'estimated_sales' => $leads->whereIn('status', ['quoted', 'negotiating', 'confirmed', 'booked'])->sum('estimated_value'),
            'pending_commission' => $commissions->where('status', 'pending')->sum('amount'),
            'approved_commission' => $commissions->where('status', 'approved')->sum('amount'),
            'paid_commission' => $commissions->where('status', 'paid')->sum('amount'),
        ];

        return view('panels.sales-agent', compact('business', 'leads', 'commissions', 'performance'));
    }

    public function storeLead(Request $request): RedirectResponse
    {
        $business = $this->business();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'rental_start_date' => ['nullable', 'date'],
            'rental_end_date' => ['nullable', 'date', 'after_or_equal:rental_start_date'],
            'preferred_vehicle' => ['nullable', 'string', 'max:255'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);
        $business->salesLeads()->create($validated + ['sales_agent_id' => Auth::id(), 'status' => 'new']);

        return back()->with('success', 'Lead added.');
    }

    public function updateLead(Request $request, SalesLead $lead): RedirectResponse
    {
        $this->authorizeLead($lead);
        $validated = $request->validate([
            'status' => ['required', 'in:new,contacted,quoted,negotiating,follow_up,confirmed,lost,cancelled,booked'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);
        $lead->update($validated);

        return back()->with('success', 'Lead updated.');
    }

    public function createQuotation(Request $request, SalesLead $lead): RedirectResponse
    {
        $this->authorizeLead($lead);
        $business = $this->business();
        $validated = $request->validate([
            'car_id' => ['required', 'integer'],
            'vehicle_rate_name' => ['required', 'string', 'max:100'],
            'rental_start_date' => ['required', 'date'],
            'rental_end_date' => ['required', 'date', 'after_or_equal:rental_start_date'],
            'add_on_name' => ['nullable', 'string', 'max:100'],
            'add_on_amount' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);
        $car = $business->cars()->with('rates')->findOrFail($validated['car_id']);
        abort_unless($this->isCarAvailable($car, $validated['rental_start_date'], $validated['rental_end_date']), 422, 'The selected vehicle is not available for those dates.');
        $rate = $car->rates->firstWhere('name', $validated['vehicle_rate_name']);
        abort_unless($rate, 422, 'The selected vehicle rate is not available.');
        $days = max(1, now()->parse($validated['rental_start_date'])->diffInDays(now()->parse($validated['rental_end_date'])) + 1);
        $vehicleAmount = (float) $rate->value * $days;
        $addOnAmount = (float) ($validated['add_on_amount'] ?? 0);
        $discount = (float) ($validated['discount'] ?? 0);
        $total = max(0, $vehicleAmount + $addOnAmount - $discount);
        $quotation = Quotation::create([
            'business_id' => $business->id,
            'car_id' => $car->id,
            'quotation_number' => 'QT-'.now()->format('Ymd').'-'.strtoupper(Str::random(5)),
            'quotation_date' => today(),
            'title' => 'Rental quotation for '.$lead->name,
            'client_name' => $lead->name,
            'package_type' => 'all_out',
            'itinerary' => [['title' => 'Rental', 'address' => $lead->notes ?: 'To be confirmed', 'latitude' => 0, 'longitude' => 0]],
            'other_payments' => $addOnAmount > 0 ? [['name' => $validated['add_on_name'] ?? 'Add-on', 'amount' => $addOnAmount]] : [],
            'hidden_charges' => $discount,
            'total_distance_km' => 0,
            'vehicle_rate_name' => $rate->name,
            'vehicle_rate' => $vehicleAmount,
            'driver_rate' => 0,
            'distance_rate' => 0,
            'total_amount' => $total,
        ]);
        $lead->update(['quotation_id' => $quotation->id, 'rental_start_date' => $validated['rental_start_date'], 'rental_end_date' => $validated['rental_end_date'], 'preferred_vehicle' => $car->car_model ?: $car->vehicle_type, 'estimated_value' => $total, 'status' => 'quoted']);

        return back()->with('success', 'Quotation created for '.$lead->name.'.');
    }

    public function createReservation(Request $request, SalesLead $lead): RedirectResponse
    {
        $this->authorizeLead($lead);
        $business = $this->business();
        $validated = $request->validate(['car_id' => ['required', 'integer'], 'final_rate' => ['required', 'numeric', 'min:0']]);
        abort_unless($lead->rental_start_date && $lead->rental_end_date, 422, 'Add rental dates to the lead first.');
        $car = $business->cars()->findOrFail($validated['car_id']);
        abort_unless($this->isCarAvailable($car, $lead->rental_start_date->toDateString(), $lead->rental_end_date->toDateString()), 422, 'The selected vehicle is no longer available.');
        $booking = $business->bookings()->create([
            'guest_first_name' => Str::before($lead->name, ' '), 'guest_last_name' => Str::after($lead->name, ' '), 'guest_email' => $lead->email, 'guest_phone' => $lead->phone,
            'car_id' => $car->id, 'rental_type' => 'self_drive', 'pickup_date' => $lead->rental_start_date, 'return_date' => $lead->rental_end_date,
            'pickup_time' => '09:00', 'return_time' => '17:00', 'pickup_location' => 'To be confirmed', 'return_location' => 'To be confirmed',
            'preferred_vehicle' => $car->car_model ?: $car->vehicle_type, 'passengers_count' => 1, 'handover_option' => 'manual', 'destination_itinerary' => $lead->notes ?: 'To be confirmed',
            'initial_rate' => $validated['final_rate'], 'final_rate' => $validated['final_rate'], 'status' => 'reserved',
        ]);
        $lead->update(['status' => 'confirmed', 'converted_booking_id' => $booking->id, 'converted_at' => now()]);
        $this->recordCommission($lead, $booking);

        return back()->with('success', 'Reservation created and lead converted to booking.');
    }

    public function convertLead(Request $request, SalesLead $lead): RedirectResponse
    {
        $this->authorizeLead($lead);
        $validated = $request->validate(['booking_id' => ['required', 'integer', 'exists:bookings,id']]);
        $booking = Booking::query()->where('business_id', $lead->business_id)->findOrFail($validated['booking_id']);

        $this->recordCommission($lead, $booking);
        $lead->update(['status' => 'booked', 'converted_booking_id' => $booking->id, 'converted_at' => now()]);

        return back()->with('success', 'Lead converted and commission recorded.');
    }

    private function recordCommission(SalesLead $lead, Booking $booking): void
    {
        $membership = Auth::user()->businesses()->whereKey($lead->business_id)->firstOrFail()->pivot;
        $rate = (float) $membership->commission_rate_percent;
        SalesCommission::updateOrCreate(
            ['sales_agent_id' => Auth::id(), 'booking_id' => $booking->id],
            [
                'business_id' => $lead->business_id,
                'rate_percent' => $rate,
                'amount' => round((float) ($booking->final_rate ?? $booking->initial_rate ?? 0) * $rate / 100, 2),
                'status' => 'pending',
            ],
        );
    }

    private function isCarAvailable(Car $car, string $startDate, string $endDate): bool
    {
        return ! $car->bookings()->whereIn('status', ['reserved', 'confirmed', 'ongoing', 'finalized', 'payment_submitted'])->whereDate('pickup_date', '<=', $endDate)->whereDate('return_date', '>=', $startDate)->exists();
    }

    private function authorizeLead(SalesLead $lead): void
    {
        abort_unless($lead->business_id === $this->business()->id && $lead->sales_agent_id === Auth::id(), 403);
    }

    private function business(): Business
    {
        $business = Auth::user()->businesses()->wherePivot('business_role', 'sales_agent')->wherePivot('is_active', true)->first();
        abort_unless($business instanceof Business, 403);

        return $business;
    }
}
