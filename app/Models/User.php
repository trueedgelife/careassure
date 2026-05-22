<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['tenant_id', 'name', 'email', 'password', 'is_super_admin', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Which panel each role is allowed into.
     */
    protected const PANEL_ACCESS = [
        'admin'   => ['tenant_admin', 'care_coordinator'],
        'portal'  => ['service_user', 'delegate', 'carer'],
        'council' => ['council_reviewer'],
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Super admins only use the admin panel.
        if ($this->isSuperAdmin()) {
            return $panel->getId() === 'admin';
        }

        // Defensive: ensure team context is set even if middleware hasn't run.
        if ($this->tenant_id) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($this->tenant_id);
        }

        $allowedRoles = self::PANEL_ACCESS[$panel->getId()] ?? [];

        return $this->hasAnyRole($allowedRoles);
    }
}
