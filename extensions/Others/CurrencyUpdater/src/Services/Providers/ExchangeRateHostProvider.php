<?php

namespace Paymenter\Extensions\Others\CurrencyUpdater\src\Services\Providers;

use Illuminate\Support\Facades\Http;

class ExchangeRateHostProvider
{
    public function fetchLatest(string $base, array $symbols = []): array
    {
        $base = strtoupper($base);
        
        // Fallback rates if API fails
        $fallbackRates = [
            'USD' => 1.0,
            'EUR' => 0.85,
            'GBP' => 0.74,
            'JPY' => 148.0,
            'AUD' => 1.51,
            'CAD' => 1.38,
            'CHF' => 0.80,
            'CNY' => 7.12,
            'LKR' => 325.0,
            'INR' => 88.0,
        ];
        
        try {
            // Use frankfurter.app (more reliable)
            $params = ['from' => $base];
            if (!empty($symbols)) {
                $params['to'] = implode(',', array_unique(array_map('strtoupper', $symbols)));
            }
            
            \Log::info('Making API request', ['url' => 'https://api.frankfurter.app/latest', 'params' => $params]);
            
            $response = Http::timeout(5)->get('https://api.frankfurter.app/latest', $params);
            
            \Log::info('API response', ['status' => $response->status(), 'body' => substr($response->body(), 0, 200)]);
            
            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && !empty($data['rates'])) {
                    $rates = [];
                    foreach ($data['rates'] as $code => $rate) {
                        if (is_numeric($rate) && $rate > 0) {
                            $rates[strtoupper($code)] = (float)$rate;
                        }
                    }
                    $rates[$base] = 1.0;
                    \Log::info('API fetch successful', ['count' => count($rates)]);
                    return $rates;
                }
            }
            
            \Log::warning('API failed, using fallback rates', ['status' => $response->status()]);
            
        } catch (\Exception $e) {
            \Log::warning('API request failed, using fallback rates', ['error' => $e->getMessage()]);
        }
        
        // Return fallback rates
        $rates = [];
        foreach ($symbols as $symbol) {
            $symbol = strtoupper($symbol);
            if (isset($fallbackRates[$symbol])) {
                $rates[$symbol] = $fallbackRates[$symbol];
            }
        }
        // Always include base currency
        $rates[$base] = 1.0;
        
        \Log::info('Using fallback rates', ['count' => count($rates)]);
        return $rates;
    }
}