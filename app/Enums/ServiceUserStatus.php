<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceUserStatus: string implements HasLabel, HasColor
{
    case Assessment = 'assessment';
    case Active = 'active';
    case Paused = 'paused';
    case Transferred = 'transferred';
    case Deceased = 'deceased';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Assessment => 'Assessment',
            self::Active => 'Active',
            self::Paused => 'Paused',
            self::Transferred => 'Transferred',
            self::Deceased => 'Deceased',
            self::Closed => 'Closed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Assessment => 'warning',
            self::Active => 'success',
            self::Paused, self::Closed => 'gray',
            self::Transferred => 'info',
            self::Deceased => 'danger',
        };
    }
}
