<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user && ($user->hasRole('administrator') || $user->hasRole('moderator'))) {
            return redirect()->route('admin.dashboard');
        }

        if ($user && ($user->hasRole('business_owner') || $user->hasRole('booker') || $user->hasRole('agent'))) {
            return redirect()->route('business.dashboard');
        }

        if ($user && $user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }

        return view('dashboard');
    }
}
