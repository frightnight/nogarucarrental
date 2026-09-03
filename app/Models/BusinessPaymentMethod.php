<?php

namespace App\Models;

use Database\Factories\BusinessPaymentMethodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessPaymentMethod extends Model
{
    /** @use HasFactory<BusinessPaymentMethodFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'payment_method',
        'account_type',
        'account_name',
        'account_number',
        'swift_code',
        'account_category',
        'currency',
        'qr_code_path',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
