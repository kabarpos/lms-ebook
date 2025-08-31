<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;
use App\Filament\Widgets\StatsOverview;

class Statistik extends Dashboard
{
    protected static string | \BackedEnum | null $navigationIcon = null;

    protected static \UnitEnum | string | null $navigationGroup = 'General';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Statistik';

    protected static ?string $navigationLabel = 'Statistik';

    public static function getNavigationIcon(): string | \BackedEnum | \Illuminate\Contracts\Support\Htmlable | null
    {
        return null;
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'statistics';
    }

    public function getTitle(): string
    {
        return 'Statistik';
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getColumns(): array | int
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 4,
        ];
    }
}