<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Car;
use App\Models\User;
use App\Notifications\BookingFinalizedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BusinessBookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_user_can_finalize_booking_with_an_available_replacement_vehicle(): void
    {
        Notification::fake();
        [$owner, $business] = $this->businessUser();
        $client = User::factory()->create();
        $requestedCar = Car::factory()->for($business)->create(['car_model' => 'Requested vehicle']);
        $replacementCar = Car::factory()->for($business)->create(['car_model' => 'Available vehicle']);
        $booking = Booking::create([
            'user_id' => $client->id,
            'business_id' => $business->id,
            'car_id' => $requestedCar->id,
            'rental_type' => 'self_drive',
            'pickup_date' => '2026-08-01',
            'pickup_time' => '08:00',
            'pickup_location' => 'Not applicable',
            'destination_itinerary' => 'Legazpi',
            'preferred_vehicle' => 'Requested vehicle',
            'passengers_count' => 2,
            'return_date' => '2026-08-02',
            'return_time' => '08:00',
            'handover_option' => 'customer_pickup',
            'status' => 'pending_review',
        ]);

        $this->actingAs($owner)->put(route('business.bookings.update', $booking), [
            'rental_type' => 'self_drive',
            'car_id' => $replacementCar->id,
            'passengers_count' => 2,
            'destination_itinerary' => 'Legazpi',
            'pickup_date' => '2026-08-01',
            'pickup_time' => '08:00',
            'return_date' => '2026-08-02',
            'return_time' => '08:00',
            'return_location' => 'Legazpi',
            'handover_option' => 'customer_pickup',
            'final_rate' => 4000,
            'delivery_fee' => 300,
            'pickup_fee' => 200,
            'reservation_fee' => 1000,
        ])->assertRedirect(route('business.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'car_id' => $replacementCar->id,
            'preferred_vehicle' => 'Available vehicle',
            'delivery_fee' => 300,
            'pickup_fee' => 200,
            'reservation_fee' => 1000,
            'status' => 'finalized',
        ]);
        Notification::assertSentTo($client, BookingFinalizedNotification::class);
    }

    /** @return array{User, Business} */
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
