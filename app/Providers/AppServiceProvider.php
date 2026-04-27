<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Services\CurrencyService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share currency + rate + admin settings with ALL views
        View::composer('*', function ($view) {
            $currency = CurrencyService::getSelectedCurrency();
            $rate     = CurrencyService::getRate($currency);

            $adminDefaults = [
                'app_name'         => 'Gestion des Dépenses',
                'welcome_message'  => 'Bienvenue sur votre espace de gestion financière.',
                'maintenance_mode' => false,
                'maintenance_message' => 'Le site est en maintenance. Merci de revenir plus tard.',
                'allow_registration'  => true,
            ];
            $adminSettings = Cache::get('admin_system_settings', $adminDefaults);
            $adminSettings = array_merge($adminDefaults, $adminSettings);

            $view->with([
                'currency'      => $currency,
                'currencyRate'  => $rate,
                'adminSettings' => $adminSettings,
            ]);
        });
    }
}
