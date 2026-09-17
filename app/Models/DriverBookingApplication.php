<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverBookingApplication extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'driver_license_number', 'driver_rate_id', 'quoted_rate', 'quoted_overtime_rate', 'message', 'status'];

    protected function casts(): array
    {
        return ['quoted_rate' => 'decimal:2', 'quoted_overtime_rate' => 'decimal:2'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_license_number', 'license_number');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(DriverRate::class, 'driver_rate_id');
    }
}
