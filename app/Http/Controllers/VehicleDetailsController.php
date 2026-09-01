<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\View\View;

class VehicleDetailsController extends Controller
{
    public function show(Car $car): View
    {
        $car->load(['business.drivers', 'images', 'rates']);

        abort_unless($car->status === 'available', 404);

        $driverDailyRate = (float) $car->business->drivers
            ->first(fn ($driver) => (bool) $driver->pivot->is_default && (bool) $driver->pivot->is_available)?->pivot->daily_rate;
        $carWashFee = (float) ($car->rates->firstWhere('name', 'Car Wash Fee')?->value ?? 0);
        $dailyDiscount = (float) ($car->rates->firstWhere('name', 'Daily Discount')?->value ?? 0);
        $pickupDropoffFee = (float) ($car->rates->firstWhere('name', 'Pick-up & Drop-off')?->value ?? 0);
        $fuelConsumptionKmPerLiter = (float) ($car->fuel_consumption_km_per_liter ?? 0);
        $fuelPricePerLiter = match ($car->fuel_type) {
            'Diesel Premium' => (float) $car->business->diesel_premium_price_per_liter,
            'Diesel Regular' => (float) $car->business->diesel_regular_price_per_liter,
            'Gasoline Premium' => (float) $car->business->gasoline_premium_price_per_liter,
            'Gasoline Regular' => (float) $car->business->gasoline_regular_price_per_liter,
            default => 0,
        };
        $garageLatitude = (float) ($car->business->garage_latitude ?? 0);
        $garageLongitude = (float) ($car->business->garage_longitude ?? 0);

        return view('public.vehicle-details', compact('car', 'driverDailyRate', 'carWashFee', 'dailyDiscount', 'pickupDropoffFee', 'fuelConsumptionKmPerLiter', 'fuelPricePerLiter', 'garageLatitude', 'garageLongitude'));
    }
}
