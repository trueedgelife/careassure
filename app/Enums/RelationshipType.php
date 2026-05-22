<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RelationshipType: string implements HasLabel
{
    case Spouse = 'spouse';
    case Parent = 'parent';
    case Child = 'child';
    case Sibling = 'sibling';
    case NextOfKin = 'next_of_kin';
    case Advocate = 'advocate';
    case Attorney = 'attorney';
    case Deputy = 'deputy';
    case SocialWorker = 'social_worker';
    case Gp = 'gp';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Spouse => 'Spouse / Partner',
            self::Parent => 'Parent',
            self::Child => 'Child',
            self::Sibling => 'Sibling',
            self::NextOfKin => 'Next of Kin',
            self::Advocate => 'Advocate',
            self::Attorney => 'Attorney (LPA)',
            self::Deputy => 'Deputy (Court-appointed)',
            self::SocialWorker => 'Social Worker',
            self::Gp => 'GP',
            self::Other => 'Other',
        };
    }
}
