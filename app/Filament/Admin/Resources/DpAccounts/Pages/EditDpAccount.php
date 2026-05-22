<?php

namespace App\Filament\Admin\Resources\DpAccounts\Pages;

use App\Filament\Admin\Resources\DpAccounts\DpAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDpAccount extends EditRecord
{
    protected static string $resource = DpAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
