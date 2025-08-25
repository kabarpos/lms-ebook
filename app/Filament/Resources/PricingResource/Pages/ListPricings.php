<?php

namespace App\Filament\Resources\PricingResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PricingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPricings extends ListRecords
{
    protected static string $resource = PricingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
