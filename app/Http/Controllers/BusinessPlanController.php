<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessPlanController extends Controller
{
    public function index(): View
    {
        return view('panels.business-plan', [
            'business' => $this->business(),
            'plans' => BusinessPlan::query()->with('permissions')->orderBy('price')->get(),
        ]);
    }

    public function adminIndex(): View
    {
        return view('panels.admin-business-plans', [
            'businesses' => Business::query()->with('plan')->orderBy('name')->get(),
            'plans' => BusinessPlan::query()->orderBy('price')->get(),
        ]);
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $validated = $request->validate(['business_plan_id' => ['required', 'exists:business_plans,id']]);
        $business->update($validated);
        $plan = BusinessPlan::findOrFail($validated['business_plan_id']);

        return back()->with('success', "{$business->name} is now on the {$plan->name} plan.");
    }

    private function business(): Business
    {
        $business = auth()->user()->businesses()->first();

        abort_unless($business instanceof Business, 403);

        return $business;
    }
}
