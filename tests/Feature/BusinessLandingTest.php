<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Car;
use Tests\TestCase;

class BusinessLandingTest extends TestCase
{
    public function test_business_index_page_is_available(): void
    {
        $response = $this->get('/businesses');

        $response->assertOk();
        $response->assertSee('Choose a rental business');
    }

    public function test_business_detail_page_shows_available_cars(): void
    {
        $business = Business::factory()->create([
            'name' => 'Metro Rentals',
            'slug' => 'metro-rentals',
        ]);

        $car = Car::factory()->create([
            'business_id' => $business->id,
            'car_model' => 'Toyota Camry',
        ]);

        $response = $this->get('/businesses/metro-rentals');

        $response->assertOk();
        $response->assertSee('Select this car');
        $response->assertSee('Toyota Camry');
    }
}
