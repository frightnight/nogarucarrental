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
        $nonMatchingCar = Car::factory()->for($otherRental)->create([
            'car_model' => 'Naga Sedan',
            'vehicle_type' => 'Sedan',
            'transmission' => 'Manual',
            'seats' => 4,
            'status' => 'available',
        ]);
        Rate::factory()->for($nonMatchingCar)->create(['name' => '24hrs', 'value' => 3000]);

        $this->get(route('marketplace.index', [
            'location' => 'Legazpi',
            'vehicle_type' => 'SUV',
            'transmission' => 'Automatic',
            'seats' => 7,
            'max_price' => 4000,
        ]))
            ->assertOk()
            ->assertSee('Toyota Fortuner')
            ->assertDontSee('Naga Sedan');
    }

    public function test_visitors_can_search_vehicles_by_make_model_or_type(): void
    {
        $business = Business::factory()->create();
        Car::factory()->for($business)->create(['car_model' => 'Toyota Fortuner', 'vehicle_type' => 'SUV', 'status' => 'available']);
        Car::factory()->for($business)->create(['car_model' => 'Honda City', 'vehicle_type' => 'Sedan', 'status' => 'available']);

        $this->get(route('marketplace.index', ['search' => 'Fortuner']))
            ->assertOk()
            ->assertSee('Toyota Fortuner')
            ->assertDontSee('Honda City');
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

    public function test_max_price_filters_using_the_price_shown_on_the_marketplace(): void
    {
        $business = Business::factory()->create();
        $affordableCar = Car::factory()->for($business)->create(['car_model' => 'Affordable Daily Car', 'status' => 'available']);
        $expensiveCarWithCheapShortRate = Car::factory()->for($business)->create(['car_model' => 'Expensive Daily Car', 'status' => 'available']);
        $carWithoutDailyRate = Car::factory()->for($business)->create(['car_model' => 'Short-Term Rate Car', 'status' => 'available']);

        Rate::factory()->for($affordableCar)->create(['name' => '24hrs', 'value' => 3000]);
        Rate::factory()->for($expensiveCarWithCheapShortRate)->create(['name' => '24hrs', 'value' => 5000]);
        Rate::factory()->for($expensiveCarWithCheapShortRate)->create(['name' => '12hrs', 'value' => 2500]);
        Rate::factory()->for($carWithoutDailyRate)->create(['name' => '12hrs', 'value' => 2500]);

        $this->get(route('marketplace.index', ['max_price' => 4000]))
            ->assertOk()
            ->assertSee('Affordable Daily Car')
            ->assertSee('Short-Term Rate Car')
            ->assertDontSee('Expensive Daily Car');
    }

    public function test_max_price_of_zero_is_applied(): void
    {
        $business = Business::factory()->create();
        $freeCar = Car::factory()->for($business)->create(['car_model' => 'Free Daily Car', 'status' => 'available']);
        $paidCar = Car::factory()->for($business)->create(['car_model' => 'Paid Daily Car', 'status' => 'available']);

        Rate::factory()->for($freeCar)->create(['name' => '24hrs', 'value' => 0]);
        Rate::factory()->for($paidCar)->create(['name' => '24hrs', 'value' => 1]);

        $this->get(route('marketplace.index', ['max_price' => 0]))
            ->assertOk()
            ->assertSee('Free Daily Car')
            ->assertDontSee('Paid Daily Car');
    }
}
