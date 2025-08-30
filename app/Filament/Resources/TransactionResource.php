<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Filament\Resources\TransactionResource\Pages\CreateTransaction;
use App\Filament\Resources\TransactionResource\Pages\EditTransaction;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static string | \UnitEnum | null $navigationGroup = 'Customers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Wizard::make([

                    Step::make('Product and Price')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('course_id')
                                        ->relationship('course', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $course = Course::find($state);
                                $price = $course->price;
                                $adminFee = $course->admin_fee_amount ?? 0;
                                $subTotal = $price;
                                $grandTotal = $subTotal + $adminFee;
                                
                                $set('admin_fee_amount', $adminFee);
                                $set('grand_total_amount', $grandTotal);
                                $set('sub_total_amount', $price);
                            }
                        }),
                                ]),

                            Grid::make(3)
                            ->schema([
                                TextInput::make('sub_total_amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->readOnly(),

                                TextInput::make('admin_fee_amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->readOnly()
                                    ->helperText('Admin fee dari course'),

                                TextInput::make('grand_total_amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->readOnly()
                                    ->helperText('Total dengan biaya admin'),
                            ]),


                            Grid::make(2)
                            ->schema([
                                DatePicker::make('started_at')
                                ->required(),
                            ]),
                        ]),

                        Step::make('Customer Information')
                        ->schema([
                            Select::make('user_id')
                                ->relationship('student', 'email')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $user = User::find($state);
                                        if ($user) {
                                            $name = $user->name;
                                            $email = $user->email;

                                            $set('name', $name);
                                            $set('email', $email);
                                        }
                                    }
                                })
                                ->afterStateHydrated(function (callable $set, $state) {
                                    $userId = $state;
                                    if ($userId) {
                                        $user = User::find($userId);
                                        if ($user) {
                                            $name = $user->name;
                                            $email = $user->email;
                                            $set('name', $name);
                                            $set('email', $email);
                                        }
                                    }
                                }),
                            TextInput::make('name')
                                ->required()
                                ->readOnly()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->required()
                                ->readOnly()
                                ->maxLength(255),
                        ]),


                    Step::make('Payment Information')
                        ->schema([

                            ToggleButtons::make('is_paid')
                                ->label('Apakah sudah membayar?')
                                ->boolean()
                                ->grouped()
                                ->icons([
                                    true => 'heroicon-o-pencil',
                                    false => 'heroicon-o-clock',
                                ])
                                ->required(),

                            Select::make('payment_type')
                                ->options([
                                    'Midtrans' => 'Midtrans',
                                    'Manual' => 'Manual',
                                ])
                                ->required(),

                            FileUpload::make('proof')
                                ->image(),
                        ]),

                ])
                ->columnSpan('full') // Use full width for the wizard
                ->columns(1) // Make sure the form has a single column layout
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('student.photo')
                ->circular()
                ->defaultImageUrl(fn ($record) => getUserAvatarWithColor($record->student, 100))
                ,

                TextColumn::make('student.name')
                    ->searchable(),

                TextColumn::make('booking_trx_id')
                ->searchable(),

                TextColumn::make('course.name')
                    ->label('Course'),

                TextColumn::make('admin_fee_amount')
                    ->label('Admin Fee')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, '', '.') : '-')
                    ->sortable(),

                TextColumn::make('grand_total_amount')
                    ->label('Total Amount')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, '', '.'))
                    ->sortable(),

                IconColumn::make('is_paid')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Terverifikasi'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),

                Action::make('approve')
                    ->label('Approve')
                    ->action(function (Transaction $record) {
                        $record->is_paid = true;
                        $record->save();

                        // Trigger the custom notification
                        Notification::make()
                            ->title('Order Approved')
                            ->success()
                            ->body('The Order has been successfully approved.')
                            ->send();

                        // kirim email, kirim sms

                    })
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (?Transaction $record) => $record && !$record->is_paid),
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
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['student', 'course'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
