<?php

namespace App\Models;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'driver_code', 'user_id', 'middle_name', 'suffix', 'date_of_birth', 'gender', 'address', 'city', 'profile_photo_path',
        'license_type', 'license_issued_at', 'license_expires_at', 'license_restrictions', 'license_front_path', 'license_back_path',
        'employment_type', 'date_hired', 'employment_status', 'years_driving_experience', 'professional_driving_experience',
        'vehicle_experience', 'service_experience', 'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone',
        'emergency_contact_address', 'approval_status', 'reviewed_at', 'reviewed_by', 'rejection_reason',
        'full_name',
        'phone',
        'email',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'license_issued_at' => 'date',
            'license_expires_at' => 'date',
            'date_hired' => 'date',
            'vehicle_experience' => 'array',
            'service_experience' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(DriverEvaluation::class, 'driver_license_number', 'license_number');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(DriverRate::class, 'driver_license_number', 'license_number');
    }

    public function bookingApplications(): HasMany
    {
        return $this->hasMany(DriverBookingApplication::class, 'driver_license_number', 'license_number');
    }

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
