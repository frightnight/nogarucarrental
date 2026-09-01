<?php

namespace Database\Factories;

use App\Models\BusinessPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessPlan>
 */
class BusinessPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Starter',
            'slug' => 'starter',
            'price' => 0,
            'vehicle_limit' => 3,
        ];
    }
}
