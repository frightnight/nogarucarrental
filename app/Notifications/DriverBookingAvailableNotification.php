<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DriverBookingAvailableNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => 'A new '.$this->booking->serviceLabel().' trip is available for driver applications.',
            'url' => route('driver.dashboard'),
        ];
    }
}
