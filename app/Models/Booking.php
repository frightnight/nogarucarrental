<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'guest_first_name',
        'guest_last_name',
        'guest_email',
        'guest_phone',
        'business_id',
        'car_id',
        'driver_license_number',
        'driver_fee',
        'rental_type',
        'pickup_date',
        'pickup_time',
        'pickup_location',
        'pickup_latitude',
        'pickup_longitude',
        'destination_itinerary',
        'itinerary_stops',
        'total_distance_km',
        'preferred_vehicle',
        'passengers_count',
        'return_date',
        'return_time',
        'handover_option',
        'handover_other',
        'return_location',
        'dropoff_latitude',
        'dropoff_longitude',
        'initial_rate',
        'final_rate',
        'delivery_fee',
        'pickup_fee',
        'reservation_fee',
        'owner_notes',
        'special_request',
        'flight_details_path',
        'finalized_at',
        'payment_method',
        'business_payment_method_id',
        'payment_reference_number',
        'payment_proof_path',
        'payment_submitted_at',
        'payment_confirmed_at',
        'status',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'return_date' => 'date',
        'passengers_count' => 'integer',
        'itinerary_stops' => 'array',
        'total_distance_km' => 'decimal:2',
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'dropoff_latitude' => 'decimal:7',
        'dropoff_longitude' => 'decimal:7',
        'initial_rate' => 'decimal:2',
        'final_rate' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'pickup_fee' => 'decimal:2',
        'reservation_fee' => 'decimal:2',
        'finalized_at' => 'datetime',
        'payment_submitted_at' => 'datetime',
        'payment_confirmed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function businessPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(BusinessPaymentMethod::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }

    public function driverEvaluations(): HasMany
    {
        return $this->hasMany(DriverEvaluation::class);
    }

    public function driverApplications(): HasMany
    {
        return $this->hasMany(DriverBookingApplication::class);
    }

    public function serviceLabel(): string
    {
        return match ($this->rental_type) {
            'airport_pickup' => 'airport transfer',
            'city_tour' => 'city tour',
            'out_of_town' => 'out-of-town',
            'wedding_event' => 'wedding/event',
            'corporate' => 'corporate',
            default => 'chauffeur',
        };
    }
}
