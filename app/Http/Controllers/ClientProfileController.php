<?php

namespace App\Http\Controllers;

use App\Models\ClientIdentityDocument;
use App\Models\ClientProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClientProfileController extends Controller
{
    /**
     * Show the client profile form (personal info).
     */
    public function edit(): View
    {
        $profile = ClientProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['full_name' => Auth::user()->name]
        );

        return view('panels.client.profile', compact('profile'));
    }

    /**
     * Update the client profile personal information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'permanent_address' => ['required', 'string', 'max:1000'],
            'mobile_numbers' => ['nullable', 'array'],
            'mobile_numbers.*.telecom' => ['required_with:mobile_numbers', 'string', 'max:50'],
            'mobile_numbers.*.number' => ['required_with:mobile_numbers', 'string', 'max:20'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'viber_number' => ['nullable', 'string', 'max:20'],
        ]);

        $profile = ClientProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return redirect()->route('client.profile.edit')->with('status', 'Profile updated successfully.');
    }

    /**
     * Show the self-drive document upload form.
     */
    public function documents(): View
    {
        $profile = ClientProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['full_name' => Auth::user()->name]
        );

        $documents = $profile->identityDocuments ?: new ClientIdentityDocument;

        return view('panels.client.documents', compact('profile', 'documents'));
    }

    /**
     * Upload self-drive identity documents.
     */
    public function uploadDocuments(Request $request): RedirectResponse
    {
        $profile = ClientProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['full_name' => Auth::user()->name]
        );

        if (! $profile->id) {
            return redirect()->route('client.profile.documents')
                ->withErrors(['profile' => 'Profile not found. Please complete your personal information first.']);
        }

        $validated = $request->validate([
            'valid_id_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'valid_id_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'selfie_with_ids' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'ltms_welcome' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'ltms_client_id' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'ltms_license_front' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'ltms_license_back' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'proof_of_billing' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $docData = [];
        $documentFields = [
            'valid_id_1' => 'valid_id_1_path',
            'valid_id_2' => 'valid_id_2_path',
            'selfie_with_ids' => 'selfie_with_ids_path',
            'ltms_welcome' => 'ltms_welcome_path',
            'ltms_client_id' => 'ltms_client_id_path',
            'ltms_license_front' => 'ltms_license_front_path',
            'ltms_license_back' => 'ltms_license_back_path',
            'proof_of_billing' => 'proof_of_billing_path',
        ];

        foreach ($documentFields as $inputName => $dbColumn) {
            if ($request->hasFile($inputName) && $request->file($inputName) !== null && $request->file($inputName)->isValid()) {
                $path = $request->file($inputName)->store('client-documents/'.$profile->id, 'public');
                if ($path) {
                    // Delete old file if exists
                    $existingDoc = $profile->identityDocuments;
                    if ($existingDoc && $existingDoc->{$dbColumn}) {
                        Storage::disk('public')->delete($existingDoc->{$dbColumn});
                    }
                    $docData[$dbColumn] = $path;
                }
            }
        }

        if (! empty($docData)) {
            if ($profile->identityDocuments) {
                $profile->identityDocuments->update($docData);
            } else {
                $docData['client_profile_id'] = $profile->id;
                ClientIdentityDocument::create($docData);
            }

            // Reload to check completeness
            $profile->load('identityDocuments');
            if ($profile->identityDocuments && $profile->identityDocuments->allDocumentsUploaded()) {
                $profile->identityDocuments->update(['is_complete' => true]);
            }
        }

        return redirect()->route('client.profile.documents')->with('status', 'Documents uploaded successfully.');
    }

    /**
     * Display the profile summary page.
     */
    public function show(): View
    {
        $profile = ClientProfile::with('identityDocuments')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('panels.client.profile-show', compact('profile'));
    }
}
