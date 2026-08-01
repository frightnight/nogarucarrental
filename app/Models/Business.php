<?php

namespace App\Models;

use App\BusinessFeature;
use App\BusinessPlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'description',
        'plan',
        'hero_title',
        'hero_subtitle',
        'about_title',
        'about_content',
        'about_features',
        'contact_email',
        'contact_phone',
        'contact_address',
    ];

    protected function casts(): array
    {
        return [
            'about_features' => 'array',
            'plan' => BusinessPlan::class,
        ];
    }

    public function canUseFeature(BusinessFeature $feature): bool
    {
        return ($this->plan ?? BusinessPlan::Free)->allows($feature);
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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
