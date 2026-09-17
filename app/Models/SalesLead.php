<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesLead extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'sales_agent_id', 'converted_booking_id', 'quotation_id', 'name', 'email', 'phone', 'source', 'rental_start_date', 'rental_end_date', 'preferred_vehicle', 'estimated_value', 'status', 'notes', 'next_follow_up_at', 'converted_at'];

    protected function casts(): array
    {
        return ['rental_start_date' => 'date', 'rental_end_date' => 'date', 'estimated_value' => 'decimal:2', 'next_follow_up_at' => 'date', 'converted_at' => 'datetime'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function salesAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_agent_id');
    }

    public function convertedBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'converted_booking_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
