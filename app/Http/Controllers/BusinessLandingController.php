<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PlatformLandingPage;
use Illuminate\View\View;

class BusinessLandingController extends Controller
{
    public function index(): View
    {
        $businesses = Business::query()
            ->withCount('cars')
            ->orderBy('created_at', 'desc')
            ->get(['name', 'slug', 'city', 'description']);

        // Backward compatible with the current Blade that expects `tagline`.
        $businesses = $businesses->map(function (Business $business) {
            $business->tagline = $business->description ? 'Trusted rentals in '.($business->city ?? 'your city') : 'Open rentals';

            return $business;
        });

        $content = PlatformLandingPage::query()->first()?->content ?? [];

        return view('public.businesses', compact('businesses', 'content'));
    }

    public function show(string $slug): View
    {
        $businessModel = Business::query()->where('slug', $slug)->first();
        abort_unless($businessModel, 404);

        // UI expects a `cars` array.
        // We map DB cars + their first image into the legacy structure used by the Blade.
        $cars = $businessModel->cars()
            ->with(['images' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }, 'rates'])
            ->get();

        $business = [
            'slug' => $businessModel->slug,
            'name' => $businessModel->name,
            'city' => $businessModel->city,
            'description' => $businessModel->description,
            'tagline' => $businessModel->description ? 'Trusted rentals in '.($businessModel->city ?? 'your city') : 'Open rentals',
            'cars' => $cars->map(function ($car) {
                return [
                    // Blade uses `name` for selection highlighting.
                    'name' => $car->car_model ?: ($car->vehicle_type ?: 'Car'),
                    'car_id' => $car->id,
                    'price' => auth()->user()?->hasRole('client') ? $car->rates->map(fn ($rate) => $rate->name.': ₱'.number_format((float) $rate->value, 2))->implode(' · ') : null,
                    // Blade uses `images` array for image slideshow.
                    'images' => $car->images->pluck('image_path')->all(),
                    'status' => $car->status,
                    'vehicle_type' => $car->vehicle_type,
                    'transmission' => $car->transmission,
                    'seats' => $car->seats,
                    'rental_type' => $car->rental_type,
                    'daily_rate' => $car->rates->whereIn('name', ['24hrs', 'Daily'])->min('value') ?? $car->rates->min('value'),
                    'rate_label' => $car->rates->firstWhere('name', '24hrs') ? '24hrs' : 'day',
                ];
            })->all(),
            'business_id' => $businessModel->id,

            // Dynamic landing content fields
            'hero_title' => $businessModel->hero_title,
            'hero_subtitle' => $businessModel->hero_subtitle,
            'about_title' => $businessModel->about_title,
            'about_content' => $businessModel->about_content,
            'about_features' => $businessModel->about_features ?? [],
            'contact_email' => $businessModel->contact_email,
            'contact_phone' => $businessModel->contact_phone,
            'contact_address' => $businessModel->contact_address,
        ];

        return view('public.business-landing', compact('business'));
    }
}
