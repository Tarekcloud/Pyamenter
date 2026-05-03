<?php

namespace Paymenter\Extensions\Others\Statuspage;

use Illuminate\Support\ServiceProvider;
use Paymenter\Extensions\Others\Statuspage\Commands\CheckMonitorsCommand;

class StatusPageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            CheckMonitorsCommand::class,
        ]);
    }

    public function boot(): void
    {
        // Schedule is now registered directly in Statuspage::boot()
    }
}
