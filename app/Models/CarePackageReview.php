<?php

namespace App\Models;

use App\Enums\CarePackageReviewOutcome;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'care_package_id', 'review_date',
    'reviewer_name', 'reviewer_organisation', 'outcome', 'notes',
])]
class CarePackageReview extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'review_date' => 'date',
            'outcome' => CarePackageReviewOutcome::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontLogEmptyChanges();
    }

    public function carePackage(): BelongsTo
    {
        return $this->belongsTo(CarePackage::class);
    }
}
