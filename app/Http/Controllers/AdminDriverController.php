<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDriverController extends Controller
{
    public function index(): View
    {
        $drivers = Driver::query()->with('user')->orderByRaw("CASE approval_status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")->orderBy('created_at', 'desc')->get();

        return view('panels.admin-drivers', compact('drivers'));
    }

    public function approve(Driver $driver): RedirectResponse
    {
        $driver->update(['approval_status' => 'approved', 'employment_status' => 'active', 'reviewed_at' => now(), 'reviewed_by' => Auth::id(), 'rejection_reason' => null]);

        return back()->with('success', 'Driver approved and made available to businesses.');
    }

    public function reject(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);
        $driver->update(['approval_status' => 'rejected', 'employment_status' => 'inactive', 'reviewed_at' => now(), 'reviewed_by' => Auth::id(), 'rejection_reason' => $validated['rejection_reason']]);

        return back()->with('success', 'Driver application rejected.');
    }
}
