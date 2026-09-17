<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $business = $this->currentBusiness();

        if ($business === null) {
            return redirect()->route('business.register');
        }

        if ($business->profile_completed_at !== null) {
            return redirect()->route('business.dashboard');
        }

        return view('auth.business-profile', compact('business'));
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->currentBusiness();

        if ($business === null) {
            return redirect()->route('business.register');
        }

        $validated = $request->validate([
            'business_type' => ['required', 'string', 'in:single_proprietorship,corporation,partnership,cooperative,franchise,other'],
            'business_category' => ['required', 'string', 'in:car_rental,van_rental,travel_agency,transport_service,corporate_fleet,other'],
            'business_address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'permit_issuer' => ['nullable', 'string', 'max:255'],
            'permit_images' => ['required', 'array', 'min:1', 'max:5'],
            'permit_images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $businessPermitPaths = $this->storePermitImages($request);

        $business->fill([
            'business_type' => $validated['business_type'],
            'business_category' => $validated['business_category'],
            'business_address' => $validated['business_address'],
            'contact_number' => $validated['contact_number'],
            'registration_number' => $validated['registration_number'] ?? null,
            'tin' => $validated['tin'] ?? null,
            'permit_issuer' => $validated['permit_issuer'] ?? null,
            'permit_images' => $businessPermitPaths,
            'profile_completed_at' => now(),
        ]);
        $business->save();

        return redirect()->route('business.dashboard')->with('success', 'Business profile completed. You can now continue using the dashboard.');
    }

    public function edit(): View|RedirectResponse
    {
        $business = $this->currentBusiness();

        if ($business === null) {
            return redirect()->route('business.register');
        }

        if ($business->profile_completed_at === null) {
            return redirect()->route('business.profile.create');
        }

        return view('panels.business-profile', compact('business'));
    }

    public function update(Request $request): RedirectResponse
    {
        $business = $this->currentBusiness();

        if ($business === null) {
            return redirect()->route('business.register');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:single_proprietorship,corporation,partnership,cooperative,franchise,other'],
            'business_category' => ['required', 'string', 'in:car_rental,van_rental,travel_agency,transport_service,corporate_fleet,other'],
            'business_address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'permit_issuer' => ['nullable', 'string', 'max:255'],
            'permit_images' => ['nullable', 'array', 'max:5'],
            'permit_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $uploadedImages = $request->file('permit_images', []);
        $uploadedImageCount = is_array($uploadedImages) ? count($uploadedImages) : 1;

        if (count($business->permit_images ?? []) + $uploadedImageCount > 5) {
            throw ValidationException::withMessages([
                'permit_images' => 'A business profile can have at most 5 permit images.',
            ]);
        }

        $newPermitPaths = $this->storePermitImages($request);
        $permitImages = array_values(array_merge($business->permit_images ?? [], $newPermitPaths));

        $business->update([
            'name' => $validated['name'],
            'city' => $validated['city'],
            'business_type' => $validated['business_type'],
            'business_category' => $validated['business_category'],
            'business_address' => $validated['business_address'],
            'contact_number' => $validated['contact_number'],
            'registration_number' => $validated['registration_number'] ?? null,
            'tin' => $validated['tin'] ?? null,
            'permit_issuer' => $validated['permit_issuer'] ?? null,
            'permit_images' => $permitImages,
        ]);

        return redirect()->route('business.profile.edit')->with('success', 'Business profile updated successfully.');
    }

    public function destroyPermit(Request $request): RedirectResponse
    {
        $business = $this->currentBusiness();

        if ($business === null) {
            return redirect()->route('business.register');
        }

        $validated = $request->validate([
            'permit_path' => ['required', 'string'],
        ]);
        $permitImages = $business->permit_images ?? [];

        if (in_array($validated['permit_path'], $permitImages, true)) {
            Storage::disk('public')->delete($validated['permit_path']);
            $business->update([
                'permit_images' => array_values(array_filter(
                    $permitImages,
                    fn (string $permitPath): bool => $permitPath !== $validated['permit_path'],
                )),
            ]);
        }

        return redirect()->route('business.profile.edit')->with('success', 'Permit image removed.');
    }

    private function currentBusiness(): ?Business
    {
        return auth()->user()?->businesses()->first();
    }

    /**
     * @return array<int, string>
     */
    private function storePermitImages(Request $request): array
    {
        $paths = [];
        $files = $request->file('permit_images', []);

        if (! is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $image) {
            if (! $image instanceof UploadedFile || ! $image->isValid()) {
                continue;
            }

            $extension = $image->getClientOriginalExtension();
            $filename = ($extension !== '')
                ? sprintf('%s.%s',
                    bin2hex(random_bytes(8)),
                    $extension,
                )
                : bin2hex(random_bytes(12));

            try {
                $image->move(storage_path('app/public/business-permits'), $filename);
            } catch (\Throwable) {
                throw ValidationException::withMessages([
                    'permit_images' => 'The uploaded permit image could not be stored. Please try again.',
                ]);
            }

            $paths[] = 'business-permits/'.$filename;
        }

        return $paths;
    }
}
