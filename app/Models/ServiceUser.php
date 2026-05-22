<?php

namespace App\Models;

use App\Enums\ServiceUserStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'profile_id', 'nhs_number', 'council_reference',
    'funding_start_date', 'status', 'is_active',
])]
class ServiceUser extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'status' => ServiceUserStatus::class,
            'funding_start_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontLogEmptyChanges();
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(PersonRelationship::class);
    }

    public function capacityAssessments(): HasMany
    {
        return $this->hasMany(CapacityAssessment::class);
    }
}
