<?php

namespace Paymenter\Extensions\Others\Helpcenter;

use Exception;
use Livewire\Livewire;
use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

// Livewire components
use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\Index;
use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\Show;
use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\Widget;
use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\FAQIndex;
use Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\FAQShow;

// Models
use Paymenter\Extensions\Others\Helpcenter\Models\Article;
use Paymenter\Extensions\Others\Helpcenter\Models\Category;
use Paymenter\Extensions\Others\Helpcenter\Models\FAQ;
use Paymenter\Extensions\Others\Helpcenter\Models\Link;

// Policies
use Paymenter\Extensions\Others\Helpcenter\Policies\ArticlePolicy;
use Paymenter\Extensions\Others\Helpcenter\Policies\CategoryPolicy;
use Paymenter\Extensions\Others\Helpcenter\Policies\FAQPolicy;
use Paymenter\Extensions\Others\Helpcenter\Policies\LinkPolicy;

class Helpcenter extends Extension
{
    /**
     * Configuration for the extension
     */
    public function getConfig($values = [])
    {
        try {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString(
                        'Use this extension to create articles, FAQs, and helpful links to assist your customers.
                         <br><br><small><i>Need support? Join our Discord at <a href="https://discord.gg/DP3atuhnsB">https://discord.gg/DP3atuhnsB</a></small></i>'
                    ),
                ],
            ];
        } catch (Exception $e) {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString(
                        'You need to enable this extension before using it.
                         <br><br><small><i>Need support? Join our Discord at <a href="https://discord.gg/DP3atuhnsB">https://discord.gg/DP3atuhnsB</a></small></i>'
                    ),
                ],
            ];
        }
    }

    /**
     * Run migrations when extension is enabled
     */
    public function enabled()
    {
        Artisan::call('migrate', [
            '--path' => 'extensions/Others/Helpcenter/database/migrations',
            '--force' => true
        ]);
    }

    /**
     * Boot the extension
     */
    public function boot()
    {
        View::addNamespace('helpcenter', __DIR__ . '/resources/views');
        // Load routes
        require __DIR__ . '/routes/web.php';

        // Register Livewire components
        Livewire::component('helpcenter.index', Index::class);
        Livewire::component('helpcenter.show', Show::class);
        Livewire::component('helpcenter.widget', Widget::class);
        Livewire::component('helpcenter.faq.index', FAQIndex::class);
        Livewire::component('helpcenter.faq.show', FAQShow::class);

        // Optional: category component
        if (class_exists(\Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\Category::class)) {
            Livewire::component('helpcenter.category', \Paymenter\Extensions\Others\Helpcenter\Livewire\Helpcenter\Category::class);
        }

        // Register policies
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(FAQ::class, FAQPolicy::class);
        Gate::policy(Link::class, LinkPolicy::class);

        // Register permissions
        Event::listen('permissions', function () {
            return [
                'admin.helpcenter.articles.view' => 'View Helpcenter Articles',
                'admin.helpcenter.articles.create' => 'Create Helpcenter Articles',
                'admin.helpcenter.articles.update' => 'Update Helpcenter Articles',
                'admin.helpcenter.articles.delete' => 'Delete Helpcenter Articles',
                'admin.helpcenter.categories.view' => 'View Helpcenter Categories',
                'admin.helpcenter.categories.create' => 'Create Helpcenter Categories',
                'admin.helpcenter.categories.update' => 'Update Helpcenter Categories',
                'admin.helpcenter.categories.delete' => 'Delete Helpcenter Categories',
                'admin.helpcenter.faqs.view' => 'View Helpcenter FAQs',
                'admin.helpcenter.faqs.create' => 'Create Helpcenter FAQs',
                'admin.helpcenter.faqs.update' => 'Update Helpcenter FAQs',
                'admin.helpcenter.faqs.delete' => 'Delete Helpcenter FAQs',
                'admin.helpcenter.links.view' => 'View Helpcenter Links',
                'admin.helpcenter.links.create' => 'Create Helpcenter Links',
                'admin.helpcenter.links.update' => 'Update Helpcenter Links',
                'admin.helpcenter.links.delete' => 'Delete Helpcenter Links',
            ];
        });

        Event::listen('api.permissions', function () {
            return [
                'admin.helpcenter.articles.view' => 'View Helpcenter Articles',
                'admin.helpcenter.articles.create' => 'Create Helpcenter Articles',
                'admin.helpcenter.articles.update' => 'Update Helpcenter Articles',
                'admin.helpcenter.articles.delete' => 'Delete Helpcenter Articles',
                'admin.helpcenter.categories.view' => 'View Helpcenter Categories',
                'admin.helpcenter.categories.create' => 'Create Helpcenter Categories',
                'admin.helpcenter.categories.update' => 'Update Helpcenter Categories',
                'admin.helpcenter.categories.delete' => 'Delete Helpcenter Categories',
                'admin.helpcenter.faqs.view' => 'View Helpcenter FAQs',
                'admin.helpcenter.faqs.create' => 'Create Helpcenter FAQs',
                'admin.helpcenter.faqs.update' => 'Update Helpcenter FAQs',
                'admin.helpcenter.faqs.delete' => 'Delete Helpcenter FAQs',
                'admin.helpcenter.links.view' => 'View Helpcenter Links',
                'admin.helpcenter.links.create' => 'Create Helpcenter Links',
                'admin.helpcenter.links.update' => 'Update Helpcenter Links',
                'admin.helpcenter.links.delete' => 'Delete Helpcenter Links',
            ];
        });

        // Admin navigation
Event::listen('navigation', function () {
    return [
        [
            'name' => 'Helpcenter',
            'route' => 'helpcenter.index',
            'icon' => 'ri-book-open',
            'separator' => true,
            'children' => [],
        ]
    ];
});

    }
}
