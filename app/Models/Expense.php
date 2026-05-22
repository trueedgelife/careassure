<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use App\Enums\TransactionDirection;
use App\Enums\TransactionSource;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'tenant_id', 'care_package_id', 'category', 'amount',
    'expense_date', 'supplier_name', 'notes', 'created_by',
])]
class Expense extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected function casts(): array
    {
        return [
            'category' => ExpenseCategory::class,
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        // When an expense is recorded, post a matching debit to the package's ledger.
        static::created(function (self $expense) {
            $account = DpAccount::where('care_package_id', $expense->care_package_id)->first();

            if (! $account) {
                return; // no DP account yet — nothing to post against
            }

            DpTransaction::create([
                'tenant_id' => $expense->tenant_id,
                'dp_account_id' => $account->id,
                'direction' => TransactionDirection::Debit,
                'amount' => $expense->amount,
                'transaction_date' => $expense->expense_date,
                'source_type' => TransactionSource::Expense,
                'related_type' => self::class,
                'related_id' => $expense->id,
                'created_by' => $expense->created_by,
            ]);
        });
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
