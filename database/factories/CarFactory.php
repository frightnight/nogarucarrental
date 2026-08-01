<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'vehicle_type' => fake()->randomElement(['Sedan', 'SUV', 'Hatchback', 'Truck', 'Van']),
            'car_model' => fake()->word().' '.fake()->randomDigitNotNull(),
            'variant' => fake()->randomElement(['Base', 'Mid', 'Top']),
            'transmission' => fake()->randomElement(['Manual', 'Automatic']),
            'rental_type' => fake()->randomElement(['Daily', 'Weekly', 'Monthly']),
            'plate_number' => strtoupper(fake()->bothify('??? ####')),
            'year_model' => fake()->numberBetween(2015, 2024),
            'ownership' => fake()->randomElement(['owned', 'consigned']),
            'owner_id' => null,
        ];
    }
}
