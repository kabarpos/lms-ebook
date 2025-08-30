<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Data extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = null;

    protected static \UnitEnum | string | null $navigationGroup = 'General';

    protected static ?int $navigationSort = 11;

    protected static ?string $title = 'Data';

    protected static ?string $navigationLabel = 'Data';

    public function getTitle(): string
    {
        return 'Data';
    }

    protected string $view = 'filament.pages.data';
}