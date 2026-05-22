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
    'tenant_id', 'carer_id',
    'dbs_certificate_number', 'dbs_issued_on', 'dbs_on_update_service',
    'right_to_work_verified_on', 'right_to_work_expires_on',
    'references_completed_on',
    'moving_handling_expires_on', 'safeguarding_expires_on',
    'first_aid_expires_on', 'medication_expires_on',
])]
class CarerCompliance extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected $table = 'carer_compliance';

    protected function casts(): array
    {
        return [
            'dbs_issued_on' => 'date',
            'dbs_on_update_service' => 'boolean',
            'right_to_work_verified_on' => 'date',
            'right_to_work_expires_on' => 'date',
            'references_completed_on' => 'date',
            'moving_handling_expires_on' => 'date',
            'safeguarding_expires_on' => 'date',
            'first_aid_expires_on' => 'date',
            'medication_expires_on' => 'date',
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
}
