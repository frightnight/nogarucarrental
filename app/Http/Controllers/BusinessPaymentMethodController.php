<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessPaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessPaymentMethodController extends Controller
{
    public function index(): View
    {
        $business = $this->business();
        $paymentMethods = $business->paymentMethods()->latest()->get();

        return view('panels.payment-methods.index', compact('paymentMethods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business();
        $validated = $request->validate($this->rules());
        $validated['business_id'] = $business->id;
        $validated['qr_code_path'] = null;

        $qrCode = $request->file('qr_code');
        if ($qrCode instanceof UploadedFile && $qrCode->isValid() && $qrCode->getPathname() !== '' && is_file($qrCode->getPathname())) {
            $validated['qr_code_path'] = $this->storeQrCode($qrCode, $business);
        }

        BusinessPaymentMethod::create($validated);

        return back()->with('success', 'Payment method added.');
    }

    public function edit(BusinessPaymentMethod $paymentMethod): View
    {
        abort_unless($paymentMethod->business_id === $this->business()->id, 404);

        return view('panels.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, BusinessPaymentMethod $paymentMethod): RedirectResponse
    {
        $business = $this->business();
        abort_unless($paymentMethod->business_id === $business->id, 404);

        $validated = $request->validate($this->rules());
        $qrCode = $request->file('qr_code');
        if ($qrCode instanceof UploadedFile && $qrCode->isValid() && $qrCode->getPathname() !== '' && is_file($qrCode->getPathname())) {
            $newQrCodePath = $this->storeQrCode($qrCode, $business);

            if ($paymentMethod->qr_code_path !== null) {
                Storage::disk('public')->delete($paymentMethod->qr_code_path);
            }

            $validated['qr_code_path'] = $newQrCodePath;
        }

        $paymentMethod->update($validated);

        return redirect()->route('business.payment-methods.index')->with('success', 'Payment method updated.');
    }

    public function destroy(BusinessPaymentMethod $paymentMethod): RedirectResponse
    {
        abort_unless($paymentMethod->business_id === $this->business()->id, 404);

        if ($paymentMethod->qr_code_path !== null) {
            Storage::disk('public')->delete($paymentMethod->qr_code_path);
        }

        $paymentMethod->delete();

        return back()->with('success', 'Payment method removed.');
    }

    /** @return array<string, array<int, string>> */
    private function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'max:100'],
            'account_type' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100'],
            'swift_code' => ['nullable', 'string', 'max:50'],
            'account_category' => ['required', 'in:Savings,Checking'],
            'currency' => ['required', 'string', 'max:12'],
            'qr_code' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    private function business(): Business
    {
        $business = Auth::user()?->businesses()->first();

        abort_unless($business instanceof Business, 403, 'You are not associated with a business.');

        return $business;
    }

    private function storeQrCode(UploadedFile $qrCode, Business $business): string
    {
        $filename = Str::uuid().'.'.($qrCode->extension() ?: 'jpg');
        $path = 'payment-method-qr-codes/'.$business->getKey().'/'.$filename;

        Storage::disk('public')->put($path, file_get_contents($qrCode->getPathname()));

        return $path;
    }
}
