<?php

namespace Paymenter\Extensions\Others\CurrencyUpdater\Admin\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ExchangeRates extends Page implements HasForms
{
    use InteractsWithForms;
    public function getView(): string
    {
        return 'currency-updater::exchange-rates';
    }

    public function getTitle(): string
    {
        return 'Exchange Rates';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-currency-dollar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Exchange Rates';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public $rates = [];

    public function mount(): void
    {
        $this->ensureRatesTable();
        $this->seedRates();
        $this->loadRates();
    }

    public function loadRates(): void
    {
        $this->rates = $this->getRates()->map(function ($rate) {
            return [
                'code' => $rate->code,
                'rate' => (float) $rate->rate,
                'enabled' => (bool) $rate->enabled,
                'is_default' => (bool) $rate->is_default,
            ];
        })->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch')
                ->label('Fetch Latest Rates')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('fetchRates'),
            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('saveRates'),
            Action::make('apply')
                ->label('Apply to Products')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning')
                ->action('applyToProducts'),
        ];
    }

    public function fetchRates(): void
    {
        try {
            $controller = app(\Paymenter\Extensions\Others\CurrencyUpdater\src\Http\Controllers\ExchangeRatesController::class);
            $response = $controller->fetch(request());
            
            if ($response->getStatusCode() === 200) {
                // Reload the rates data
                $this->loadRates();
                
                Notification::make()
                    ->title('Success')
                    ->body('Exchange rates fetched successfully!')
                    ->success()
                    ->send();
            } else {
                throw new \Exception('Failed to fetch rates');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to fetch rates: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function saveRates(): void
    {
        try {
            // Save the current rates data
            DB::transaction(function () {
                foreach ($this->rates as $rate) {
                    DB::table('exchange_rates')->updateOrInsert(
                        ['code' => $rate['code']],
                        [
                            'rate' => (float) $rate['rate'],
                            'enabled' => (bool) $rate['enabled'],
                            'is_default' => (bool) $rate['is_default'],
                        ]
                    );
                }
            });
            
            Notification::make()
                ->title('Success')
                ->body('Exchange rates saved successfully!')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to save rates: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function updateRate($index, $field, $value): void
    {
        if (isset($this->rates[$index])) {
            $this->rates[$index][$field] = $value;
        }
    }

    public function setDefault($index): void
    {
        // Reset all defaults
        foreach ($this->rates as $i => $rate) {
            $this->rates[$i]['is_default'] = false;
        }
        // Set the selected one as default
        $this->rates[$index]['is_default'] = true;
    }

    public function applyToProducts(): void
    {
        try {
            $controller = app(\Paymenter\Extensions\Others\CurrencyUpdater\src\Http\Controllers\ExchangeRatesController::class);
            $response = $controller->applyToProducts(request());
            
            if ($response->getStatusCode() === 200) {
                Notification::make()
                    ->title('Success')
                    ->body('Exchange rates applied to products successfully!')
                    ->success()
                    ->send();
            } else {
                throw new \Exception('Failed to apply rates');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to apply rates: ' . $e->getMessage())
                ->danger()
                ->send();
        }
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

    protected function seedRates(): void
    {
        // Get currencies
        $codes = [];
        if (Schema::hasTable('currencies')) {
            $codes = DB::table('currencies')
                ->orderBy('code')
                ->pluck('code')
                ->map(fn ($c) => strtoupper($c))
                ->all();
        }
        if (!$codes) $codes = ['USD'];

        // Seed rows idempotently
        DB::beginTransaction();
        foreach ($codes as $code) {
            $existing = DB::table('exchange_rates')->where('code', $code)->first();
            DB::table('exchange_rates')->updateOrInsert(
                ['code' => $code],
                [
                    'rate' => $existing ? $existing->rate : 1.0,
                    'enabled' => $existing ? $existing->enabled : true
                ]
            );
        }
        if (!DB::table('exchange_rates')->where('is_default', true)->exists()) {
            DB::table('exchange_rates')->where('code', $codes[0])->update(['is_default' => true]);
        }
        DB::commit();
    }

    public function getRates()
    {
        // Get currencies
        $codes = [];
        if (Schema::hasTable('currencies')) {
            $codes = DB::table('currencies')
                ->orderBy('code')
                ->pluck('code')
                ->map(fn ($c) => strtoupper($c))
                ->all();
        }
        if (!$codes) $codes = ['USD'];

        // Get existing rates
        $existingRates = DB::table('exchange_rates')
            ->whereIn('code', $codes)
            ->get()
            ->keyBy('code');

        // Build result with all currencies
        $result = collect();
        foreach ($codes as $code) {
            $rate = $existingRates->get($code);
            $result->push((object) [
                'code' => $code,
                'rate' => $rate ? (float) $rate->rate : 1.0,
                'enabled' => $rate ? (bool) $rate->enabled : true,
                'is_default' => $rate ? (bool) $rate->is_default : false,
            ]);
        }

        return $result->sortByDesc('is_default')->sortBy('code');
    }
}
