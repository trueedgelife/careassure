<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExpenseCategory: string implements HasLabel
{
    case PayrollService = 'payroll_service';
    case Insurance = 'insurance';
    case Training = 'training';
    case Equipment = 'equipment';
    case Transport = 'transport';
    case AgencyCover = 'agency_cover';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::PayrollService => 'Payroll Service',
            self::Insurance => 'Insurance',
            self::Training => 'Training',
            self::Equipment => 'Equipment',
            self::Transport => 'Transport',
            self::AgencyCover => 'Agency Cover',
            self::Other => 'Other',
        };
    }
}
