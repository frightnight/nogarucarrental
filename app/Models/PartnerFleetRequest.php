<?php

namespace App\Models;

use Database\Factories\PartnerFleetRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerFleetRequest extends Model
{
    /** @use HasFactory<PartnerFleetRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'requesting_business_id',
        'partner_business_id',
        'car_id',
        'status',
        'responded_at',
    ];

    protected function casts(): array
    {
        return ['responded_at' => 'datetime'];
    }

    public function requestingBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'requesting_business_id');
    }

    public function partnerBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'partner_business_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
