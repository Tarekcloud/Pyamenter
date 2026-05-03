<?php

namespace Paymenter\Extensions\Others\CurrencyUpdater;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\CurrencyUpdater\src\Http\Controllers\ExchangeRatesController;

/**
 * CurrencyUpdater bootstrap
 *
 * - Runs migrations on enable()
 * - Adds Blade namespace
 * - Registers only POST action routes (Filament owns the GET page)
 */
#[ExtensionMeta(
    name: 'CurrencyUpdater',
    description: 'Fetch FX Rates Automatically.',
    version: '1.0',
    author: 'DigiDome'
)]
class CurrencyUpdater extends Extension
{
    public function installed(): void
    {
        // no-op
    }

    public function enabled(): void
    {
        Artisan::call('migrate', [
            '--path'  => 'extensions/Others/CurrencyUpdater/database/migrations',
            '--force' => true,
        ]);
    }

    public function disabled(): void
    {
        // keep audit/log tables
    }

    public function boot(): void
    {
        // Blade namespace for our views
        View::addNamespace('currency-updater', base_path('extensions/Others/CurrencyUpdater/resources/views'));

        // Register Filament page
        $this->app->afterResolving(\Filament\Panel::class, function (\Filament\Panel $panel) {
            if ($panel->getId() === 'admin') {
                $panel->pages([
                    \Paymenter\Extensions\Others\CurrencyUpdater\Admin\Pages\ExchangeRates::class,
                ]);
            }
        });

        // Register Artisan command
        $this->commands([
            \Paymenter\Extensions\Others\CurrencyUpdater\src\Console\UpdateCurrencyRates::class,
        ]);

        // API routes for AJAX operations
        Route::middleware(['web', 'auth', 'verified'])->group(function () {
            Route::post('admin/exchange-rates/fetch', [ExchangeRatesController::class, 'fetch'])->name('exchange-rates.fetch');
            Route::post('admin/exchange-rates/save',  [ExchangeRatesController::class, 'save'])->name('exchange-rates.save');
            Route::post('admin/exchange-rates/apply', [ExchangeRatesController::class, 'applyToProducts'])->name('exchange-rates.apply');
        });
    }

    public function getConfig($values = []): array
    {
        return [];
    }
}
