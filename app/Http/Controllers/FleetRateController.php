<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Car;
use App\Models\Rate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FleetRateController extends Controller
{
    public function store(Request $request, Car $car): RedirectResponse
    {
        $this->ensureCarBelongsToBusiness($car);

        $car->rates()->create($this->validatedRate($request));

        return redirect()->route('business.fleet.edit', $car)
            ->with('success', 'Rate added successfully.');
    }

    public function update(Request $request, Car $car, Rate $rate): RedirectResponse
    {
        $this->ensureRateBelongsToCar($car, $rate);

        $rate->update($this->validatedRate($request));

        return redirect()->route('business.fleet.edit', $car)
            ->with('success', 'Rate updated successfully.');
    }

    public function destroy(Car $car, Rate $rate): RedirectResponse
    {
        $this->ensureRateBelongsToCar($car, $rate);

        $rate->delete();

        return redirect()->route('business.fleet.edit', $car)
            ->with('success', 'Rate removed successfully.');
    }

    /**
     * @return array{name: string, value: float}
     */
    private function validatedRate(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0|max:99999999.99',
        ]);
    }

    private function ensureRateBelongsToCar(Car $car, Rate $rate): void
    {
        $this->ensureCarBelongsToBusiness($car);
        abort_unless($rate->car_id === $car->id, 404);
    }

    private function ensureCarBelongsToBusiness(Car $car): void
    {
        $business = Auth::user()->businesses()->first();

        abort_unless($business instanceof Business && $car->business_id === $business->id, 403);
    }
}
