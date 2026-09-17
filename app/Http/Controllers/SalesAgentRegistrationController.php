<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class SalesAgentRegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.sales-agent-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'sales_agent_status' => 'pending',
        ]);
        Role::findOrCreate('sales_agent');
        $user->assignRole('sales_agent');

        return redirect()->route('sales-agent.registration.success')->with('status', 'Your application was submitted for administrator review.');
    }

    public function success(): View
    {
        return view('auth.sales-agent-register-success');
    }
}
