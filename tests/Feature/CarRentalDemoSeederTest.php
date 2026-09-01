<?php

namespace Tests\Feature;

use App\BusinessFeature;
use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\Car;
use App\Models\ClientProfile;
use App\Models\User;
use Database\Seeders\CarRentalDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarRentalDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_two_businesses_for_each_plan_with_complete_fleets_and_one_client(): void
    {
        $this->seed(CarRentalDemoSeeder::class);

        $this->assertSame(8, Business::count());
        $this->assertSame(2, Business::whereHas('plan', fn ($query) => $query->where('slug', 'free'))->count());
        $this->assertSame(2, Business::whereHas('plan', fn ($query) => $query->where('slug', 'basic'))->count());
        $this->assertSame(2, Business::whereHas('plan', fn ($query) => $query->where('slug', 'pro'))->count());
        $this->assertSame(2, Business::whereHas('plan', fn ($query) => $query->where('slug', 'business'))->count());
        $this->assertSame(40, Car::count());
        $this->assertSame(4, BusinessPlan::count());
        $this->assertSame(2, BusinessPlan::where('slug', 'basic')->sole()->permissions()->count());
        $this->assertSame(5, BusinessPlan::where('slug', 'pro')->sole()->permissions()->count());
        $this->assertSame(7, BusinessPlan::where('slug', 'business')->sole()->permissions()->count());
        $this->assertTrue(Business::whereHas('plan', fn ($query) => $query->where('slug', 'pro'))->firstOrFail()->canUseFeature(BusinessFeature::Reports));
        $this->assertFalse(Business::whereHas('plan', fn ($query) => $query->where('slug', 'free'))->firstOrFail()->canUseFeature(BusinessFeature::Reports));

        $businesses = Business::query()->with('cars.images', 'cars.rates')->get();
        $this->assertTrue($businesses->every(
            fn (Business $business): bool => $business->cars->count() === 5
                && $business->cars->every(fn (Car $car): bool => $car->images->count() === 1 && $car->rates->count() === 5),
        ));

        $profile = ClientProfile::query()->with('identityDocuments')->sole();
        $this->assertSame('client@example.com', $profile->email_address);
        $this->assertTrue($profile->identityDocuments->is_complete);
        $this->assertTrue(User::where('email', 'admin@example.com')->sole()->hasRole('administrator'));
    }
}
