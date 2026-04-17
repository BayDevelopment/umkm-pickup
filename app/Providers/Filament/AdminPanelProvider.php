<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard as PagesDashboard;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName(new HtmlString(
                '<span style="font-style: italic; font-weight: 400;">UMKM</span><span style="font-weight: 700;font-style: italic;">Panel</span>'
            ))
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn() => new HtmlString('
                <div style="text-align:center; margin-top:20px; font-size:12px; color:#6b7280;">
                    Developed by <strong>Bayu Albar Ladici</strong>
                </div>
            ')
            )
            ->renderHook(
                PanelsRenderHook::AUTH_REGISTER_FORM_AFTER,
                fn() => new HtmlString('
                <div style="text-align:center; margin-top:20px; font-size:12px; color:#6b7280;">
                    Developed by <strong>Bayu Albar Ladici</strong>
                </div>
            ')
            )
            ->font('poppins')
            ->login()
            ->registration(\App\Filament\Auth\Register::class)
            ->emailVerification()
            ->passwordReset()
            ->authGuard('web') // WAJIB
            ->authPasswordBroker('users') // WAJIB INI
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            // // ->pages([
            // //     PagesDashboard::class,
            // // ])
            // // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            // ->widgets([
            //     StatsOverview::class
            // ])
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
                \App\Http\Middleware\CheckUserApproval::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn() => view('filament.hooks.flash-notification')
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
