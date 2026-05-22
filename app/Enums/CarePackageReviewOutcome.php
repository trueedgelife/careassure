<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CarePackageReviewOutcome: string implements HasLabel
{
    case Continue = 'continue';
    case Increase = 'increase';
    case Decrease = 'decrease';
    case Close = 'close';
    case Referred = 'referred';

    public function getLabel(): string
    {
        return match ($this) {
            self::Continue => 'Continue unchanged',
            self::Increase => 'Increase support',
            self::Decrease => 'Decrease support',
            self::Close => 'Close package',
            self::Referred => 'Referred on',
        };
    }
}
