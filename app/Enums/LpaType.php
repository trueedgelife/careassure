<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LpaType: string implements HasLabel
{
    case HealthWelfare = 'health_welfare';
    case PropertyFinance = 'property_finance';
    case Both = 'both';

    public function getLabel(): string
    {
        return match ($this) {
            self::HealthWelfare => 'Health & Welfare',
            self::PropertyFinance => 'Property & Finance',
            self::Both => 'Both',
        };
    }
}
