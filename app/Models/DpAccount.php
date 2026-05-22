<?php

namespace App\Models;

use App\Enums\TransactionDirection;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'care_package_id', 'opening_balance',
    'opened_on', 'closed_on', 'bank_account_ref',
])]
class DpAccount extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'opened_on' => 'date',
            'closed_on' => 'date',
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

    public function transactions(): HasMany
    {
        return $this->hasMany(DpTransaction::class);
    }

    /**
     * Current balance: opening + credits − debits.
     */
    public function balance(): float
    {
        $credits = (float) $this->transactions()
            ->where('direction', TransactionDirection::Credit->value)->sum('amount');
        $debits = (float) $this->transactions()
            ->where('direction', TransactionDirection::Debit->value)->sum('amount');

        return round((float) $this->opening_balance + $credits - $debits, 2);
    }

    /**
     * Total wages committed via costed shifts on this package — money owed
     * to carers, distinct from what's actually been paid out of the ledger.
     */
    public function committedWages(): float
    {
        return round((float) $this->carePackage?->shifts()->sum('computed_cost'), 2);
    }
}
