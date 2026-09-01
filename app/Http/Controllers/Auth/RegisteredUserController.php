<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function createBusiness(): View
    {
        return view('auth.business-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('client');

        // Auto-create a basic profile so they can fill it in later
        ClientProfile::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
        ]);

        Auth::login($user);

        return redirect()->route('client.profile.edit');
    }

    public function storeBusiness(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Role::findOrCreate('business_owner');
            $user->assignRole('business_owner');

            $freePlan = BusinessPlan::query()->where('slug', 'free')->firstOrFail();
            $business = Business::create([
                'name' => $validated['business_name'],
                'slug' => $this->availableBusinessSlug($validated['business_name']),
                'city' => $validated['city'] ?: null,
                'business_plan_id' => $freePlan->id,
            ]);

            $user->businesses()->attach($business, ['business_role' => 'owner']);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('business.dashboard');
    }

    private function availableBusinessSlug(string $businessName): string
    {
        $baseSlug = Str::slug($businessName) ?: 'rental-business';
        $slug = $baseSlug;
        $suffix = 2;

        while (Business::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
