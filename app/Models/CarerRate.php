<?php

namespace App\Models;

use App\Enums\CarerRateType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'carer_id', 'rate_type',
    'hourly_rate', 'flat_rate', 'effective_from', 'effective_to',
])]
class CarerRate extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'rate_type' => CarerRateType::class,
            'hourly_rate' => 'decimal:2',
            'flat_rate' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontLogEmptyChanges();
    }

    public function carer(): BelongsTo
    {
        return $this->belongsTo(Carer::class);
    }

    /**
     * Rates in effect on a given date (effective_from <= date, and
     * effective_to is null or >= date). Layer 6 will use this to cost shifts.
     */
    public function scopeEffectiveOn(Builder $query, string $date): Builder
    {
        return $query
            ->whereDate('effective_from', '<=', $date)
            ->where(function (Builder $q) use ($date) {
                $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date);
            });
    }
}
