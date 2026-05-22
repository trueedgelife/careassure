<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EmploymentType: string implements HasLabel
{
    case Employee = 'employee';
    case Agency = 'agency';
    case SelfEmployed = 'self_employed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Employee => 'Employee',
            self::Agency => 'Agency',
            self::SelfEmployed => 'Self-employed',
        };
    }
}
