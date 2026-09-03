<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationFootnote extends Model
{
    protected $fillable = ['business_id', 'title', 'content'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
