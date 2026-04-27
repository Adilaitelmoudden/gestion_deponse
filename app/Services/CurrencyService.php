<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    // Base currency stored in DB
    const BASE = 'MAD';

    // Cache exchange rates for 6 hours
    const CACHE_TTL = 21600;

    // Fallback rates (MAD → X) if API fails
    const FALLBACK_RATES = [
        'MAD' => 1.0,
        'EUR' => 0.092,
        'USD' => 0.100,
        'GBP' => 0.079,
        'CAD' => 0.136,
        'CHF' => 0.090,
        'SAR' => 0.375,
        'AED' => 0.367,
    ];

    /**
     * Get the admin-selected currency code.
     */
    public static function getSelectedCurrency(): string
    {
        $settings = Cache::get('admin_system_settings', []);
        return $settings['default_currency'] ?? self::BASE;
    }

    /**
     * Get exchange rate: MAD → $targetCurrency
     */
    public static function getRate(string $targetCurrency): float
    {
        if ($targetCurrency === self::BASE) {
            return 1.0;
        }

        $cacheKey = 'exchange_rate_MAD_' . $targetCurrency;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($targetCurrency) {
            try {
                // Free API — no key needed, MAD base
                $response = Http::timeout(5)->get(
                    "https://api.exchangerate-api.com/v4/latest/MAD"
                );

                if ($response->successful()) {
                    $rates = $response->json('rates', []);
                    if (isset($rates[$targetCurrency])) {
                        return (float) $rates[$targetCurrency];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('CurrencyService: API failed, using fallback. ' . $e->getMessage());
            }

            // Use fallback rate
            return self::FALLBACK_RATES[$targetCurrency] ?? 1.0;
        });
    }

    /**
     * Convert an amount from MAD to the selected currency.
     */
    public static function convert(float $amount): float
    {
        $currency = self::getSelectedCurrency();
        $rate     = self::getRate($currency);
        return round($amount * $rate, 2);
    }

    /**
     * Convert and format: "1 234,56 EUR"
     */
    public static function format(float $amount): string
    {
        $currency    = self::getSelectedCurrency();
        $converted   = self::convert($amount);
        return number_format($converted, 2, ',', ' ') . ' ' . $currency;
    }

    /**
     * Get current rate info for display (used in settings preview).
     */
    public static function getRateInfo(): array
    {
        $currency = self::getSelectedCurrency();
        $rate     = self::getRate($currency);
        return [
            'from'     => self::BASE,
            'to'       => $currency,
            'rate'     => $rate,
            'example'  => number_format(1000 * $rate, 2, ',', ' ') . ' ' . $currency,
        ];
    }

    /**
     * Force-refresh rate from API (called when admin changes currency).
     */
    public static function refreshRate(string $currency): void
    {
        Cache::forget('exchange_rate_MAD_' . $currency);
        self::getRate($currency);
    }
}
