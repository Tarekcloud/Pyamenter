@php
    $currentRoute = request()->livewireUrl();

    $navigation = [
        \App\Classes\Navigation::getLinks(),
        \App\Classes\Navigation::getAccountDropdownLinks(),
        \App\Classes\Navigation::getDashboardLinks(),
    ];

    if (!function_exists('findBreadcrumb')) {
        function findBreadcrumb($items, $currentRoute) {
            foreach ($items as $item) {
                if (isset($item['url']) && $item['url'] === $currentRoute) {
                    return [$item];
                }

                if (!empty($item['children'])) {
                    $childTrail = findBreadcrumb($item['children'], $currentRoute);
                    if (!empty($childTrail)) {
                        return array_merge([$item], $childTrail);
                    }
                }
            }
            return [];
        }
    }

    $breadcrumbs = [];
    foreach ($navigation as $group) {
        $found = findBreadcrumb($group, $currentRoute);
        if (!empty($found)) {
            $breadcrumbs = $found;
            break;
        }
    }
    
    if(empty($breadcrumbs) && route('home') === $currentRoute) {
         $breadcrumbs[] = ['name' => 'Dashboard', 'url' => route('home'), 'icon' => 'ri-home-3-line'];
    }
@endphp

<nav class="flex mb-5" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">

        @foreach ($breadcrumbs as $index => $breadcrumb)
            
            @if ($index > 0)
                <li>
                    <x-ri-arrow-right-s-line class="size-4 text-base/30 mx-1" />
                </li>
            @endif

            <li>
                <div class="flex items-center">
                    @if ($index === count($breadcrumbs) - 1)
                        <span class="inline-flex items-center px-2 py-1 text-sm font-bold text-foreground rounded-md bg-neutral/10 cursor-default">
                            @if(isset($breadcrumb['icon']))
                                <x-dynamic-component :component="$breadcrumb['icon']" class="size-3.5 mr-2" />
                            @elseif($index === 0)
                                <x-ri-home-3-line class="size-3.5 mr-2" />
                            @endif
                            {{ $breadcrumb['name'] }}
                        </span>
                    @else
                        <a href="{{ isset($breadcrumb['route']) ? route($breadcrumb['route'], $breadcrumb['params'] ?? []) : ($breadcrumb['url'] ?? '#') }}" 
                           class="inline-flex items-center px-2 py-1 text-sm font-medium text-base/60 rounded-md hover:text-primary hover:bg-neutral/50 transition-colors duration-200">
                            
                            @if(isset($breadcrumb['icon']))
                                <x-dynamic-component :component="$breadcrumb['icon']" class="size-3.5 mr-2" />
                            @elseif($index === 0)
                                <x-ri-home-3-line class="size-3.5 mr-2" />
                            @endif

                            {{ $breadcrumb['name'] }}
                        </a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>