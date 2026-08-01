<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Car;
use App\Models\PlatformLandingPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlatformLandingController extends Controller
{
    public function edit(): View
    {
        return view('panels.platform-landing-editor', [
            'landingPage' => PlatformLandingPage::query()->firstOrFail(),
            'businesses' => Business::query()->orderBy('name')->get(['id', 'name', 'slug', 'city']),
            'cars' => Car::query()->with('business:id,name')->orderBy('car_model')->get(['id', 'business_id', 'car_model', 'vehicle_type']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_eyebrow' => ['required', 'string', 'max:120'], 'hero_title' => ['required', 'string', 'max:160'],
            'hero_highlight' => ['nullable', 'string', 'max:120'], 'hero_subtitle' => ['required', 'string', 'max:500'],
            'company_heading' => ['required', 'string', 'max:160'], 'destination_heading' => ['required', 'string', 'max:160'],
            'vehicle_heading' => ['required', 'string', 'max:160'], 'why_heading' => ['required', 'string', 'max:160'],
            'testimonial_quote' => ['required', 'string', 'max:500'], 'testimonial_name' => ['required', 'string', 'max:120'],
            'testimonial_role' => ['required', 'string', 'max:120'], 'pricing_heading' => ['required', 'string', 'max:160'],
            'faq_heading' => ['required', 'string', 'max:160'], 'footer_text' => ['required', 'string', 'max:250'],
            'featured_business_slugs' => ['nullable', 'array', 'max:4'], 'featured_business_slugs.*' => ['string', 'exists:businesses,slug'],
            'featured_car_ids' => ['nullable', 'array', 'max:3'], 'featured_car_ids.*' => ['integer', 'exists:cars,id'],
        ]);

        $landingPage = PlatformLandingPage::query()->firstOrFail();
        $content = $landingPage->content;
        foreach (['hero_eyebrow', 'hero_title', 'hero_highlight', 'hero_subtitle', 'company_heading', 'destination_heading', 'vehicle_heading', 'why_heading', 'pricing_heading', 'faq_heading', 'footer_text'] as $key) {
            $content[$key] = $validated[$key];
        }
        $content['testimonial'] = ['quote' => $validated['testimonial_quote'], 'name' => $validated['testimonial_name'], 'role' => $validated['testimonial_role']];

        $landingPage->update(['content' => $content, 'featured_business_slugs' => $validated['featured_business_slugs'] ?? [], 'featured_car_ids' => $validated['featured_car_ids'] ?? []]);

        return back()->with('status', 'Public landing page updated.');
    }
}
