<?php

namespace Paymenter\Extensions\Others\Statuspage;

use Exception;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Statuspage\Livewire\Index;
use Paymenter\Extensions\Others\Statuspage\Livewire\Show;
use Paymenter\Extensions\Others\Statuspage\Livewire\Widget;
use App\Classes\Extension\Extension;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MonitorResource;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;
use Paymenter\Extensions\Others\Statuspage\Models\Incident;
use Paymenter\Extensions\Others\Statuspage\StatusPageServiceProvider;

class Statuspage extends Extension
{
    protected static bool $commandRegistered = false;
    public function getConfig($values = [])
    {
        try {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString(
                        'Use this extension to create a statuspage on your Paymenter website. 
                         Manage your monitors and incidents at <a class="text-primary-600" href="' . MonitorResource::getUrl() . '">Monitors</a>.
                         <br><br><small><i>Need support? Join our Discord: <a href="https://discord.gg/DP3atuhnsB">https://discord.gg/DP3atuhnsB</a></small></i>'
                    ),
                ],
            ];
        } catch (Exception $e) {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString(
                        'Use this extension to create a statuspage on your Paymenter website. 
                         You need to enable it before you can use it.
                         <br><br><small><i>Need support? Join our Discord: <a href="https://discord.gg/DP3atuhnsB">https://discord.gg/DP3atuhnsB</a></small></i>'
                    ),
                ],
            ];
        }
    }

    public function enabled()
    {
        Artisan::call('migrate', [
            '--path' => 'extensions/Others/Statuspage/database/migrations',
            '--force' => true
        ]);
    }

    public function boot()
    {
        $this->registerConsoleCommands();
        $this->registerSchedule();
        
        require __DIR__ . '/routes/web.php';
        View::addNamespace('statuspage', __DIR__ . '/resources/views');

        app()->register(StatusPageServiceProvider::class);

        Livewire::component('statuspage.index', Index::class);
        Livewire::component('statuspage.show', Show::class);
        Livewire::component('statuspage.widget', Widget::class);

        if (class_exists(\Filament\Filament::class)) {
            \Filament\Filament::registerResources([
                MaintenanceResource::class,
            ]);
            
            \Filament\Filament::registerPages([
                \Paymenter\Extensions\Others\Statuspage\Admin\Pages\StatusPageSettings::class,
                \Paymenter\Extensions\Others\Statuspage\Admin\Pages\CategorySort::class,
            ]);
        }

        Event::listen('navigation', function () {
            return [
                'name' => 'Status',
                'route' => 'statuspage.index',
                'icon' => 'ri-pulse',
                'separator' => true,
                'children' => [],
            ];
        });
    }

    private function registerConsoleCommands(): void
    {
        if (!app()->runningInConsole()) {
            return;
        }

        if (self::$commandRegistered) {
            return;
        }

        self::$commandRegistered = true;

        Event::listen(ArtisanStarting::class, function (ArtisanStarting $event): void {
            $event->artisan->resolveCommands([
                Commands\CheckMonitorsCommand::class,
            ]);
        });
    }

    private function registerSchedule(): void
    {
        if (!app()->runningInConsole()) {
            return;
        }

        try {
            $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
            $schedule->command('statuspage:check')
                ->everyMinute()
                ->withoutOverlapping()
                ->onFailure(function () {
                    \Illuminate\Support\Facades\Log::error('StatusPage: Scheduled monitor check failed');
                })
                ->onSuccess(function () {
                    \Illuminate\Support\Facades\Log::debug('StatusPage: Scheduled monitor check completed successfully');
                });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('StatusPage: Error registering scheduled monitor check', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
