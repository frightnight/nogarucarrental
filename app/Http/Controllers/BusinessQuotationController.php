<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Car;
use App\Models\Quotation;
use App\Models\QuotationFootnote;
use App\Models\SavedLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessQuotationController extends Controller
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $quotations = $business->quotations()->with(['car', 'driver'])->latest()->get();
        $cars = $business->cars()->with('rates')->orderBy('car_model')->get();
        $drivers = $business->drivers()->wherePivot('is_available', true)->orderBy('full_name')->get();
        $savedLocations = $business->savedLocations()->latest()->get();
        $footnotes = $business->quotationFootnotes()->latest()->get();
        $editingQuotation = null;
        if ($request->filled('edit')) {
            $editingQuotation = $business->quotations()->findOrFail($request->integer('edit'));
        }

        return view('panels.quotations.index', compact('business', 'quotations', 'cars', 'drivers', 'savedLocations', 'footnotes', 'editingQuotation'));
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business();
        $quotation = $this->createQuotation($business, $this->validatedQuotation($request));

        return redirect()->route('business.quotations.show', $quotation)->with('success', 'Quotation created.');
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $business = $this->business();
        abort_unless($quotation->business_id === $business->id, 404);
        $validated = $this->validatedQuotation($request);

        if ($request->boolean('save_as_new')) {
            $newQuotation = $this->createQuotation($business, $validated);

            return redirect()->route('business.quotations.show', $newQuotation)->with('success', 'New quotation saved from the edited details.');
        }

        $this->fillQuotation($quotation, $business, $validated);
        $quotation->save();

        return redirect()->route('business.quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    public function storeSavedLocation(Request $request): RedirectResponse
    {
        $business = $this->business();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        SavedLocation::updateOrCreate(
            ['business_id' => $business->id, 'title' => $validated['title']],
            $validated + ['business_id' => $business->id],
        );

        return back()->with('success', 'Location saved for future quotations.');
    }

    public function updateSavedLocation(Request $request, SavedLocation $savedLocation): RedirectResponse
    {
        $this->authorizeSavedLocation($savedLocation);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $savedLocation->update($validated);

        return back()->with('success', 'Saved location updated.');
    }

    public function destroySavedLocation(SavedLocation $savedLocation): RedirectResponse
    {
        $this->authorizeSavedLocation($savedLocation);
        $savedLocation->delete();

        return back()->with('success', 'Saved location removed.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);
        $quotation->delete();

        return redirect()->route('business.quotations.index')->with('success', 'Quotation removed.');
    }

    public function storeFootnote(Request $request): RedirectResponse
    {
        $business = $this->business();
        $validated = $this->validatedFootnote($request);
        QuotationFootnote::create([
            'business_id' => $business->id,
            'title' => $validated['title'],
            'content' => $this->sanitizeFootnote($validated['content']),
        ]);

        return back()->with('success', 'Reusable footnote added.');
    }

    public function updateFootnote(Request $request, QuotationFootnote $footnote): RedirectResponse
    {
        $this->authorizeFootnote($footnote);
        $validated = $this->validatedFootnote($request);
        $footnote->update(['title' => $validated['title'], 'content' => $this->sanitizeFootnote($validated['content'])]);

        return back()->with('success', 'Reusable footnote updated.');
    }

    public function destroyFootnote(QuotationFootnote $footnote): RedirectResponse
    {
        $this->authorizeFootnote($footnote);
        $footnote->delete();

        return back()->with('success', 'Reusable footnote removed.');
    }

    /** @return array<string, mixed> */
    private function validatedQuotation(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'car_id' => ['required', 'integer'],
            'driver_license_number' => ['nullable', 'string', 'max:100'],
            'package_type' => ['required', 'in:all_in,all_out'],
            'itinerary' => ['required', 'array', 'min:1', 'max:20'],
            'itinerary.*.title' => ['nullable', 'string', 'max:100'],
            'itinerary.*.address' => ['required', 'string', 'max:255'],
            'itinerary.*.latitude' => ['required', 'numeric', 'between:-90,90'],
            'itinerary.*.longitude' => ['required', 'numeric', 'between:-180,180'],
            'itinerary_start_address' => ['required', 'string', 'max:255'],
            'itinerary_start_latitude' => ['required', 'numeric', 'between:-90,90'],
            'itinerary_start_longitude' => ['required', 'numeric', 'between:-180,180'],
            'itinerary_end_address' => ['required', 'string', 'max:255'],
            'itinerary_end_latitude' => ['required', 'numeric', 'between:-90,90'],
            'itinerary_end_longitude' => ['required', 'numeric', 'between:-180,180'],
            'other_payments' => ['nullable', 'array', 'max:50'],
            'other_payments.*.name' => ['required', 'string', 'max:100'],
            'other_payments.*.amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'quotation_footnote_id' => ['nullable', 'integer'],
            'footnote_content' => ['nullable', 'string', 'max:30000'],
            'total_distance_km' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);
    }

    /** @return array{title: string, content: string} */
    private function validatedFootnote(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:30000'],
        ]);
    }

    /** @param array<string, mixed> $validated */
    private function createQuotation(Business $business, array $validated): Quotation
    {
        $quotation = new Quotation(['business_id' => $business->id, 'quotation_number' => 'QT-'.now()->format('Ymd').'-'.strtoupper(Str::random(5))]);
        $this->fillQuotation($quotation, $business, $validated);
        $quotation->save();

        return $quotation;
    }

    /** @param array<string, mixed> $validated */
    private function fillQuotation(Quotation $quotation, Business $business, array $validated): void
    {
        $car = $business->cars()->with('rates')->findOrFail($validated['car_id']);
        $driver = null;
        if ($validated['driver_license_number'] ?? null) {
            $driver = $business->drivers()->wherePivot('is_available', true)->whereKey($validated['driver_license_number'])->firstOrFail();
        }

        $vehicleRate = (float) ($car->rates->firstWhere('name', '24hrs')?->value ?? $car->rates->firstWhere('name', 'Daily')?->value ?? 0);
        $driverRate = (float) ($driver?->pivot->daily_rate ?? 0);
        $rawDistanceRate = $validated['package_type'] === 'all_in' ? $this->fuelCost($car, (float) $validated['total_distance_km']) : 0;
        $distanceRate = $rawDistanceRate > 0 ? ceil($rawDistanceRate / 100) * 100 : 0;
        $otherPayments = collect($validated['other_payments'] ?? [])->map(fn (array $payment): array => [
            'name' => $payment['name'],
            'amount' => round((float) $payment['amount'], 2),
        ])->values()->all();
        $otherPaymentsTotal = collect($otherPayments)->sum('amount');
        $footnote = null;
        if ($validated['quotation_footnote_id'] ?? null) {
            $footnote = $business->quotationFootnotes()->findOrFail($validated['quotation_footnote_id']);
        }

        $quotation->fill([
            'car_id' => $car->id,
            'driver_license_number' => $driver?->license_number,
            'title' => $validated['title'],
            'client_name' => $validated['client_name'] ?? null,
            'package_type' => $validated['package_type'],
            'itinerary' => $validated['itinerary'],
            'other_payments' => $otherPayments,
            'itinerary_start_address' => $validated['itinerary_start_address'],
            'itinerary_start_latitude' => $validated['itinerary_start_latitude'],
            'itinerary_start_longitude' => $validated['itinerary_start_longitude'],
            'itinerary_end_address' => $validated['itinerary_end_address'],
            'itinerary_end_latitude' => $validated['itinerary_end_latitude'],
            'itinerary_end_longitude' => $validated['itinerary_end_longitude'],
            'quotation_footnote_id' => $footnote?->id,
            'footnote_content' => $this->sanitizeFootnote($validated['footnote_content'] ?? $footnote?->content ?? ''),
            'total_distance_km' => $validated['total_distance_km'],
            'vehicle_rate' => $vehicleRate,
            'driver_rate' => $driverRate,
            'distance_rate' => $distanceRate,
            'total_amount' => $vehicleRate + $driverRate + $distanceRate + $otherPaymentsTotal,
        ]);
    }

    public function show(Quotation $quotation): View
    {
        $this->authorizeQuotation($quotation);
        $quotation->load(['business', 'car', 'driver']);

        return view('panels.quotations.show', compact('quotation'));
    }

    private function business(): Business
    {
        $business = Auth::user()?->businesses()->first();

        abort_unless($business instanceof Business, 403, 'You are not associated with a business.');

        return $business;
    }

    private function authorizeQuotation(Quotation $quotation): void
    {
        abort_unless($quotation->business_id === $this->business()->id, 404);
    }

    private function authorizeSavedLocation(SavedLocation $savedLocation): void
    {
        abort_unless($savedLocation->business_id === $this->business()->id, 404);
    }

    private function authorizeFootnote(QuotationFootnote $footnote): void
    {
        abort_unless($footnote->business_id === $this->business()->id, 404);
    }

    private function sanitizeFootnote(string $content): string
    {
        $content = strip_tags($content, '<p><br><strong><em><u><s><ol><ul><li><h1><h2><h3><blockquote><a><span>');
        $content = preg_replace_callback('/\s(?:href|src)\s*=\s*(["\'])(.*?)\1/i', function (array $match): string {
            $url = trim($match[2]);
            $isSafeUrl = preg_match('/^(?:https?:|mailto:|tel:|#)/i', $url) === 1;

            return $isSafeUrl ? ' href='.$match[1].e($url).$match[1] : '';
        }, $content) ?? '';

        return preg_replace('/\son\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content) ?? '';
    }

    private function fuelCost(Car $car, float $distanceKm): float
    {
        $fuelPricePerLiter = match ($car->fuel_type) {
            'Diesel Premium' => (float) $car->business->diesel_premium_price_per_liter,
            'Diesel Regular' => (float) $car->business->diesel_regular_price_per_liter,
            'Gasoline Premium' => (float) $car->business->gasoline_premium_price_per_liter,
            'Gasoline Regular' => (float) $car->business->gasoline_regular_price_per_liter,
            default => 0,
        };
        $consumption = (float) ($car->fuel_consumption_km_per_liter ?? 0);

        return $consumption > 0 ? ($distanceKm / $consumption) * $fuelPricePerLiter : 0;
    }
}
