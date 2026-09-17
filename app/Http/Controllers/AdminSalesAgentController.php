<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\SalesCommission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminSalesAgentController extends Controller
{
    public function index(): View
    {
        $agents = User::role('sales_agent')->with(['businesses', 'salesCommissions'])->orderBy('name')->get();
        $businesses = Business::query()->orderBy('name')->get();

        return view('panels.admin-sales-agents', compact('agents', 'businesses'));
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole('sales_agent'), 404);
        $validated = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'commission_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $user->update(['sales_agent_status' => 'approved']);
        $user->businesses()->syncWithoutDetaching([$validated['business_id'] => [
            'business_role' => 'sales_agent',
            'commission_rate_percent' => $validated['commission_rate_percent'],
            'is_active' => true,
        ]]);

        return back()->with('success', 'Sales agent approved and assigned.');
    }

    public function reject(User $user): RedirectResponse
    {
        abort_unless($user->hasRole('sales_agent'), 404);
        $user->update(['sales_agent_status' => 'rejected']);

        return back()->with('success', 'Sales agent application rejected.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'commission_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
                'sales_agent_status' => 'approved',
            ]);
            Role::findOrCreate('sales_agent');
            $user->assignRole('sales_agent');
            $user->businesses()->attach($validated['business_id'], [
                'business_role' => 'sales_agent',
                'commission_rate_percent' => $validated['commission_rate_percent'],
                'is_active' => true,
            ]);
        });

        return back()->with('success', 'Sales agent account created.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole('sales_agent'), 404);
        $membership = BusinessUser::query()->where('user_id', $user->id)->firstOrFail();
        $membership->update(['is_active' => ! $membership->is_active]);

        return back()->with('success', $membership->is_active ? 'Sales agent activated.' : 'Sales agent deactivated.');
    }

    public function approveCommission(Request $request, SalesCommission $commission): RedirectResponse
    {
        $commission->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => $request->user()->id]);

        return back()->with('success', 'Commission approved.');
    }
}
