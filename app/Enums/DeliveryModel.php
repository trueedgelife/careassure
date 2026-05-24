<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DeliveryModel: string implements HasLabel, HasColor
{
    case DirectPayment = 'direct_payment';
    case CouncilManaged = 'council_managed';
    case Mixed = 'mixed';

    public function getLabel(): string
    {
        return match ($this) {
            self::DirectPayment => 'Direct Payment',
            self::CouncilManaged => 'Council-Managed',
            self::Mixed => 'Mixed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DirectPayment => 'success',
            self::CouncilManaged => 'info',
            self::Mixed => 'warning',
        };
    }

    /** Whether this delivery model has a family-controlled DP account. */
    public function hasDpAccount(): bool
    {
        return $this !== self::CouncilManaged;
    }
}
