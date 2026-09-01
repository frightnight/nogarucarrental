<?php

namespace App\Models;

use App\BusinessFeature;
use Database\Factories\BusinessPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

class BusinessPlan extends Model
{
    /** @use HasFactory<BusinessPlanFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price', 'vehicle_limit'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'vehicle_limit' => 'integer'];
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'business_plan_permissions')->withTimestamps();
    }

    public function allows(BusinessFeature $feature): bool
    {
        return $this->permissions->contains('name', $feature->value);
    }

    public function formattedPrice(): string
    {
        return '₱'.number_format((float) $this->price, 0).($this->price > 0 ? '/month' : '');
    }
}
