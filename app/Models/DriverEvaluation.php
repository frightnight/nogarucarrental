<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'driver_license_number', 'business_id', 'evaluated_by', 'overall_rating',
        'punctuality_rating', 'safety_rating', 'service_rating', 'comments',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_license_number', 'license_number');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
