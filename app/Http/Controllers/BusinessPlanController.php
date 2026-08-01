<?php

namespace App\Http\Controllers;

use App\BusinessPlan;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessPlanController extends Controller
{
    public function index(): View
    {
        return view('panels.business-plan', [
            'business' => $this->business(),
            'plans' => BusinessPlan::cases(),
        ]);
    }

    public function adminIndex(): View
    {
        return view('panels.admin-business-plans', [
            'businesses' => Business::query()->orderBy('name')->get(),
            'plans' => BusinessPlan::cases(),
        ]);
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $validated = $request->validate(['plan' => ['required', 'in:free,basic,pro,business']]);
        $business->update($validated);

        return back()->with('success', "{$business->name} is now on the ".BusinessPlan::from($validated['plan'])->label().' plan.');
    }

    private function business(): Business
    {
        $business = auth()->user()->businesses()->first();

        abort_unless($business instanceof Business, 403);

        return $business;
    }
}
