<div class="lg:px-4 lg:py-6 flex flex-col gap-2">
    <div class="flex flex-col gap-2 md:hidden">
        @foreach (\App\Classes\Navigation::getLinks() as $nav)
            @if (!empty($nav['children']))
                <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
                    class="relative w-full overflow-hidden">
                    <button @click="activeAccordion = !activeAccordion"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 group
                            {{ $nav['active'] ? 'text-primary bg-gradient-to-r from-primary/10 to-secondary/10 border border-primary/30 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-neutral/30' }}">
                        <div class="flex items-center gap-3">
                            @isset($nav['icon'])
                                <x-dynamic-component :component="$nav['icon']"
                                    class="size-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                            @endisset
                            <span>{{ $nav['name'] }}</span>
                        </div>
                        <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                            class="size-5 text-base/60 transition-transform duration-300" />
                    </button>
                    <div x-show="activeAccordion" 
                         x-collapse 
                         x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="py-2 pl-8 pr-4 mt-2 border-l-2 border-primary/30 ml-4">
                            @foreach ($nav['children'] as $child)
                                <x-navigation.link :href="$child['url']"
                                    :spa="$child['spa'] ?? true"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium mb-1 transition-all duration-200 group hover:translate-x-1
                                        {{ $child['active'] ? 'text-primary bg-primary/10 border border-primary/20' : 'text-base/70 hover:text-foreground hover:bg-background/50' }}">
                                    {{ $child['name'] }}
                                    @if($child['active'])
                                        <div class="ml-auto w-2 h-2 bg-primary rounded-full"></div>
                                    @endif
                                </x-navigation.link>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <x-navigation.link
                    :href="$nav['url']"
                    :spa="$nav['spa'] ?? true"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 group
                        {{ $nav['active'] ? 'text-primary bg-gradient-to-r from-primary/10 to-secondary/10 border border-primary/30 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-neutral/30' }}">
                    @isset($nav['icon'])
                        <x-dynamic-component :component="$nav['icon']"
                            class="size-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                    @endisset
                    <span>{{ $nav['name'] }}</span>
                    @if($nav['active'])
                        <div class="ml-auto w-2 h-2 bg-primary rounded-full"></div>
                    @endif
                </x-navigation.link>
            @endif
            @isset($nav['separator'])
                <div class="h-px w-full bg-neutral/30 my-3"></div>
            @endisset
        @endforeach
    </div>

    <div class="space-y-2">
        @foreach (\App\Classes\Navigation::getDashboardLinks() as $nav)
            @if (!empty($nav['children']))
                <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
                    class="relative w-full overflow-hidden">
                    <button @click="activeAccordion = !activeAccordion"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 group
                            {{ $nav['active'] ? 'text-primary bg-gradient-to-r from-primary/10 to-secondary/10 border border-primary/30 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-neutral/30' }}">
                        <div class="flex items-center gap-3">
                            @isset($nav['icon'])
                                <x-dynamic-component :component="$nav['icon']"
                                    class="size-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                            @endisset
                            <span>{{ $nav['name'] }}</span>
                        </div>
                        <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                            class="size-5 text-base/60 transition-transform duration-300" />
                    </button>
                    <div x-show="activeAccordion" 
                         x-collapse 
                         x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="py-2 pl-8 pr-4 mt-2 border-l-2 border-primary/30 ml-4">
                            @foreach ($nav['children'] as $child)
                                @if ($child['condition'] ?? true)
                                    <x-navigation.link :href="$child['url']"
                                        :spa="$child['spa'] ?? true"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium mb-1 transition-all duration-200 group hover:translate-x-1
                                            {{ $child['active'] ? 'text-primary bg-primary/10 border border-primary/20' : 'text-base/70 hover:text-foreground hover:bg-background/50' }}">
                                        {{ $child['name'] }}
                                        @if($child['active'])
                                            <div class="ml-auto w-2 h-2 bg-primary rounded-full"></div>
                                        @endif
                                    </x-navigation.link>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <x-navigation.link
                    :href="$nav['url']"
                    :spa="$nav['spa'] ?? true"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 group
                        {{ $nav['active'] ? 'text-primary bg-gradient-to-r from-primary/10 to-secondary/10 border border-primary/30 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-neutral/30' }}">
                    @isset($nav['icon'])
                        <x-dynamic-component :component="$nav['icon']"
                            class="size-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                    @endisset
                    <span>{{ $nav['name'] }}</span>
                    @if($nav['active'])
                        <div class="ml-auto w-2 h-2 bg-primary rounded-full"></div>
                    @endif
                </x-navigation.link>
            @endif
            @isset($nav['separator'])
                <div class="h-px w-full bg-neutral/30 my-3"></div>
            @endisset
        @endforeach
        
        <div class="mt-6 pt-4 border-t border-neutral/50">
            <div class="flex flex-row items-center mt-4 justify-between md:hidden">
            <x-dropdown>
                <x-slot:trigger>
                    <div class="flex flex-col">
                        <span class="text-sm text-base font-semibold text-nowrap">{{ strtoupper(app()->getLocale()) }} <span class="text-base/50 font-semibold">|</span> {{ session('currency', config('settings.default_currency')) }}</span>
                    </div>
                </x-slot:trigger>
                <x-slot:content>
                    <strong class="block p-2 text-xs font-semibold uppercase text-base/50"> Language </strong>
                    <livewire:components.language-switch />
                    <livewire:components.currency-switch />
                </x-slot:content>
            </x-dropdown>

            <x-theme-toggle />

        </div>
    </div>
</div>