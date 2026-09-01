<?php

namespace App\Models;

use App\BusinessFeature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'description',
        'business_plan_id',
        'hero_title',
        'hero_subtitle',
        'about_title',
        'about_content',
        'about_features',
        'contact_email',
        'contact_phone',
        'contact_address',
        'garage_address',
        'garage_latitude',
        'garage_longitude',
        'diesel_premium_price_per_liter',
        'diesel_regular_price_per_liter',
        'gasoline_premium_price_per_liter',
        'gasoline_regular_price_per_liter',
        'discount_7_to_14_days_percent',
        'discount_15_to_24_days_percent',
        'discount_25_to_31_days_percent',
    ];

    protected function casts(): array
    {
        return [
            'about_features' => 'array',
            'garage_latitude' => 'decimal:7',
            'garage_longitude' => 'decimal:7',
            'diesel_premium_price_per_liter' => 'decimal:2',
            'diesel_regular_price_per_liter' => 'decimal:2',
            'gasoline_premium_price_per_liter' => 'decimal:2',
            'gasoline_regular_price_per_liter' => 'decimal:2',
            'discount_7_to_14_days_percent' => 'decimal:2',
            'discount_15_to_24_days_percent' => 'decimal:2',
            'discount_25_to_31_days_percent' => 'decimal:2',
        ];
    }

    public function canUseFeature(BusinessFeature $feature): bool
    {
        return $this->plan?->allows($feature) ?? false;
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(BusinessPlan::class, 'business_plan_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->withPivot(['business_role'])
            ->withTimestamps();
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'business_driver', 'business_id', 'driver_license_number', 'id', 'license_number')
            ->withPivot(['daily_rate', 'is_available', 'is_default'])
            ->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
