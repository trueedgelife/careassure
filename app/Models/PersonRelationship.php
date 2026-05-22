<?php

namespace App\Models;

use App\Enums\LpaType;
use App\Enums\RelationshipType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'service_user_id', 'related_profile_id',
    'relationship_type', 'is_emergency_contact', 'has_lpa', 'lpa_type', 'notes',
])]
class PersonRelationship extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'relationship_type' => RelationshipType::class,
            'lpa_type' => LpaType::class,
            'is_emergency_contact' => 'boolean',
            'has_lpa' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontLogEmptyChanges();
    }

    public function serviceUser(): BelongsTo
    {
        return $this->belongsTo(ServiceUser::class);
    }

    public function relatedProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'related_profile_id');
    }
}
