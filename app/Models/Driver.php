<?php

namespace App\Models;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    protected $primaryKey = 'license_number';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'license_number',
        'full_name',
        'phone',
        'email',
    ];

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_driver', 'driver_license_number', 'business_id', 'license_number')
            ->withPivot(['daily_rate', 'is_available', 'is_default'])
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'driver_license_number', 'license_number');
    }
}
