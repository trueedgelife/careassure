<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CarerRateType: string implements HasLabel, HasColor
{
    case Day = 'day';
    case Night = 'night';
    case SleepIn = 'sleep_in';
    case Weekend = 'weekend';
    case BankHoliday = 'bank_holiday';

    public function getLabel(): string
    {
        return match ($this) {
            self::Day => 'Day',
            self::Night => 'Night',
            self::SleepIn => 'Sleep-in',
            self::Weekend => 'Weekend',
            self::BankHoliday => 'Bank Holiday',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Day => 'success',
            self::Night => 'info',
            self::SleepIn => 'warning',
            self::Weekend => 'primary',
            self::BankHoliday => 'danger',
        };
    }
}
