<?php

namespace App\Filament\Admin\Resources\DpAccounts\Pages;

use App\Filament\Admin\Resources\DpAccounts\DpAccountResource;
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
