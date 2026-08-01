<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'location' => ['nullable', 'string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'max:255'],
            'transmission' => ['nullable', 'in:Automatic,Manual,CVT'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:60'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'pickup_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:pickup_date'],
        ]);

        $cars = Car::query()
            ->with(['business', 'images', 'rates'])
            ->where('status', 'available')
            ->when($filters['location'] ?? null, function (Builder $query, string $location): void {
                $query->whereHas('business', fn (Builder $businesses) => $businesses->where('city', 'like', '%'.$location.'%'));
            })
            ->when($filters['vehicle_type'] ?? null, fn (Builder $query, string $type) => $query->where('vehicle_type', $type))
            ->when($filters['transmission'] ?? null, fn (Builder $query, string $transmission) => $query->where('transmission', $transmission))
            ->when($filters['seats'] ?? null, fn (Builder $query, int $seats) => $query->where('seats', '>=', $seats))
            ->when($filters['max_price'] ?? null, function (Builder $query, float $maxPrice): void {
                $query->whereHas('rates', fn (Builder $rates) => $rates->where('value', '<=', $maxPrice));
            })
            ->when(($filters['pickup_date'] ?? null) && ($filters['return_date'] ?? null), function (Builder $query) use ($filters): void {
                $query->whereDoesntHave('bookings', function (Builder $bookings) use ($filters): void {
                    $bookings->whereNotIn('status', ['cancelled', 'rejected'])
                        ->whereDate('pickup_date', '<=', $filters['return_date'])
                        ->whereDate('return_date', '>=', $filters['pickup_date']);
                });
            })
            ->latest()
            ->get();

        $vehicleTypes = Car::query()
            ->where('status', 'available')
            ->whereNotNull('vehicle_type')
            ->distinct()
            ->orderBy('vehicle_type')
            ->pluck('vehicle_type');

        return view('public.marketplace', compact('cars', 'filters', 'vehicleTypes'));
    }
}
