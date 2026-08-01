<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformLandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'featured_business_slugs',
        'featured_car_ids',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'featured_business_slugs' => 'array',
            'featured_car_ids' => 'array',
        ];
    }
}
