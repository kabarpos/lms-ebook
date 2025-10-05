<?php

namespace App\Filament\Pages\Dashboard;

use Filament\Pages\Page;
use App\Filament\Widgets\MonthlyRevenueChart;
use App\Filament\Widgets\SalesPerCategoryChart;

class Statistik extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = null;

    protected static \UnitEnum | string | null $navigationGroup = 'General';

    protected static ?int $navigationSort = 11;

    protected static ?string $title = 'Statistik';

    protected static ?string $navigationLabel = 'Statistik';

    public static function getNavigationIcon(): string | \BackedEnum | \Illuminate\Contracts\Support\Htmlable | null
    {
        return null;
    }

    // Gunakan properti bawaan untuk title/slug agar konsisten dengan navigasi

    protected static ?string $slug = 'statistics';

    public function getHeaderWidgets(): array
    {
        return [
            MonthlyRevenueChart::class,
            SalesPerCategoryChart::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [];
    }
}