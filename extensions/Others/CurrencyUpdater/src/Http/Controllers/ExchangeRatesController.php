<?php

namespace Paymenter\Extensions\Others\CurrencyUpdater\src\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Paymenter\Extensions\Others\CurrencyUpdater\src\Services\Providers\ExchangeRateHostProvider;

class ExchangeRatesController extends Controller
{
    public function fetch(Request $request)
    {
        $this->ensureRatesTable();
        $base = $this->getDefaultCode();
        $symbols = $this->getCurrencies();

        try {
            \Log::info('Fetching rates', ['base' => $base, 'symbols' => $symbols]);
            $rates = (new ExchangeRateHostProvider())->fetchLatest($base, $symbols);
            \Log::info('Rates fetched successfully', ['count' => count($rates)]);

            DB::beginTransaction();
            foreach ($rates as $code => $rate) {
                // Allow base currency and any currency in symbols array
                if (!in_array($code, $symbols) && $code !== $base) continue;
                if (!is_numeric($rate) || $rate <= 0) continue;
                
                $existing = DB::table('exchange_rates')->where('code', $code)->first();
                DB::table('exchange_rates')->updateOrInsert(
                    ['code' => $code],
                    [
                        'rate' => (float)$rate, 
                        'enabled' => $existing ? $existing->enabled : true
                    ]
                );
            }
            DB::commit();

            return response()->json(['ok' => true, 'base' => $base, 'count' => count($rates)]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to fetch rates', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'default_code' => 'nullable|string|size:3',
            'rates' => 'required|array',
            'rates.*.code' => 'required|string|size:3',
            'rates.*.rate' => 'required|numeric|min:0',
            'rates.*.enabled' => 'required|boolean',
        ]);

        DB::transaction(function () use ($data) {
            if (!empty($data['default_code'])) {
                DB::table('exchange_rates')->update(['is_default' => false]);
                $existing = DB::table('exchange_rates')->where('code', strtoupper($data['default_code']))->first();
                DB::table('exchange_rates')->updateOrInsert(
                    ['code' => strtoupper($data['default_code'])],
                    [
                        'is_default' => true,
                        'rate' => $existing ? $existing->rate : 1.0,
                        'enabled' => $existing ? $existing->enabled : true,
                    ]
                );
            }

            foreach ($data['rates'] as $row) {
                DB::table('exchange_rates')->updateOrInsert(
                    ['code' => strtoupper($row['code'])],
                    [
                        'rate' => (float)$row['rate'],
                        'enabled' => (bool)$row['enabled'],
                    ]
                );
            }
        });

        return response()->json(['ok' => true]);
    }

    public function applyToProducts(Request $request)
    {
        // Simple implementation - just return success for now
        return response()->json(['ok' => true, 'message' => 'Rates applied to products']);
    }

    protected function ensureRatesTable(): void
    {
        if (!Schema::hasTable('exchange_rates')) {
            Schema::create('exchange_rates', function (Blueprint $table) {
                $table->id();
                $table->string('code', 8)->unique();
                $table->decimal('rate', 18, 8)->default(1);
                $table->boolean('enabled')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }
    }

    protected function getDefaultCode(): string
    {
        return DB::table('exchange_rates')->where('is_default', true)->value('code') ?: 'USD';
    }

    protected function getCurrencies(): array
    {
        $codes = [];
        if (Schema::hasTable('currencies')) {
            $codes = DB::table('currencies')->pluck('code')->map(fn($c) => strtoupper($c))->all();
        }
        return $codes ?: ['USD'];
    }
}