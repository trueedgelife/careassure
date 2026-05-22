<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum IncidentStatus: string implements HasLabel, HasColor
{
    case Open = 'open';
    case Reviewing = 'reviewing';
    case Resolved = 'resolved';

    public function getLabel(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Reviewing => 'Reviewing',
            self::Resolved => 'Resolved',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open => 'danger',
            self::Reviewing => 'warning',
            self::Resolved => 'success',
        };
    }
}
