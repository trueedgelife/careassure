<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tenant_id', 'user_id',
    'first_name', 'last_name', 'phone', 'dob',
    'address_line_1', 'address_line_2', 'city', 'postcode',
    'emergency_contact_name', 'emergency_contact_phone',
])]
class Profile extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()        // log all fillable attributes
            ->logOnlyDirty()       // ...but only the ones that changed
            ->dontLogEmptyChanges(); // skip no-op updates
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceUser(): HasOne
    {
        return $this->hasOne(ServiceUser::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    public function carer(): HasOne
    {
        return $this->hasOne(Carer::class);
    }

    // Relationships where THIS profile is the contact/relative of a service user
    // (they're someone's emergency contact, delegate, next of kin…).
    public function relationshipsAsContact(): HasMany
    {
        return $this->hasMany(PersonRelationship::class, 'related_profile_id');
    }

    // Convenience "hat" checks for the multi-hat view page and table icons.
    public function getHasLoginAttribute(): bool
    {
        return $this->user_id !== null;
    }

    public function getIsCarerAttribute(): bool
    {
        return $this->carer()->exists();
    }

    public function getIsServiceUserAttribute(): bool
    {
        return $this->serviceUser()->exists();
    }

}
