<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Statistik extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = null;

    protected static \UnitEnum | string | null $navigationGroup = 'General';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Statistik';

    protected static ?string $navigationLabel = 'Statistik';

    public function getTitle(): string
    {
        return 'Statistik';
    }

    protected string $view = 'filament.pages.statistik';
}