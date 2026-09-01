<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Car;
use App\Models\PartnerFleetRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartnerFleetRequest>
 */
class PartnerFleetRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requesting_business_id' => Business::factory(),
            'partner_business_id' => Business::factory(),
            'car_id' => Car::factory(),
            'status' => 'pending',
        ];
    }
}
