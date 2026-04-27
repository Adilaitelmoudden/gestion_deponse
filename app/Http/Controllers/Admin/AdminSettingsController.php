<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\CurrencyService;

class AdminSettingsController extends Controller
{
    private const CACHE_KEY = 'admin_system_settings';

    private array $defaults = [
        'app_name'              => 'Gestion des Dépenses',
        'welcome_message'       => 'Bienvenue sur votre espace de gestion financière.',
        'contact_email'         => 'admin@example.com',
        'maintenance_mode'      => false,
        'maintenance_message'   => 'Le site est en maintenance. Merci de revenir plus tard.',
        'allow_registration'    => true,
        'max_transactions_user' => 1000,
        'default_currency'      => 'MAD',
        'notify_new_user'        => false,
        'notify_budget_exceeded' => true,
        'notify_weekly_report'   => false,
        'notify_inactivity'      => false,
    ];

    /** GET /admin/settings */
    public function index()
    {
        $settings = Cache::get(self::CACHE_KEY, $this->defaults);
        $settings = array_merge($this->defaults, $settings);
        $rateInfo = CurrencyService::getRateInfo();
        return view('admin.settings.index', compact('settings', 'rateInfo'));
    }

    /** PUT /admin/settings */
    public function update(Request $request)
    {
        $request->validate([
            'app_name'              => 'required|string|max:100',
            'contact_email'         => 'required|email|max:100',
            'welcome_message'       => 'nullable|string|max:500',
            'maintenance_message'   => 'nullable|string|max:500',
            'max_transactions_user' => 'required|integer|min:1|max:99999',
            'default_currency'      => 'required|string|in:MAD,EUR,USD,GBP,CAD,CHF,SAR,AED',
        ]);

        // Load current settings to preserve everything not in the form
        $current = Cache::get(self::CACHE_KEY, $this->defaults);
        $current = array_merge($this->defaults, $current);

        // Only update the fields that exist in the form
        $current['app_name']              = $request->input('app_name');
        $current['contact_email']         = $request->input('contact_email');
        $current['welcome_message']       = $request->input('welcome_message', '');
        $current['maintenance_message']   = $request->input('maintenance_message', '');
        $current['max_transactions_user'] = (int) $request->input('max_transactions_user');
        $current['default_currency']      = $request->input('default_currency');

        Cache::forever(self::CACHE_KEY, $current);

        CurrencyService::refreshRate($current['default_currency']);

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    /** POST /admin/settings/reset */
    public function reset()
    {
        Cache::forget(self::CACHE_KEY);
        return back()->with('success', 'Paramètres réinitialisés aux valeurs par défaut.');
    }

    /** POST /admin/settings/clear-cache */
    public function clearCache()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du vidage du cache : ' . $e->getMessage());
        }
        return back()->with('success', 'Cache système vidé avec succès.');
    }

    /** GET /admin/settings/export */
    public function export()
    {
        $settings = Cache::get(self::CACHE_KEY, $this->defaults);
        $settings = array_merge($this->defaults, $settings);

        $export = [
            'exported_at' => now()->toIso8601String(),
            'settings'    => $settings,
        ];

        return response()->json($export, 200, [
            'Content-Disposition' => 'attachment; filename="settings-' . now()->format('Y-m-d') . '.json"',
        ]);
    }
}
