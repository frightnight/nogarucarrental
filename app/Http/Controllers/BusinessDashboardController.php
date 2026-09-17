<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $business = auth()->user()?->businesses()->first();

        if ($business !== null && $business->profile_completed_at === null) {
            return redirect()->route('business.profile.create');
        }

        return app(RentalManagementController::class)->dashboard();
    }
}
