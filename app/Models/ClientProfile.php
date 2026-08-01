<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientProfile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'permanent_address',
        'mobile_numbers',
        'email_address',
        'facebook_url',
        'whatsapp_number',
        'viber_number',
    ];

    protected $casts = [
        'mobile_numbers' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function identityDocuments(): HasOne
    {
        return $this->hasOne(ClientIdentityDocument::class);
    }

    /**
     * Check if profile has complete personal information.
     */
    public function isPersonalInfoComplete(): bool
    {
        return ! empty($this->full_name) && ! empty($this->permanent_address) && ! empty($this->mobile_numbers);
    }

    /**
     * Check if self-drive required documents have been submitted.
     */
    public function isSelfDriveReady(): bool
    {
        return $this->identityDocuments && $this->identityDocuments->is_complete;
    }
}
