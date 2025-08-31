<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\DataOverview;
use App\Filament\Widgets\RecentTransactions;
use App\Filament\Widgets\TopCourses;

class Data extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = null;

    protected static \UnitEnum | string | null $navigationGroup = 'General';

    protected static ?int $navigationSort = 11;

    protected static ?string $title = 'Data';

    protected static ?string $navigationLabel = 'Data';

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'data';
    }

    public function getTitle(): string
    {
        return 'Data';
    }

    public function getView(): string
    {
        return 'filament.pages.data';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DataOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RecentTransactions::class,
            TopCourses::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }
}