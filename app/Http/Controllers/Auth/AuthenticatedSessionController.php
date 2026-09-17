<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $authenticatedUser = Auth::user();
        if ($authenticatedUser?->hasRole('driver')) {
            $driver = Driver::query()->where('user_id', $authenticatedUser->id)->first();
            if ($driver?->approval_status !== 'approved') {
                Auth::logout();

                return back()
                    ->withErrors(['email' => $driver?->approval_status === 'rejected' ? 'Your driver application was not approved.' : 'Your driver application is still under review.'])
                    ->onlyInput('email');
            }
        }

        if ($authenticatedUser?->hasRole('sales_agent') && $authenticatedUser->sales_agent_status !== 'approved') {
            Auth::logout();

            return back()
                ->withErrors(['email' => $authenticatedUser->sales_agent_status === 'rejected' ? 'Your sales-agent application was not approved.' : 'Your sales-agent application is still under review.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->getDashboardRoute($authenticatedUser));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function getDashboardRoute($user): string
    {
        if ($user && ($user->hasRole('administrator') || $user->hasRole('moderator'))) {
            return route('admin.dashboard');
        }

        if ($user && ($user->hasRole('business_owner') || $user->hasRole('booker') || $user->hasRole('agent'))) {
            return route('business.dashboard');
        }

        if ($user && $user->hasRole('client')) {
            return route('client.dashboard');
        }

        if ($user && $user->hasRole('driver')) {
            return route('driver.dashboard');
        }

        if ($user && $user->hasRole('sales_agent')) {
            return route('sales-agent.dashboard');
        }

        return route('dashboard');
    }
}
