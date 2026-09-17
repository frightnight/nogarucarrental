<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DriverRegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.driver-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:18 years ago'],
            'gender' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'license_number' => ['required', 'string', 'max:100', 'unique:drivers,license_number'],
            'license_type' => ['required', 'string', 'max:100'],
            'license_issued_at' => ['nullable', 'date'],
            'license_expires_at' => ['required', 'date', 'after:today'],
            'license_restrictions' => ['nullable', 'string', 'max:255'],
            'license_front' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'license_back' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'employment_type' => ['required', 'in:full_time,part_time,on_call,contract,freelance'],
            'years_driving_experience' => ['required', 'integer', 'min:0', 'max:80'],
            'professional_driving_experience' => ['required', 'integer', 'min:0', 'max:80'],
            'vehicle_experience' => ['nullable', 'array'],
            'service_experience' => ['nullable', 'array'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_relationship' => ['required', 'string', 'max:100'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
            'emergency_contact_address' => ['required', 'string', 'max:500'],
        ]);

        $licenseFront = $request->file('license_front');
        $licenseBack = $request->file('license_back');
        if (! $licenseFront instanceof UploadedFile || ! $licenseFront->isValid()) {
            return back()->withInput()->withErrors(['license_front' => 'Please upload a valid license front image.']);
        }
        if (! $licenseBack instanceof UploadedFile || ! $licenseBack->isValid()) {
            return back()->withInput()->withErrors(['license_back' => 'Please upload a valid license back image.']);
        }

        $licenseFrontPath = $this->storeDriverDocument($licenseFront);
        $licenseBackPath = $this->storeDriverDocument($licenseBack);
        if ($licenseFrontPath === null || $licenseBackPath === null) {
            return back()->withInput()->withErrors(['license_front' => 'The license images could not be saved. Please try again with smaller JPG or PNG files.']);
        }

        $driver = DB::transaction(function () use ($validated, $licenseFrontPath, $licenseBackPath): Driver {
            $user = User::create([
                'name' => trim($validated['first_name'].' '.$validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);
            Role::findOrCreate('driver');
            $user->assignRole('driver');

            return Driver::create([
                'driver_code' => 'DRV-'.strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'license_number' => strtoupper(trim($validated['license_number'])),
                'full_name' => trim(implode(' ', array_filter([$validated['first_name'], $validated['middle_name'] ?? null, $validated['last_name'], $validated['suffix'] ?? null]))),
                'middle_name' => $validated['middle_name'] ?? null,
                'suffix' => $validated['suffix'] ?? null,
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'license_type' => $validated['license_type'],
                'license_issued_at' => $validated['license_issued_at'] ?? null,
                'license_expires_at' => $validated['license_expires_at'],
                'license_restrictions' => $validated['license_restrictions'] ?? null,
                'license_front_path' => $licenseFrontPath,
                'license_back_path' => $licenseBackPath,
                'employment_type' => $validated['employment_type'],
                'employment_status' => 'applicant',
                'years_driving_experience' => $validated['years_driving_experience'],
                'professional_driving_experience' => $validated['professional_driving_experience'],
                'vehicle_experience' => $validated['vehicle_experience'] ?? [],
                'service_experience' => $validated['service_experience'] ?? [],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_relationship' => $validated['emergency_contact_relationship'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
                'emergency_contact_address' => $validated['emergency_contact_address'],
                'approval_status' => 'pending',
            ]);
        });

        return redirect()->route('driver.registration.success', $driver)->with('status', 'Your application was submitted for administrator review.');
    }

    public function success(Driver $driver): View
    {
        return view('auth.driver-register-success', compact('driver'));
    }

    private function storeDriverDocument(UploadedFile $file): ?string
    {
        try {
            $extension = $file->extension() ?: 'bin';
            $path = 'driver-documents/'.Str::uuid().'.'.$extension;
            $contents = $file->getContent();

            return Storage::disk('public')->put($path, $contents) ? $path : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
