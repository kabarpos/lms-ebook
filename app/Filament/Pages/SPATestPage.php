<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;

class SPATestPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = null;
    
    protected static ?string $navigationLabel = 'SPA Testing';
    
    protected static ?string $title = 'SPA Testing Page';
    
    public function getView(): string
    {
        return 'filament.pages.spa-test-page';
    }
    
    protected static \UnitEnum | string | null $navigationGroup = 'System';
    
    protected static ?int $navigationSort = 99;
    
    // Only available in development environments
    public static function shouldRegisterNavigation(): bool
    {
        return app()->environment(['local', 'testing']);
    }
    
    public static function canAccess(): bool
    {
        return app()->environment(['local', 'testing']);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('testExternalLink')
                ->label('Test External Link')
                ->url('https://docs.filamentphp.com')
                ->openUrlInNewTab()
                ->icon('heroicon-o-arrow-top-right-on-square'),
                
            Action::make('testGitHub')
                ->label('Test GitHub Link')
                ->url('https://github.com/filamentphp/filament')
                ->openUrlInNewTab()
                ->icon('heroicon-o-arrow-top-right-on-square'),
                
            Action::make('testLaravel')
                ->label('Test Laravel Docs')
                ->url('https://laravel.com/docs')
                ->openUrlInNewTab()
                ->icon('heroicon-o-arrow-top-right-on-square'),
                
            Action::make('downloadTest')
                ->label('Test Download')
                ->action(function () {
                    // Simulate download action
                    return response()->download(
                        public_path('favicon.ico'),
                        'test-download.ico'
                    );
                })
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}