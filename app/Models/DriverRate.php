<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverRate extends Model
{
    use HasFactory;

    protected $fillable = ['driver_license_number', 'trip_type', 'rate_type', 'amount', 'overtime_rate', 'is_active'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'overtime_rate' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_license_number', 'license_number');
    }
}
