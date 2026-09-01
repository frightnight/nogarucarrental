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
    /** @var array<int, string> */
    public const VEHICLE_RATE_NAMES = [
        '12hrs',
        '24hrs',
        'Extension per hour',
        'Pick-up & Drop-off',
        'Car Wash Fee',
    ];

    public function sync(Request $request, Car $car): RedirectResponse
    {
        $this->ensureCarBelongsToBusiness($car);
        $validated = $request->validate([
            'rates' => ['required', 'array'],
            'rates.*' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        foreach (self::VEHICLE_RATE_NAMES as $rateName) {
            $car->rates()->updateOrCreate(
                ['name' => $rateName],
                ['value' => $validated['rates'][$rateName] ?? 0],
            );
        }

        $car->rates()->whereNotIn('name', self::VEHICLE_RATE_NAMES)->delete();

        return redirect()->route('business.fleet.edit', $car)->with('success', 'Vehicle rate schedule updated.');
    }

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
            'name' => ['required', 'string', 'in:'.implode(',', self::VEHICLE_RATE_NAMES)],
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
