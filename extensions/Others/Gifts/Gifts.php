<?php

namespace Paymenter\Extensions\Others\Gifts;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Extension;
use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource;
use Paymenter\Extensions\Others\Gifts\Livewire\Gifts\Redeem;
use Paymenter\Extensions\Others\Gifts\Models\Gift;
use Paymenter\Extensions\Others\Gifts\Policies\GiftPolicy;

#[ExtensionMeta(
    name: 'Gifts',
    description: 'Redeem gift codes to get services, credits, discounts, and more',
    version: '1.0.0',
    author: 'BuiltByOtte',
    url: ''
)]
class Gifts extends Extension
{
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'Notice',
                'type' => 'placeholder',
                'label' => new \Illuminate\Support\HtmlString(
                    'Create and manage gift codes that can be redeemed for coupons, credits, services, or discounts.
                     <br><br><small><i>Users can redeem codes from their dashboard or via direct links.</small></i>'
                ),
            ],
        ];
    }

    public function enabled()
    {
        Artisan::call('migrate', [
            '--path' => 'extensions/Others/Gifts/database/migrations',
            '--force' => true
        ]);
    }

    public function uninstalled()
    {
        ExtensionHelper::rollbackMigrations(__DIR__ . '/database/migrations');
    }

    public function boot()
    {
        View::addNamespace('gifts', __DIR__ . '/resources/views');
        
        Livewire::component('paymenter.extensions.others.gifts.livewire.gifts.redeem', Redeem::class);
        Livewire::component('gifts.redeem', Redeem::class);
        Livewire::component('paymenter.extensions.others.gifts.livewire.gifts.redeem-direct', \Paymenter\Extensions\Others\Gifts\Livewire\Gifts\RedeemDirect::class);
        
        require __DIR__ . '/routes/web.php';

        Gate::policy(Gift::class, GiftPolicy::class);

        Event::listen('permissions', function () {
            return [
                'admin.gifts.view' => 'View Gifts',
                'admin.gifts.create' => 'Create Gifts',
                'admin.gifts.update' => 'Update Gifts',
                'admin.gifts.delete' => 'Delete Gifts',
            ];
        });

        Event::listen('api.permissions', function () {
            return [
                'admin.gifts.view' => 'View Gifts',
                'admin.gifts.create' => 'Create Gifts',
                'admin.gifts.update' => 'Update Gifts',
                'admin.gifts.delete' => 'Delete Gifts',
            ];
        });

        Event::listen('navigation.account', function () {
            return [
                'name' => 'Redeem Gift Code',
                'route' => 'gifts.redeem',
                'icon' => 'ri-gift',
                'priority' => 50,
            ];
        });

        if (class_exists(\Filament\Filament::class)) {
            \Filament\Filament::registerResources([
                GiftResource::class,
            ]);
        }
    }
}
