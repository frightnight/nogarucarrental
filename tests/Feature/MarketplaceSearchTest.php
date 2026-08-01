<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Car;
use App\Models\Rate;
use App\Models\User;
use Tests\TestCase;

class MarketplaceSearchTest extends TestCase
{
    public function test_visitors_can_filter_available_marketplace_vehicles(): void
    {
        $legazpiRental = Business::factory()->create(['city' => 'Legazpi']);
        $otherRental = Business::factory()->create(['city' => 'Naga']);
        $availableCar = Car::factory()->for($legazpiRental)->create([
            'car_model' => 'Toyota Fortuner',
            'vehicle_type' => 'SUV',
            'transmission' => 'Automatic',
            'seats' => 7,
            'status' => 'available',
        ]);
        Rate::factory()->for($availableCar)->create(['name' => '24hrs', 'value' => 3800]);
        Car::factory()->for($otherRental)->create(['car_model' => 'Unavailable Sedan', 'status' => 'maintenance']);

        $this->get(route('marketplace.index', [
            'location' => 'Legazpi',
            'vehicle_type' => 'SUV',
            'transmission' => 'Automatic',
            'seats' => 7,
            'max_price' => 4000,
        ]))
            ->assertOk()
            ->assertSee('Toyota Fortuner')
            ->assertDontSee('Unavailable Sedan');
    }

    public function test_booked_vehicles_are_excluded_for_overlapping_dates(): void
    {
        $business = Business::factory()->create();
        $car = Car::factory()->for($business)->create(['car_model' => 'Booked Fortuner', 'status' => 'available']);
        $customer = User::factory()->create();

        Booking::create([
            'user_id' => $customer->id,
            'business_id' => $business->id,
            'car_id' => $car->id,
            'rental_type' => 'self_drive',
            'pickup_date' => '2026-08-10',
            'pickup_time' => '08:00',
            'pickup_location' => 'Legazpi',
            'destination_itinerary' => 'Legazpi',
            'preferred_vehicle' => 'Booked Fortuner',
            'passengers_count' => 2,
            'return_date' => '2026-08-12',
            'return_time' => '08:00',
            'handover_option' => 'customer_pickup',
            'status' => 'finalized',
        ]);

        $this->get(route('marketplace.index', ['pickup_date' => '2026-08-11', 'return_date' => '2026-08-13']))
            ->assertOk()
            ->assertDontSee('Booked Fortuner');
    }
}
