<?php

namespace App\Http\Controllers;

use App\BusinessPlan;
use App\Models\Business;
use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FleetController extends Controller
{
    private function getBusiness(): Business
    {
        $user = Auth::user();

        $business = $user->businesses()->first();

        abort_unless($business, 403, 'You are not associated with any business.');

        return $business;
    }

    public function index(): View
    {
        $business = $this->getBusiness();
        $cars = $business->cars()->with(['images', 'rates'])->orderBy('created_at', 'desc')->get();

        return view('panels.fleet.index', compact('business', 'cars'));
    }

    public function create(): View
    {
        $business = $this->getBusiness();

        abort_unless($this->hasVehicleCapacity($business), 403, $this->vehicleLimitMessage($business));

        return view('panels.fleet.create', compact('business'));
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->getBusiness();

        if (! $this->hasVehicleCapacity($business)) {
            return redirect()->route('business.fleet.index')->with('error', $this->vehicleLimitMessage($business));
        }

        $validated = $request->validate([
            'car_model' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'variant' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:50',
            'seats' => 'nullable|integer|min:1|max:60',
            'rental_type' => 'nullable|string|max:50',
            'status' => 'nullable|in:available,maintenance,inactive',
            'plate_number' => 'nullable|string|max:50',
            'year_model' => 'nullable|integer|min:1900|max:2100',
            'registration_expires_at' => 'nullable|date',
            'insurance_expires_at' => 'nullable|date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'rates' => 'nullable|array',
            'rates.*.name' => 'nullable|string|max:255|required_with:rates.*.value',
            'rates.*.value' => 'nullable|numeric|min:0|max:99999999.99|required_with:rates.*.name',
        ]);

        $car = $business->cars()->create([
            'car_model' => $validated['car_model'] ?? null,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'variant' => $validated['variant'] ?? null,
            'transmission' => $validated['transmission'] ?? null,
            'seats' => $validated['seats'] ?? null,
            'rental_type' => $validated['rental_type'] ?? null,
            'status' => $validated['status'] ?? 'available',
            'plate_number' => $validated['plate_number'] ?? null,
            'year_model' => $validated['year_model'] ?? null,
            'registration_expires_at' => $validated['registration_expires_at'] ?? null,
            'insurance_expires_at' => $validated['insurance_expires_at'] ?? null,
        ]);

        foreach ($validated['rates'] ?? [] as $rate) {
            if (! isset($rate['name'], $rate['value'])) {
                continue;
            }

            $car->rates()->create($rate);
        }

        // Upload new images
        $this->uploadImages($request, $car, $business);

        return redirect()->route('business.fleet.index')
            ->with('success', 'Vehicle added successfully.');
    }

    public function edit(Car $car): View
    {
        $business = $this->getBusiness();
        abort_unless($car->business_id === $business->id, 403);

        $car->load(['images', 'rates']);

        return view('panels.fleet.edit', compact('business', 'car'));
    }

    public function update(Request $request, Car $car): RedirectResponse
    {
        $business = $this->getBusiness();
        abort_unless($car->business_id === $business->id, 403);

        $validated = $request->validate([
            'car_model' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'variant' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:50',
            'seats' => 'nullable|integer|min:1|max:60',
            'rental_type' => 'nullable|string|max:50',
            'status' => 'nullable|in:available,maintenance,inactive',
            'plate_number' => 'nullable|string|max:50',
            'year_model' => 'nullable|integer|min:1900|max:2100',
            'registration_expires_at' => 'nullable|date',
            'insurance_expires_at' => 'nullable|date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:car_images,id',
        ]);

        $car->update([
            'car_model' => $validated['car_model'] ?? null,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'variant' => $validated['variant'] ?? null,
            'transmission' => $validated['transmission'] ?? null,
            'seats' => $validated['seats'] ?? null,
            'rental_type' => $validated['rental_type'] ?? null,
            'status' => $validated['status'] ?? $car->status,
            'plate_number' => $validated['plate_number'] ?? null,
            'year_model' => $validated['year_model'] ?? null,
            'registration_expires_at' => $validated['registration_expires_at'] ?? null,
            'insurance_expires_at' => $validated['insurance_expires_at'] ?? null,
        ]);

        // Delete selected images
        if ($request->filled('delete_images') && is_array($request->delete_images)) {
            $imagesToDelete = CarImage::whereIn('id', $request->delete_images)
                ->where('car_id', $car->id)
                ->get();
            foreach ($imagesToDelete as $img) {
                $relative = str_replace('/storage/', '', $img->image_path);
                if ($relative && Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->delete($relative);
                }
                $img->delete();
            }
        }

        // Upload new images
        $this->uploadImages($request, $car, $business);

        return redirect()->route('business.fleet.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Car $car): RedirectResponse
    {
        $business = $this->getBusiness();
        abort_unless($car->business_id === $business->id, 403);

        // Delete images from storage
        foreach ($car->images as $img) {
            $relative = str_replace('/storage/', '', $img->image_path);
            if ($relative && Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        }
        $car->images()->delete();
        $car->delete();

        return redirect()->route('business.fleet.index')
            ->with('success', 'Vehicle removed successfully.');
    }

    /**
     * Upload images for a car.
     * Creates the business-specific directory if it doesn't exist.
     */
    private function uploadImages(Request $request, Car $car, Business $business): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $images = $request->file('images');
        if (! is_array($images)) {
            return;
        }

        // Ensure the business directory exists under storage/app/public/cars/{business_id}
        $businessDir = 'cars/'.$business->id;
        if (! Storage::disk('public')->exists($businessDir)) {
            Storage::disk('public')->makeDirectory($businessDir, 0755, true);
            Log::info('Created business images directory', ['dir' => $businessDir]);
        }

        foreach ($images as $index => $image) {
            if (! ($image instanceof UploadedFile) || ! $image->isValid()) {
                Log::warning('Fleet image upload skipped: invalid or not an UploadedFile', [
                    'type' => gettype($image),
                    'valid' => $image instanceof UploadedFile ? $image->isValid() : false,
                ]);

                continue;
            }

            if ($image->getError() !== UPLOAD_ERR_OK) {
                Log::warning('Fleet image upload skipped: upload error', ['error_code' => $image->getError()]);

                continue;
            }

            try {
                // Generate a unique filename to prevent collisions
                $extension = $image->getClientOriginalExtension() ?: 'jpg';
                $filename = time().'_'.uniqid().'.'.$extension;

                // Move the uploaded file using its temporary upload pathname.
                $image->move(Storage::disk('public')->path($businessDir), $filename);
                $storedPath = $businessDir.'/'.$filename;

                if (Storage::disk('public')->exists($storedPath)) {
                    $car->images()->create(['image_path' => '/storage/'.$storedPath]);
                    Log::info('Fleet image uploaded successfully', ['path' => $storedPath]);
                } else {
                    Log::error('Fleet image upload failed: file was not found after move', ['file' => $filename]);
                }
            } catch (\Throwable $e) {
                Log::error('Fleet image upload exception: '.$e->getMessage(), [
                    'file' => $image->getClientOriginalName(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    private function hasVehicleCapacity(Business $business): bool
    {
        $limit = ($business->plan ?? BusinessPlan::Free)->vehicleLimit();

        return $limit === null || $business->cars()->count() < $limit;
    }

    private function vehicleLimitMessage(Business $business): string
    {
        $plan = $business->plan ?? BusinessPlan::Free;

        return "The {$plan->label()} plan supports up to {$plan->vehicleLimit()} vehicles. Upgrade your plan to add more.";
    }
}
