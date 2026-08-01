<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleInspection extends Model
{
    protected $fillable = ['booking_id', 'car_id', 'inspected_by', 'stage', 'checklist', 'damage_notes', 'photo_paths'];

    protected function casts(): array
    {
        return ['checklist' => 'array', 'photo_paths' => 'array'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }
}
