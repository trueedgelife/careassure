<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'service_user_id', 'decision_domain', 'has_capacity',
    'assessed_by', 'assessed_on', 'review_due', 'notes',
])]
class CapacityAssessment extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'has_capacity' => 'boolean',
            'assessed_on' => 'date',
            'review_due' => 'date',
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
}
