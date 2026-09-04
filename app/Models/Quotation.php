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
        'quotation_footnote_id',
        'quotation_number',
        'quotation_date',
        'title',
        'client_name',
        'package_type',
        'itinerary',
        'other_payments',
        'hidden_charges',
        'itinerary_start_address',
        'itinerary_start_latitude',
        'itinerary_start_longitude',
        'itinerary_end_address',
        'itinerary_end_latitude',
        'itinerary_end_longitude',
        'footnote_content',
        'total_distance_km',
        'vehicle_rate_name',
        'vehicle_rate',
        'driver_rate',
        'distance_rate',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'itinerary' => 'array',
            'other_payments' => 'array',
            'quotation_date' => 'date',
            'hidden_charges' => 'decimal:2',
            'itinerary_start_latitude' => 'decimal:7',
            'itinerary_start_longitude' => 'decimal:7',
            'itinerary_end_latitude' => 'decimal:7',
            'itinerary_end_longitude' => 'decimal:7',
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

    public function footnote(): BelongsTo
    {
        return $this->belongsTo(QuotationFootnote::class, 'quotation_footnote_id');
    }
}
