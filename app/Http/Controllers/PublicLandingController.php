<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Car;
use App\Models\PlatformLandingPage;
use Illuminate\Contracts\View\View;

class PublicLandingController extends Controller
{
    public function index(): View
    {
        $landingPage = PlatformLandingPage::query()->firstOrFail();
        $businessSlugs = $landingPage->featured_business_slugs ?? [];
        $carIds = $landingPage->featured_car_ids ?? [];

        $businesses = Business::query()
            ->withCount('cars')
            ->when($businessSlugs !== [], fn ($query) => $query->whereIn('slug', $businessSlugs))
            ->orderByRaw($businessSlugs === [] ? 'name' : 'FIELD(slug, '.implode(',', array_fill(0, count($businessSlugs), '?')).')', $businessSlugs)
            ->limit(4)
            ->get();

        $cars = Car::query()
            ->with(['business', 'images', 'rates'])
            ->where('status', 'available')
            ->when($carIds !== [], fn ($query) => $query->whereIn('id', $carIds))
            ->orderByRaw($carIds === [] ? 'created_at desc' : 'FIELD(id, '.implode(',', array_fill(0, count($carIds), '?')).')', $carIds)
            ->limit(3)
            ->get();

        return view('landing', [
            'content' => $landingPage->content,
            'businesses' => $businesses,
            'cars' => $cars,
        ]);
    }
}
