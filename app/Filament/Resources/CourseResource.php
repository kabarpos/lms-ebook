<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Resources\CourseResource\Pages\ListCourses;
use App\Filament\Resources\CourseResource\Pages\CreateCourse;
use App\Filament\Resources\CourseResource\Pages\EditCourse;
use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Filament\Resources\CourseResource\RelationManagers\CourseSectionsRelationManager;
use App\Models\Course;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string | \BackedEnum | null $navigationIcon = null;

    protected static string | \UnitEnum | null $navigationGroup = 'Products';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $navigationLabel = 'Kursus';
    
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'courses';
    }


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),

                FileUpload::make('thumbnail')
                    ->required()
                    ->image()
                    ->disk('public')
                    ->directory('assets/images/thumbnails')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->maxSize(2048)
                    ->imagePreviewHeight('250')
                    ->loadingIndicatorPosition('left')
                    ->panelAspectRatio('2:1')
                    ->panelLayout('integrated')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left')
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                            ->prepend('thumbnail-'),
                    ),
                    
                TextInput::make('price')
                    ->label('Course Price (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Set to 0 for free courses')
                    ->required(),

                TextInput::make('admin_fee_amount')
                    ->label('Admin Fee (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Admin fee for this course')
                    ->required(),

                Textarea::make('about')
                    ->required(),

                Select::make('is_popular')
                    ->options([
                        true => 'Popular',
                        false => 'Not Popular',
                    ])
                    ->required(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Repeater::make('benefits')
                    ->relationship('benefits')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('thumbnail')
                    ->disk('public')
                    ->visibility('public'),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('category.name'),
                
                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, '', '.') : 'Free')
                    ->color(fn ($state) => $state > 0 ? 'primary' : 'success')
                    ->sortable(),
                    
                TextColumn::make('sales_count')
                    ->label('Sales')
                    ->getStateUsing(fn (Course $record) => $record->transactions()->where('is_paid', true)->count())
                    ->sortable(),
                    
                TextColumn::make('revenue')
                    ->label('Revenue')
                    ->getStateUsing(fn (Course $record) => 'Rp ' . number_format($record->transactions()->where('is_paid', true)->sum('grand_total_amount'), 0, '', '.'))
                    ->sortable(),

                IconColumn::make('is_popular')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Popular'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
            CourseSectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'edit' => EditCourse::route('/{record}/edit'),
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
