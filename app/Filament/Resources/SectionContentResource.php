<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use App\Models\CourseSection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Resources\SectionContentResource\Pages\ListSectionContents;
use App\Filament\Resources\SectionContentResource\Pages\CreateSectionContent;
use App\Filament\Resources\SectionContentResource\Pages\EditSectionContent;
use App\Filament\Resources\SectionContentResource\Pages;
use App\Filament\Resources\SectionContentResource\RelationManagers;
use App\Models\SectionContent;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectionContentResource extends Resource
{
    protected static ?string $model = SectionContent::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Products';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Select::make('course_section_id')
                ->label('Course Section')
                ->options(function () {
                    return CourseSection::with('course')
                        ->get()
                        ->mapWithKeys(function ($section) {
                            return [
                                $section->id => $section->course
                                    ? "{$section->course->name} - {$section->name}"
                                    : $section->name, // Fallback if course is null
                            ];
                        })
                        ->toArray(); // Convert the collection to an array
                })
                ->searchable()
                ->required(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('content')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                ->sortable()
                    ->searchable(),

                TextColumn::make('courseSection.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('courseSection.course.name')
                    ->sortable()
                    ->searchable(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSectionContents::route('/'),
            'create' => CreateSectionContent::route('/create'),
            'edit' => EditSectionContent::route('/{record}/edit'),
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
