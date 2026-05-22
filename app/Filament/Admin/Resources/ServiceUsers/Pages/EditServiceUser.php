<?php

namespace App\Filament\Admin\Resources\ServiceUsers\Pages;

use App\Filament\Admin\Resources\ServiceUsers\ServiceUserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceUser extends EditRecord
{
    protected static string $resource = ServiceUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
