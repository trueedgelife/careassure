<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ConfirmationType: string implements HasLabel
{
    case Carer = 'carer';
    case Delegate = 'delegate';
    case ServiceUser = 'service_user';

    public function getLabel(): string
    {
        return match ($this) {
            self::Carer => 'Carer',
            self::Delegate => 'Delegate',
            self::ServiceUser => 'Service User',
        };
    }
}
