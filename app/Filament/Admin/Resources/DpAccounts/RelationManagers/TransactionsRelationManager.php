<?php

namespace App\Filament\Admin\Resources\DpAccounts\RelationManagers;

use App\Enums\TransactionDirection;
use App\Enums\TransactionSource;
use App\Models\DpTransaction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Ledger';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('transaction_date', 'desc')
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('direction')
                    ->badge(),
                TextColumn::make('source_type')
                    ->label('Source')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('amount')
                    ->money('GBP')
                    ->color(fn ($record) => $record->direction === TransactionDirection::Credit ? 'success' : 'danger'),
                IconColumn::make('reconciled_at')
                    ->label('Reconciled')
                    ->boolean()
                    ->state(fn ($record) => $record->reconciled_at !== null),
                TextColumn::make('bank_reference')
                    ->label('Bank ref')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('notes')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->headerActions([
                Action::make('recordPayment')
                    ->label('Record payment')
                    ->icon('heroicon-o-plus-circle')
                    ->schema([
                        Select::make('direction')
                            ->options(TransactionDirection::class)
                            ->default(TransactionDirection::Credit)
                            ->required(),
                        Select::make('source_type')
                            ->label('Source')
                            ->options(TransactionSource::class)
                            ->default(TransactionSource::CouncilPayment)
                            ->required(),
                        TextInput::make('amount')
                            ->numeric()
                            ->prefix('£')
                            ->required(),
                        DatePicker::make('transaction_date')
                            ->default(now())
                            ->required(),
                        TextInput::make('bank_reference')
                            ->label('Bank reference'),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data) {
                        $account = $this->getOwnerRecord();

                        DpTransaction::create([
                            'tenant_id' => $account->tenant_id,
                            'dp_account_id' => $account->id,
                            'direction' => $data['direction'],
                            'source_type' => $data['source_type'],
                            'amount' => $data['amount'],
                            'transaction_date' => $data['transaction_date'],
                            'bank_reference' => $data['bank_reference'] ?? null,
                            'notes' => $data['notes'] ?? null,
                            'created_by' => auth()->id(),
                        ]);
                    })
                    ->modalHeading('Record a transaction'),
            ])
            ->recordActions([
                Action::make('reconcile')
                    ->label('Reconcile')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Model $record) => $record->reconciled_at === null)
                    ->schema([
                        TextInput::make('bank_reference')
                            ->label('Bank reference'),
                    ])
                    ->action(function (Model $record, array $data) {
                        $record->update([
                            'reconciled_at' => now(),
                            'bank_reference' => $data['bank_reference'] ?? $record->bank_reference,
                        ]);
                    }),
                Action::make('reverse')
                    ->label('Reverse')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This posts an opposite-direction entry that cancels this transaction out. The original row is preserved for the audit trail.')
                    ->schema([
                        Textarea::make('reason')
                            ->label('Reason for reversal')
                            ->required(),
                    ])
                    ->action(function (Model $record, array $data) {
                        $record->reverse($data['reason']);
                    }),
            ]);
    }
}
