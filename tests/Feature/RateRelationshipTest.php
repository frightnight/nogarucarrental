<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Rate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_car_has_many_rates(): void
    {
        $car = Car::factory()->create();

        $rate = Rate::factory()->for($car)->create([
            'name' => '24hrs',
            'value' => 4500,
        ]);

        $this->assertTrue($rate->car->is($car));
        $this->assertCount(1, $car->rates);
        $this->assertSame('4500.00', $car->rates->first()->value);
    }
}
