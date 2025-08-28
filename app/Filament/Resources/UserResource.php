<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Customers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make('name')
                ->maxLength(255)
                ->required(),

                TextInput::make('email')
                ->maxLength(255)
                ->email()
                ->required(),

                TextInput::make('password')
                ->helperText('Minimum 9 characters')
                ->password() // This makes it a password field
                ->required()
                ->minLength(9) // Minimum length for the password
                ->maxLength(255),

                TextInput::make('whatsapp_number')
                ->label('WhatsApp Number')
                ->placeholder('+62812345678')
                ->tel()
                ->maxLength(20)
                ->required(),

                Select::make('roles')
                ->label('Role')
                ->relationship('roles', 'name')
                ->required(),

                FileUpload::make('photo')
                ->required()
                ->image(),

                // Verification Status Fields
                Toggle::make('email_verified_at')
                    ->label('Email Verified')
                    ->helperText('Toggle untuk verifikasi email user')
                    ->reactive(),

                Toggle::make('whatsapp_verified_at')
                    ->label('WhatsApp Verified')
                    ->helperText('Toggle untuk verifikasi WhatsApp user')
                    ->reactive(),

                Toggle::make('is_account_active')
                    ->label('Account Active')
                    ->helperText('Status aktif akun user (otomatis diatur berdasarkan verifikasi)')
                    ->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('photo'),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->searchable(),

                IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->email_verified_at !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('whatsapp_verified_at')
                    ->label('WhatsApp Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->whatsapp_verified_at !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                ToggleColumn::make('is_account_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger')
                    ->beforeStateUpdated(function ($record, $state) {
                        // Validate that user has at least one verification method if being activated
                        if ($state && !$record->email_verified_at && !$record->whatsapp_verified_at) {
                            Notification::make()
                                ->title('Tidak dapat mengaktifkan akun')
                                ->body('User harus memiliki minimal satu verifikasi (email atau WhatsApp) sebelum diaktifkan.')
                                ->danger()
                                ->send();
                            
                            return false; // Prevent the update
                        }
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('Status akun berhasil diperbarui')
                            ->body($state ? 'Akun telah diaktifkan' : 'Akun telah dinonaktifkan')
                            ->success()
                            ->send();
                    }),

                TextColumn::make('roles.name')
                    ->label('Role'),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                
                Action::make('verify_email')
                    ->label('Verify Email')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->visible(fn ($record) => $record->email_verified_at === null)
                    ->action(function ($record) {
                        $record->update(['email_verified_at' => now()]);
                        
                        // Auto-activate account if conditions are met
                        if ($record->whatsapp_verified_at || $record->email_verified_at) {
                            $record->update(['is_account_active' => true]);
                        }
                        
                        Notification::make()
                            ->title('Email berhasil diverifikasi')
                            ->body('Email user telah diverifikasi secara manual')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Email User')
                    ->modalDescription('Apakah Anda yakin ingin memverifikasi email user ini?'),
                
                Action::make('verify_whatsapp')
                    ->label('Verify WhatsApp')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('success')
                    ->visible(fn ($record) => $record->whatsapp_verified_at === null)
                    ->action(function ($record) {
                        $record->update(['whatsapp_verified_at' => now()]);
                        
                        // Auto-activate account if conditions are met
                        if ($record->whatsapp_verified_at || $record->email_verified_at) {
                            $record->update(['is_account_active' => true]);
                        }
                        
                        Notification::make()
                            ->title('WhatsApp berhasil diverifikasi')
                            ->body('WhatsApp user telah diverifikasi secara manual')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi WhatsApp User')
                    ->modalDescription('Apakah Anda yakin ingin memverifikasi WhatsApp user ini?'),
                    
                Action::make('reset_verification')
                    ->label('Reset Verification')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn ($record) => $record->email_verified_at || $record->whatsapp_verified_at)
                    ->action(function ($record) {
                        $record->update([
                            'email_verified_at' => null,
                            'whatsapp_verified_at' => null,
                            'is_account_active' => false
                        ]);
                        
                        Notification::make()
                            ->title('Verifikasi berhasil direset')
                            ->body('Semua status verifikasi user telah direset')
                            ->warning()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reset Semua Verifikasi')
                    ->modalDescription('Apakah Anda yakin ingin mereset semua status verifikasi user ini? User harus verifikasi ulang.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
