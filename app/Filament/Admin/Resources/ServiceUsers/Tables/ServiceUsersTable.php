<?php

namespace App\Filament\Admin\Resources\ServiceUsers\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('council_reference'),
            ]);
    }
}
