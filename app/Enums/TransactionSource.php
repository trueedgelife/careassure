<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransactionSource: string implements HasLabel
{
    case CouncilPayment = 'council_payment';
    case NhsChc = 'nhs_chc';
    case ClientContribution = 'client_contribution';
    case Wages = 'wages';
    case PayrollProvider = 'payroll_provider';
    case Expense = 'expense';
    case Refund = 'refund';
    case Adjustment = 'adjustment';
    case Clawback = 'clawback';
    case OpeningBalance = 'opening_balance';

    public function getLabel(): string
    {
        return match ($this) {
            self::CouncilPayment => 'Council Payment',
            self::NhsChc => 'NHS CHC Payment',
            self::ClientContribution => 'Client Contribution',
            self::Wages => 'Wages',
            self::PayrollProvider => 'Payroll Provider',
            self::Expense => 'Expense',
            self::Refund => 'Refund',
            self::Adjustment => 'Adjustment',
            self::Clawback => 'Clawback',
            self::OpeningBalance => 'Opening Balance',
        };
    }
}
