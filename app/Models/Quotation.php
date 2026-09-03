<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    protected $fillable = [
        'business_id',
        'car_id',
        'driver_license_number',
        'quotation_number',
        'title',
        'client_name',
        'package_type',
        'itinerary',
        'total_distance_km',
        'vehicle_rate',
        'driver_rate',
        'distance_rate',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'itinerary' => 'array',
            'total_distance_km' => 'decimal:2',
            'vehicle_rate' => 'decimal:2',
            'driver_rate' => 'decimal:2',
            'distance_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_license_number', 'license_number');
    }
}
