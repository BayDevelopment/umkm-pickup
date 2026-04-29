<?php

namespace App\Providers;

use App\Listeners\SendEmailVerifiedNotification;
use App\Models\OrderModel;
use App\Observers\OrderObserver;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        OrderModel::observe(OrderObserver::class);
        Event::listen(Verified::class, SendEmailVerifiedNotification::class);

        // Block semua akses Filament jika status bukan active
        Gate::before(function ($user, $ability) {
            if ($user->status !== 'active') {
                return false;
            }
        });

        if (session('status')) {
            Notification::make()
                ->title('Password berhasil diperbarui')
                ->success()
                ->send();
        }

        \Illuminate\Support\Facades\Lang::addLines([
            'filament-forms::components/select.no_options_message' => 'Data tidak ditemukan.',
        ], 'en');
    }
}
