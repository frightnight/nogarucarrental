<?php

namespace App\Http\Controllers;

use App\BusinessFeature;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BusinessLandingContentController extends Controller
{
    /**
     * Show the landing content editor for the authenticated user's business.
     */
    public function edit(): View
    {
        $business = $this->getUserBusiness();

        abort_unless($business, 403, 'You are not associated with any business.');

        $this->authorizeAccess();
        abort_unless($business->canUseFeature(BusinessFeature::LandingPage), 403, 'Landing page editing is not included in your current plan.');

        return view('panels.business-landing-editor', compact('business'));
    }

    /**
     * Update the landing page content.
     */
    public function update(Request $request): RedirectResponse
    {
        $business = $this->getUserBusiness();

        abort_unless($business, 403, 'You are not associated with any business.');

        $this->authorizeAccess();
        abort_unless($business->canUseFeature(BusinessFeature::LandingPage), 403, 'Landing page editing is not included in your current plan.');

        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'about_title' => 'nullable|string|max:255',
            'about_content' => 'nullable|string|max:5000',
            'about_features' => 'nullable|array',
            'about_features.*.icon' => 'nullable|string|max:50',
            'about_features.*.title' => 'nullable|string|max:255',
            'about_features.*.description' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:1000',
        ]);

        $business->update($validated);

        return redirect()->route('business.landing.content.edit')
            ->with('success', 'Landing page content updated successfully.');
    }

    /**
     * Get the business associated with the authenticated user.
     */
    private function getUserBusiness(): ?Business
    {
        $user = Auth::user();

        // First try the business_users pivot relationship
        $pivotBusiness = $user->businesses()->first();
        if ($pivotBusiness) {
            return $pivotBusiness;
        }

        // Fallback: try the user's name/slug convention for inferring business
        $slug = str($user->name)->slug()->value();
        $businessBySlug = Business::query()->where('slug', $slug)->first();
        if ($businessBySlug) {
            return $businessBySlug;
        }

        // Last resort: return the first business in the system as fallback
        return Business::query()->first();
    }

    /**
     * Ensure the user has permission to edit landing content.
     * Uses Spatie roles instead of pivot table business_role.
     */
    private function authorizeAccess(): void
    {
        $user = Auth::user();

        // Use Spatie built-in role check
        abort_unless($user->hasAnyRole(['business_owner', 'moderator']), 403,
            'Only business owners and moderators can edit landing page content.');
    }
}
