<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Car;
use App\Models\PartnerFleetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerFleetController extends Controller
{
    public function index(Request $request): View
    {
        $business = $this->business($request);
        $partnerBusinesses = Business::query()
            ->whereKeyNot($business->id)
            ->with(['cars' => fn ($query) => $query->where('status', 'available')->orderBy('car_model')])
            ->orderBy('name')
            ->get();
        $receivedRequests = PartnerFleetRequest::query()
            ->where('partner_business_id', $business->id)
            ->where('status', 'pending')
            ->with(['requestingBusiness', 'car'])
            ->latest()
            ->get();
        $approvedRequests = PartnerFleetRequest::query()
            ->where('requesting_business_id', $business->id)
            ->where('status', 'approved')
            ->with(['partnerBusiness', 'car'])
            ->latest('responded_at')
            ->get();
        $partnerFleetUnits = $partnerBusinesses->mapWithKeys(function (Business $partnerBusiness): array {
            return [$partnerBusiness->id => $partnerBusiness->cars->map(function (Car $car): array {
                return [
                    'id' => $car->id,
                    'label' => trim(implode(' ', array_filter([
                        $car->car_model ?: $car->vehicle_type,
                        $car->variant,
                        $car->plate_number ? '· '.$car->plate_number : null,
                    ]))),
                ];
            })->values()->all()];
        })->all();

        return view('panels.partner-fleets.index', compact('partnerBusinesses', 'receivedRequests', 'approvedRequests', 'partnerFleetUnits'));
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business($request);
        $validated = $request->validate([
            'partner_business_id' => ['required', 'integer', 'exists:businesses,id'],
            'car_id' => ['required', 'integer', 'exists:cars,id'],
        ]);

        abort_if($validated['partner_business_id'] === $business->id, 422);

        $car = Car::query()->where('id', $validated['car_id'])
            ->where('business_id', $validated['partner_business_id'])
            ->where('status', 'available')
            ->firstOrFail();

        PartnerFleetRequest::updateOrCreate(
            [
                'requesting_business_id' => $business->id,
                'partner_business_id' => $validated['partner_business_id'],
                'car_id' => $car->id,
            ],
            ['status' => 'pending', 'responded_at' => null],
        );

        return redirect()->route('business.partner-fleets.index')->with('success', 'Partner fleet request sent for approval.');
    }

    public function approve(Request $request, PartnerFleetRequest $partnerFleetRequest): RedirectResponse
    {
        $this->respond($request, $partnerFleetRequest, 'approved');

        return redirect()->route('business.partner-fleets.index')->with('success', 'Partner fleet unit approved.');
    }

    public function reject(Request $request, PartnerFleetRequest $partnerFleetRequest): RedirectResponse
    {
        $this->respond($request, $partnerFleetRequest, 'rejected');

        return redirect()->route('business.partner-fleets.index')->with('success', 'Partner fleet request declined.');
    }

    public function destroy(Request $request, PartnerFleetRequest $partnerFleetRequest): RedirectResponse
    {
        $business = $this->business($request);

        abort_unless($partnerFleetRequest->requesting_business_id === $business->id, 403);

        $partnerFleetRequest->delete();

        return redirect()->route('business.partner-fleets.index')->with('success', 'Partner fleet unit removed.');
    }

    private function business(Request $request): Business
    {
        $business = $request->user()->businesses()->first();

        abort_unless($business instanceof Business, 403);

        return $business;
    }

    private function respond(Request $request, PartnerFleetRequest $partnerFleetRequest, string $status): void
    {
        $business = $this->business($request);

        abort_unless($partnerFleetRequest->partner_business_id === $business->id, 403);
        abort_unless($partnerFleetRequest->status === 'pending', 422);

        $partnerFleetRequest->update(['status' => $status, 'responded_at' => now()]);
    }
}
