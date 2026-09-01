<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Car;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FleetRateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_user_can_create_a_rate_for_their_car(): void
    {
        [$user, $business] = $this->businessUser();
        $car = Car::factory()->for($business)->create();

        $response = $this->actingAs($user)->post(route('business.fleet.rates.store', $car), [
            'name' => '24hrs',
            'value' => 4500,
        ]);

        $response->assertRedirect(route('business.fleet.edit', $car));
        $this->assertDatabaseHas('rates', [
            'car_id' => $car->id,
            'name' => '24hrs',
            'value' => 4500,
        ]);
    }

    public function test_business_user_can_add_rates_when_creating_a_car(): void
    {
        [$user, $business] = $this->businessUser();

        $response = $this->actingAs($user)->post(route('business.fleet.store'), [
            'car_model' => 'Ford Everest',
            'vehicle_type' => 'SUV',
            'rates' => [
                ['name' => '24hrs', 'value' => 4500],
                ['name' => '12hrs', 'value' => 3000],
            ],
        ]);

        $response->assertRedirect(route('business.fleet.index'));

        $car = Car::where('business_id', $business->id)->where('car_model', 'Ford Everest')->firstOrFail();

        $this->assertDatabaseHas('rates', ['car_id' => $car->id, 'name' => '24hrs', 'value' => 4500]);
        $this->assertDatabaseHas('rates', ['car_id' => $car->id, 'name' => '12hrs', 'value' => 3000]);
    }

    public function test_business_user_can_update_and_delete_a_rate_for_their_car(): void
    {
        [$user, $business] = $this->businessUser();
        $car = Car::factory()->for($business)->create();
        $rate = Rate::factory()->for($car)->create(['name' => '12hrs', 'value' => 3500]);

        $this->actingAs($user)->put(route('business.fleet.rates.update', [$car, $rate]), [
            'name' => '24hrs',
            'value' => 4000,
        ])->assertRedirect(route('business.fleet.edit', $car));

        $this->assertDatabaseHas('rates', ['id' => $rate->id, 'name' => '24hrs', 'value' => 4000]);

        $this->actingAs($user)->delete(route('business.fleet.rates.destroy', [$car, $rate]))
            ->assertRedirect(route('business.fleet.edit', $car));

        $this->assertDatabaseMissing('rates', ['id' => $rate->id]);
    }

    public function test_business_user_cannot_manage_another_businesses_car_rates(): void
    {
        [$user] = $this->businessUser();
        $otherCar = Car::factory()->create();

        $this->actingAs($user)
            ->post(route('business.fleet.rates.store', $otherCar), ['name' => '24hrs', 'value' => 4500])
            ->assertForbidden();
    }

    /**
     * @return array{User, Business}
     */
    private function businessUser(): array
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();

        Role::findOrCreate('business_owner');
        $user->assignRole('business_owner');
        $user->businesses()->attach($business, ['business_role' => 'owner']);

        return [$user, $business];
    }
}
