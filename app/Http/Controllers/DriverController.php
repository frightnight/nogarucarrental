<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        $business = $this->business();
        $drivers = $business->drivers()->orderBy('full_name')->get();
        $availableExternalDrivers = Driver::query()
            ->whereDoesntHave('businesses', fn ($query) => $query->whereKey($business->id))
            ->whereHas('businesses', fn ($query) => $query->where('business_driver.is_available', true))
            ->orderBy('full_name')
            ->get();

        return view('panels.drivers.index', compact('business', 'drivers', 'availableExternalDrivers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business();
        $validated = $request->validate([
            'license_number' => ['required', 'string', 'max:100'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'daily_rate' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $licenseNumber = strtoupper(trim($validated['license_number']));
        $driver = Driver::find($licenseNumber);

        if ($driver === null) {
            $request->validate(['full_name' => ['required', 'string', 'max:255']]);
            $driver = Driver::create([
                'license_number' => $licenseNumber,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);
            $wasAlreadyRegistered = false;
        } else {
            $belongsToBusiness = $business->drivers()->whereKey($driver->getKey())->exists();
            $isAvailableToOutsource = $driver->businesses()->where('business_driver.is_available', true)->exists();

            if (! $belongsToBusiness && ! $isAvailableToOutsource) {
                return back()->withInput()->withErrors([
                    'license_number' => 'This driver is not currently available for outsourcing.',
                ]);
            }

            $wasAlreadyRegistered = true;
        }

        $business->drivers()->syncWithoutDetaching([
            $driver->license_number => ['daily_rate' => $validated['daily_rate'], 'is_available' => true, 'is_default' => false],
        ]);

        return redirect()->route('business.drivers.index')->with(
            'success',
            $wasAlreadyRegistered ? 'Existing licensed driver added to your business.' : 'Driver added to your business.',
        );
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $business = $this->business();
        abort_unless($business->drivers()->whereKey($driver->getKey())->exists(), 404);
        $validated = $request->validate([
            'daily_rate' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'is_available' => ['required', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'default_driver' => ['nullable', 'string', 'max:100'],
        ]);

        $isDefault = ($request->boolean('is_default') || ($validated['default_driver'] ?? null) === $driver->license_number)
            && (bool) $validated['is_available'];
        if ($isDefault) {
            $business->drivers()->newPivotStatement()
                ->where('business_id', $business->id)
                ->update(['is_default' => false]);
        }

        $business->drivers()->updateExistingPivot($driver->license_number, [
            'daily_rate' => $validated['daily_rate'],
            'is_available' => $validated['is_available'],
            'is_default' => $isDefault,
        ]);

        return redirect()->route('business.drivers.index')->with('success', 'Driver assignment updated.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $business = $this->business();
        $business->drivers()->detach($driver->license_number);

        return redirect()->route('business.drivers.index')->with('success', 'Driver removed from your business.');
    }

    public function show(Driver $driver): View
    {
        $business = $this->business();
        $driver = $business->drivers()->whereKey($driver->getKey())->firstOrFail();

        $bookings = $driver->bookings()
            ->where('business_id', $business->id)
            ->with(['user.clientProfile', 'car'])
            ->orderBy('pickup_date')
            ->get();
        $upcomingTravels = $bookings->filter(fn ($booking): bool => $booking->pickup_date?->isTodayOrFuture() && ! in_array($booking->status, ['completed', 'cancelled', 'rejected'], true));
        $completedTravels = $bookings->filter(fn ($booking): bool => $booking->status === 'completed');

        return view('panels.drivers.show', compact('business', 'driver', 'upcomingTravels', 'completedTravels'));
    }

    private function business(): Business
    {
        $business = Auth::user()->businesses()->first();

        abort_unless($business instanceof Business, 403);

        return $business;
    }
}
