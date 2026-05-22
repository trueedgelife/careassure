<?php

namespace App\Filament\Admin\Resources\DpAccounts\RelationManagers;

use App\Filament\Admin\Resources\DpAccounts\DpAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
