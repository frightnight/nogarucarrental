<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rate>
 */
class RateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'name' => fake()->randomElement(['24hrs', '12hrs', 'Sorsogon/CamSur surcharge']),
            'value' => fake()->randomFloat(2, 500, 5_000),
        ];
    }
}
