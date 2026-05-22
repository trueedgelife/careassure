<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionDirection: string implements HasLabel, HasColor
{
    case Credit = 'credit';
    case Debit = 'debit';

    public function getLabel(): string
    {
        return match ($this) {
            self::Credit => 'Credit (in)',
            self::Debit => 'Debit (out)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Credit => 'success',
            self::Debit => 'danger',
        };
    }
}
