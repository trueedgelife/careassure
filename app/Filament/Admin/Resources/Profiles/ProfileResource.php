<?php

namespace App\Filament\Admin\Resources\Profiles;

use App\Filament\Admin\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Admin\Resources\Profiles\Pages\EditProfile;
use App\Filament\Admin\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Admin\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Admin\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Admin\Resources\Profiles\Schemas\ProfileInfolist;
use App\Filament\Admin\Resources\Profiles\Tables\ProfilesTable;
use App\Models\Profile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'People';

    protected static ?string $modelLabel = 'person';

    protected static ?string $pluralModelLabel = 'people';

    protected static ?string $recordTitleAttribute = 'last_name';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->full_name ?? "Person #{$record?->id}";
    }

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfiles::route('/'),
            'create' => CreateProfile::route('/create'),
            'view' => ViewProfile::route('/{record}'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }

    /**
     * Action: grant a login to a person who doesn't have one.
     * Creates a User in the profile's tenant, links it, assigns roles,
     * and shows a one-time generated password. Hidden once a login exists.
     */
    public static function grantLoginAction(): Action
    {
        return Action::make('grantLogin')
            ->label('Grant login')
            ->icon('heroicon-o-key')
            ->color('success')
            ->visible(fn (Profile $record) => $record->user_id === null)
            ->modalHeading('Grant a login')
            ->modalDescription(fn (Profile $record) => "Create a login account for {$record->full_name}. A one-time password will be shown after creation.")
            ->schema([
                TextInput::make('email')
                    ->label('Login email')
                    ->email()
                    ->required()
                    ->unique(table: User::class, column: 'email')
                    ->helperText('Must be unique across all users.'),
                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->required()
                    ->options([
                        'tenant_admin' => 'Tenant Administrator',
                        'care_coordinator' => 'Care Coordinator',
                        'carer' => 'Carer / Personal Assistant',
                        'service_user' => 'Service User',
                        'delegate' => 'Delegate (Family / Advocate)',
                        'council_reviewer' => 'Council Reviewer (read-only)',
                    ])
                    ->helperText('A person may hold several roles (e.g. carer and service user).'),
            ])
            ->action(function (Profile $record, array $data) {
                // Generate a readable one-time password.
                $plainPassword = Str::password(12, symbols: false);

                // Create the user in the SAME tenant as the profile.
                $user = User::create([
                    'tenant_id' => $record->tenant_id,
                    'name' => $record->full_name,
                    'email' => $data['email'],
                    'password' => $plainPassword, // hashed by the model cast
                    'is_super_admin' => false,
                    'is_active' => true,
                ]);
                $user->email_verified_at = now();
                $user->save();

                // Assign roles within the tenant's team context.
                app(PermissionRegistrar::class)->setPermissionsTeamId($record->tenant_id);
                $user->syncRoles($data['roles']);

                // Link the profile to the new user.
                $record->update(['user_id' => $user->id]);

                // Show the one-time password (no mail configured yet).
                Notification::make()
                    ->title('Login created')
                    ->body("Email: {$data['email']}\nTemporary password: {$plainPassword}\n\nShare this securely. It is shown only once.")
                    ->success()
                    ->persistent()
                    ->send();
            });
    }
}
