<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientIdentityDocument extends Model
{
    protected $fillable = [
        'client_profile_id',
        'valid_id_1_path',
        'valid_id_2_path',
        'selfie_with_ids_path',
        'ltms_welcome_path',
        'ltms_client_id_path',
        'ltms_license_front_path',
        'ltms_license_back_path',
        'proof_of_billing_path',
        'is_complete',
    ];

    protected $casts = [
        'is_complete' => 'boolean',
    ];

    public function clientProfile(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class);
    }

    /**
     * Check if all required documents for self-drive are uploaded.
     */
    public function allDocumentsUploaded(): bool
    {
        return ! empty($this->valid_id_1_path)
            && ! empty($this->valid_id_2_path)
            && ! empty($this->selfie_with_ids_path)
            && ! empty($this->ltms_welcome_path)
            && ! empty($this->ltms_client_id_path)
            && ! empty($this->ltms_license_front_path)
            && ! empty($this->ltms_license_back_path)
            && ! empty($this->proof_of_billing_path);
    }
}
