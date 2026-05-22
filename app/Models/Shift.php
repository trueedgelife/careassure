<?php

namespace App\Models;

use App\Enums\ShiftStatus;
use App\Models\Concerns\BelongsToTenant;
use App\Enums\CarerRateType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'care_package_id', 'carer_id',
    'scheduled_start_at', 'scheduled_end_at', 'actual_start_at', 'actual_end_at',
    'unpaid_break_minutes', 'support_category', 'notes', 'status',
    'carer_rate_id', 'computed_cost', 'costed_at', 'created_by',
])]
class Shift extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'status' => ShiftStatus::class,
            'scheduled_start_at' => 'datetime',
            'scheduled_end_at' => 'datetime',
            'actual_start_at' => 'datetime',
            'actual_end_at' => 'datetime',
            'computed_cost' => 'decimal:2',
            'costed_at' => 'datetime',
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

    public function carer(): BelongsTo
    {
        return $this->belongsTo(Carer::class);
    }

    public function carerRate(): BelongsTo
    {
        return $this->belongsTo(CarerRate::class);
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(ShiftConfirmation::class);
    }

    /**
     * Paid duration in hours, using actual times if present, else scheduled,
     * minus unpaid breaks. Handles midnight-crossing shifts (full datetimes).
     */
    public function paidHours(): float
    {
        $start = $this->actual_start_at ?? $this->scheduled_start_at;
        $end = $this->actual_end_at ?? $this->scheduled_end_at;

        if (! $start || ! $end) {
            return 0.0;
        }

        $minutes = $start->diffInMinutes($end) - (int) $this->unpaid_break_minutes;

        return max(0, $minutes) / 60;
    }

    /**
     * Snapshot the cost of this shift against the carer's rate IN EFFECT on
     * the shift date. Stores the rate used, the computed cost, and a timestamp,
     * so the figure reflects the rate at the time — not whatever it is today.
     */
    public function cost(CarerRateType $rateType = CarerRateType::Day): self
    {
        $date = ($this->actual_start_at ?? $this->scheduled_start_at)?->toDateString();

        if (! $date) {
            return $this;
        }

        $rate = $this->carer
            ->rates()
            ->effectiveOn($date)
            ->where('rate_type', $rateType->value)
            ->first();

        if (! $rate) {
            return $this;
        }

        // Flat-rate (e.g. sleep-in) uses the flat sum; otherwise hours × hourly.
        $cost = $rate->flat_rate !== null
            ? (float) $rate->flat_rate
            : $this->paidHours() * (float) $rate->hourly_rate;

        $this->forceFill([
            'carer_rate_id' => $rate->id,
            'computed_cost' => round($cost, 2),
            'costed_at' => now(),
        ])->save();

        return $this;
    }
}
