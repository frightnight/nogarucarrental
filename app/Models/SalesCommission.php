<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCommission extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'sales_agent_id', 'booking_id', 'rate_percent', 'amount', 'status', 'approved_at', 'approved_by'];

    protected function casts(): array
    {
        return ['rate_percent' => 'decimal:2', 'amount' => 'decimal:2', 'approved_at' => 'datetime'];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function salesAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_agent_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
