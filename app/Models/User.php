<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'phone', 'password', 'provider', 'provider_id', 'sales_agent_status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_users')
            ->withPivot(['business_role', 'commission_rate_percent', 'is_active'])
            ->withTimestamps();
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function salesLeads(): HasMany
    {
        return $this->hasMany(SalesLead::class, 'sales_agent_id');
    }

    public function salesCommissions(): HasMany
    {
        return $this->hasMany(SalesCommission::class, 'sales_agent_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sales_agent_status' => 'string',
        ];
    }
}
