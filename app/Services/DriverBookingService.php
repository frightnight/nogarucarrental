<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Driver;
use App\Notifications\DriverBookingAvailableNotification;

class DriverBookingService
{
    public function notifyEligibleDrivers(Booking $booking): void
    {
        if ($booking->rental_type === 'self_drive') {
            return;
        }

        Driver::query()
            ->where('approval_status', 'approved')
            ->whereNotNull('user_id')
            ->with('user')
            ->get()
            ->each(fn (Driver $driver) => $driver->user?->notify(new DriverBookingAvailableNotification($booking)));
    }

    public function tripType(Booking $booking): string
    {
        return match ($booking->rental_type) {
            'airport_pickup' => 'airport_transfer',
            'city_tour' => 'city_tour',
            'out_of_town' => 'out_of_town',
            'wedding_event' => 'wedding_event',
            'corporate' => 'corporate',
            default => $booking->return_date && $booking->pickup_date?->diffInDays($booking->return_date) > 0 ? 'multi_day' : 'full_day',
        };
    }
}
