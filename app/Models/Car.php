<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'vehicle_type',
        'car_model',
        'variant',
        'transmission',
        'seats',
        'rental_type',
        'status',
        'plate_number',
        'year_model',
        'ownership',
        'owner_id',
        'registration_expires_at',
        'insurance_expires_at',
    ];

    protected $casts = [
        'year_model' => 'integer',
        'seats' => 'integer',
        'registration_expires_at' => 'date',
        'insurance_expires_at' => 'date',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }
}
