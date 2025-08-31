<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Data;
use App\Filament\Pages\Statistik;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->spaUrlExceptions([
                // External URLs yang tidak kompatibel dengan SPA
                'https://docs.filamentphp.com/*',
                'https://github.com/*',
                'https://laravel.com/*',
                // URL internal yang memerlukan full page reload
                '/admin/export/*',
                '/admin/download/*',
                '/admin/pdf/*',
                // URL yang menggunakan target="_blank"
                '/admin/external-link/*',
            ])
            ->login()
            ->homeUrl('/admin/statistics')
            ->colors([
                'primary' => Color::Cyan,
                'secondary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                Statistik::class,
                Data::class,
            ])
            ->navigationGroups([
                'General' => NavigationGroup::make()
                    ->label('Dashboard')
                    ->icon('heroicon-o-home')
                    ->collapsed(false),
                'Products' => NavigationGroup::make()
                    ->label('Produk')
                    ->icon('heroicon-o-cube')
                    ->collapsed(false),
                'Customers' => NavigationGroup::make()
                    ->label('Pelanggan')
                    ->icon('heroicon-o-users')
                    ->collapsed(false),
                'System' => NavigationGroup::make()
                    ->label('Sistem')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(false),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
