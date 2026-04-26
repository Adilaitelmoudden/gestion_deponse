<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    private const CACHE_KEY = 'admin_system_settings';

    private array $defaults = [
        'app_name'              => 'Gestion des Dépenses',
        'welcome_message'       => 'Bienvenue sur votre espace de gestion financière.',
        'maintenance_mode'      => false,
        'maintenance_message'   => 'Le site est en maintenance. Merci de revenir plus tard.',
        'allow_registration'    => true,
        'max_transactions_user' => 1000,
        'default_currency'      => 'MAD',
        'contact_email'         => 'admin@example.com',
    ];

    /** GET /admin/settings */
    public function index()
    {
        $settings = Cache::get(self::CACHE_KEY, $this->defaults);
        // Ensure all default keys exist in case new ones were added
        $settings = array_merge($this->defaults, $settings);
        return view('admin.settings.index', compact('settings'));
    }

    /** PUT /admin/settings */
    public function update(Request $request)
    {
        $request->validate([
            'app_name'              => 'required|string|max:100',
            'welcome_message'       => 'nullable|string|max:500',
            'maintenance_message'   => 'nullable|string|max:500',
            'max_transactions_user' => 'required|integer|min:1|max:99999',
            'default_currency'      => 'required|string|max:10',
            'contact_email'         => 'required|email|max:100',
        ]);

        $settings = [
            'app_name'              => $request->input('app_name'),
            'welcome_message'       => $request->input('welcome_message', ''),
            'maintenance_mode'      => $request->boolean('maintenance_mode'),
            'maintenance_message'   => $request->input('maintenance_message', ''),
            'allow_registration'    => $request->boolean('allow_registration'),
            'max_transactions_user' => (int) $request->input('max_transactions_user'),
            'default_currency'      => $request->input('default_currency'),
            'contact_email'         => $request->input('contact_email'),
        ];

        Cache::forever(self::CACHE_KEY, $settings);

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    /** POST /admin/settings/reset */
    public function reset()
    {
        Cache::forget(self::CACHE_KEY);
        return back()->with('success', 'Paramètres réinitialisés aux valeurs par défaut.');
    }
}
