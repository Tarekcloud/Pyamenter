<?php

namespace Paymenter\Extensions\Others\Pages;

use App\Attributes\ExtensionMeta;
use App\Helpers\ExtensionHelper;
use Exception;
use Livewire\Livewire;
use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource;
use Paymenter\Extensions\Others\Pages\Livewire\Page;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Paymenter\Extensions\Others\Pages\Models\Page as ModelsPage;

#[ExtensionMeta(
    name: 'Pages',
    description: 'Setup custom pages on your site!',
    version: '1.2.0',
    author: 'Paymenter',
    url: 'https://builtbybit.com/resources/paymenter-pages.65119'
)]
class Pages extends Extension
{
    /**
     * Get all the configuration for the extension
     * 
     * @param array $values
     * @return array
     */
    public function getConfig($values = [])
    {
        try {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('You can use this extension to create custom frontend pages, go to <a class="text-primary-600" href="' . PageResource::getUrl() . '">Pages</a> to get started.'),
                ],
            ];
        } catch (Exception $e) {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('You can use this extension to create custom frontend pages, you\'ll need to enable this extension above to get started.'),
                ],
            ];
        }
    }

    public function installed()
    {
        ExtensionHelper::runMigrations(path: 'extensions/Others/Pages/database/migrations');
    }

    public function uninstalled()
    {
        // Rollback migrations
        ExtensionHelper::rollbackMigrations(path: 'extensions/Others/Pages/database/migrations');
    }

    public function upgraded($oldVersion = null)
    {
        $this->installed();
    }

    public function boot()
    {
        // Register routes
        require __DIR__ . '/routes.php';
        View::addNamespace('others.pages', __DIR__ . '/resources/views');

        // Register livewire
        Livewire::component('extensions.others.pages.show', Page::class);

        Event::listen('navigation', function () {
            if (ModelsPage::where('visible', true)->where('navigation', 'top')->count() == 0) {
                return;
            }

            $pages = ModelsPage::where('visible', true)->where('navigation', 'top')->orderBy('sort')->get();

            return $this->toNavigation($pages);
        });

        Event::listen('navigation.account-dropdown', function () {
            if (ModelsPage::where('visible', true)->where('navigation', 'account_dropdown')->count() == 0) {
                return;
            }

            $pages = ModelsPage::where('visible', true)->where('navigation', 'account_dropdown')->orderBy('sort')->get();

            return $this->toNavigation($pages);
        });

        Event::listen('navigation.dashboard', function () {
            if (ModelsPage::where('visible', true)->where('navigation', 'dashboard')->count() == 0) {
                return;
            }

            $pages = ModelsPage::where('visible', true)->where('navigation', 'dashboard')->orderBy('sort')->get();

            return $this->toNavigation($pages);
        });
    }

    private function toNavigation($pages)
    {
        $navigation = [];
        foreach ($pages as $page) {
            if ($page->visibility == 'client' && !auth()->check()) {
                continue;
            }
            if ($page->visibility == 'admin' && (!auth()->check() || is_null(auth()->user()->role))) {
                continue;
            }
            $navigation[] = [
                'name' => $page->title,
                'route' => 'extensions.others.pages',
                'params' => ['fallbackPlaceholder' => $page->slug],
            ];
        }
        return $navigation;
    }
}
