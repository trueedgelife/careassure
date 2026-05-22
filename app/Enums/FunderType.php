<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FunderType: string implements HasLabel
{
    case LocalAuthority = 'local_authority';
    case NhsChc = 'nhs_chc';
    case Icb = 'icb';
    case ClientContribution = 'client_contribution';
    case Charity = 'charity';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::LocalAuthority => 'Local Authority',
            self::NhsChc => 'NHS Continuing Healthcare',
            self::Icb => 'ICB',
            self::ClientContribution => 'Client Contribution',
            self::Charity => 'Charity',
            self::Other => 'Other',
        };
    }
}
