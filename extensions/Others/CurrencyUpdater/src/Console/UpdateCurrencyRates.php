<?php

namespace Paymenter\Extensions\Others\CurrencyUpdater\src\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Paymenter\Extensions\Others\CurrencyUpdater\src\Services\Providers\ExchangeRateHostProvider;

class UpdateCurrencyRates extends Command
{
    protected $signature = 'currency:update {--base=}';
    protected $description = 'Fetch and store latest exchange rates';

    public function handle(): int
    {
        $base = $this->option('base') ?: DB::table('exchange_rates')->where('is_default', true)->value('code') ?: 'USD';
        $this->info("Fetching rates (base={$base}) ...");

        try {
            $provider = new ExchangeRateHostProvider();
            $rates    = $provider->fetchLatest($base);

            DB::beginTransaction();
            foreach ($rates as $code => $rate) {
                $existing = DB::table('exchange_rates')->where('code', $code)->first();
                DB::table('exchange_rates')->updateOrInsert(
                    ['code' => $code],
                    [
                        'rate' => $rate, 
                        'enabled' => $existing ? $existing->enabled : true
                    ]
                );
            }
            DB::commit();
            $this->info('Done: ' . count($rates) . ' rates updated.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[currency:update] failed', ['e' => $e->getMessage()]);
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
