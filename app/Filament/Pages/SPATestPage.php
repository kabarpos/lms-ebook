<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;

class SPATestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    
    protected static ?string $navigationLabel = 'SPA Testing';
    
    protected static ?string $title = 'SPA Testing Page';
    
    protected static string $view = 'filament.pages.spa-test-page';
    
    protected static ?string $navigationGroup = 'System';
    
    protected static ?int $navigationSort = 99;
    
    // Hide from navigation in production
    public static function shouldRegisterNavigation(): bool
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