<?php

namespace App\Models;

use App\Enums\TransactionDirection;
use App\Enums\TransactionSource;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'dp_account_id', 'direction', 'amount', 'transaction_date',
    'source_type', 'related_type', 'related_id', 'reconciled_at',
    'bank_reference', 'notes', 'created_by',
])]
class DpTransaction extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    /** Financial fields that can never change once a transaction is posted. */
    protected const IMMUTABLE = [
        'dp_account_id', 'direction', 'amount',
        'transaction_date', 'source_type', 'related_type', 'related_id',
    ];

    protected function casts(): array
    {
        return [
            'direction' => TransactionDirection::class,
            'source_type' => TransactionSource::class,
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
            'reconciled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $tx) {
            foreach (self::IMMUTABLE as $field) {
                if ($tx->isDirty($field)) {
                    // Restore the original value so the rejected change doesn't
                    // linger on the in-memory model and break later saves.
                    $tx->setAttribute($field, $tx->getOriginal($field));
                    throw new RuntimeException(
                        "Cannot modify '{$field}' on a posted transaction. Create a reversal instead."
                    );
                }
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontLogEmptyChanges();
    }

    public function dpAccount(): BelongsTo
    {
        return $this->belongsTo(DpAccount::class);
    }

    /**
     * Post an opposite-direction transaction that cancels this one out,
     * preserving both rows for the audit trail.
     */
    public function reverse(?string $reason = null): self
    {
        return static::create([
            'tenant_id' => $this->tenant_id,
            'dp_account_id' => $this->dp_account_id,
            'direction' => $this->direction === TransactionDirection::Credit
                ? TransactionDirection::Debit
                : TransactionDirection::Credit,
            'amount' => $this->amount,
            'transaction_date' => now()->toDateString(),
            'source_type' => TransactionSource::Adjustment,
            'related_type' => static::class,
            'related_id' => $this->id,
            'notes' => $reason ?? "Reversal of transaction #{$this->id}",
            'created_by' => auth()->id(),
        ]);
    }
}
