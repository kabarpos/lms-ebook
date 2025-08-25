<?php

namespace App\Filament\Resources\SectionContentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\SectionContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionContent extends EditRecord
{
    protected static string $resource = SectionContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
