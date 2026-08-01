<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        $request->session()->regenerate();

        return redirect()->intended($this->getDashboardRoute(Auth::user()));
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

        return route('dashboard');
    }
}
