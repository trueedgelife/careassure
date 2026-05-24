<?php

namespace App\Filament\Portal\Resources\DpAccounts\Pages;

use App\Filament\Portal\Resources\DpAccounts\DpAccountResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDpAccount extends ViewRecord
{
    protected static string $resource = DpAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
