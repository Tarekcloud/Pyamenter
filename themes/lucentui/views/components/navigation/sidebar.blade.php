<nav class="w-full px-4 lg:px-8 fixed top-4 z-20 left-1/2 transform -translate-x-1/2"
     x-data="{ 
         slideOverOpen: false,
         hasAside: !!document.getElementById('main-aside'),
         scrolled: window.scrollY > 10 
     }"
     x-init="$watch('slideOverOpen', value => { document.documentElement.style.overflow = value ? 'hidden' : '' })"
     @scroll.window.throttle.100ms="scrolled = (window.scrollY > 10)">
     
    <div class="rounded-xl transition-all duration-300"
         :class="scrolled ? 'bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-xl' : 'bg-transparent border border-transparent shadow-none'">
         
        <div class="relative z-50 w-full h-auto">

            @if (theme('navbar_layout', 'lucent-left') === 'lucent-centered')

                <div class="flex flex-row items-center justify-between w-full h-16 px-6">
                    <div class="flex items-center gap-6 flex-1">
                        @if (theme('show_full_logo', true))
                            <a href="{{ route('home') }}" class="flex flex-col justify-center h-10 py-1 gap-2 group" wire:navigate>
                                <x-logo class="h-10 w-auto my-1 group-hover:scale-105 transition-transform duration-300" />
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="flex items-center h-10 gap-2 group" wire:navigate>
                                <x-logo class="h-10 w-auto group-hover:scale-105 transition-transform duration-300" />
                                <span class="text-xl font-bold leading-none tracking-tight flex items-center group-hover:text-primary transition-colors duration-300">{{ config('app.name') }}</span>
                            </a>
                        @endif
                    </div>

                    <div class="hidden md:flex flex-row gap-2 flex-1 justify-center">
                        @foreach (\App\Classes\Navigation::getLinks() as $nav)
                            @if (isset($nav['children']) && count($nav['children']) > 0)
                                <div class="relative">
                                    <x-dropdown>
                                        <x-slot:trigger>
                                            <span class="flex flex-row items-center p-3 text-sm font-semibold whitespace-nowrap text-base hover:text-primary transition-all duration-200 hover:bg-neutral/30 rounded-lg cursor-pointer">{{ $nav['name'] }}</span>
                                        </x-slot:trigger>
                                        <x-slot:content>
                                            @foreach ($nav['children'] as $child)
                                                <x-navigation.link
                                                    :href="$child['url']"
                                                    :spa="isset($child['spa']) ? $nav['spa'] : true">
                                                    {{ $child['name'] }}
                                                </x-navigation.link>
                                            @endforeach
                                        </x-slot:content>
                                    </x-dropdown>
                                </div>
                            @else
                                <x-navigation.link
                                    :href="$nav['url']"
                                    :spa="isset($nav['spa']) ? $nav['spa'] : true"
                                    class="flex items-center hover:bg-neutral/30 rounded-xl transition-all duration-200 hover:scale-105">
                                    {{ $nav['name'] }}
                                </x-navigation.link>
                            @endif
                        @endforeach
                    </div>

                    <div class="flex items-center flex-1 justify-end">
                        <div class="hidden md:flex items-center gap-2">

                            <x-dropdown>
                                <x-slot:trigger>
                                    <div class="px-3 py-2 hover:bg-neutral/30 rounded-lg transition-all duration-200 cursor-pointer group">
                                        <img 
                                            src="{{ asset('assets/flags/language-' . app()->getLocale() . '.svg') }}" 
                                            alt="{{ app()->getLocale() }}" 
                                            class="inline-block w-5 h-5 rounded-sm mr-2 object-cover" 
                                        >
                                        @if(theme('show_language_currency', false))
                                            <span class="text-sm font-semibold group-hover:text-primary transition-colors duration-200">{{ strtoupper(app()->getLocale()) }} | {{ session('currency', config('settings.default_currency')) }}</span>
                                        @else
                                            <span class="text-sm font-semibold group-hover:text-primary transition-colors duration-200">{{ strtoupper(app()->getLocale()) }}</span>
                                        @endif
                                    </div>
                                </x-slot:trigger>
                                <x-slot:content>
                                    <livewire:components.language-switch />
                                    <livewire:components.currency-switch />
                                </x-slot:content>
                            </x-dropdown>
                            <div class="hover:scale-110 transition-transform duration-200">
                                <x-theme-toggle />
                            </div>
                        </div>

                        <div class="hover:scale-110 transition-transform duration-200">
                            <livewire:components.cart />
                        </div>

                        <livewire:components.notifications />
                        <div class="relative hidden md:block" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center gap-3 p-2 rounded-xl transition-all duration-300 group hover:bg-gradient-to-r hover:from-primary/5 hover:to-secondary/5 hover:scale-105">
                                <div class="hidden lg:block text-left">
                                    <div class="text-sm font-bold group-hover:text-primary transition-colors duration-200">
                                        {{ \Illuminate\Support\Str::limit(Auth::user()->first_name ?? Auth::user()->name, 10, '...') }}
                                    </div>
                                    <div class="text-xs text-base/70">
                                        {{ \Illuminate\Support\Str::limit(auth()->user()->email, 10, '...') }}
                                    </div>
                                </div>
                                <svg class="w-4 h-4 transform transition-all duration-300 group-hover:text-primary" 
                                     :class="{ 'rotate-180': open }" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-80 bg-background-secondary/90 backdrop-blur-md border border-neutral/50 rounded-xl shadow-2xl z-50 overflow-hidden">
                                    
                                <div class="p-6 border-b border-neutral/50 bg-gradient-to-br from-primary/5 to-secondary/5 backdrop-blur-md">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <img src="{{ auth()->user()->avatar }}" 
                                                 class="w-16 h-16 rounded-full border-3 border-primary/30 shadow-lg" 
                                                 alt="avatar" />
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-lg text-foreground">{{ auth()->user()->name }}</div>
                                            <div class="text-sm text-base/80">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-2 backdrop-blur-md">
                                    @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                                        <x-navigation.link 
                                            :href="$nav['url']" 
                                            :spa="isset($nav['spa']) ? $nav['spa'] : true"
                                            class="flex items-center gap-3 px-6 py-3 text-sm text-base rounded-xl hover:bg-gradient-to-r hover:from-primary/1 hover:to-secondary/5 hover:text-primary transition-all duration-200 hover:translate-x-1">
                                            {{ $nav['name'] }}
                                        </x-navigation.link>
                                    @endforeach
                                    <div class="px-2 pb-2">
                                        <livewire:auth.logout />
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                        <button
                            @click="slideOverOpen = !slideOverOpen"
                            class="relative w-12 h-12 flex lg:hidden items-center justify-center rounded-xl bg-background hover:bg-gradient-to-br hover:from-primary/10 hover:to-secondary/10 transition-all duration-300 group hover:scale-110"
                            aria-label="Toggle Menu">
                            <span
                                x-show="!slideOverOpen"
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="opacity-0 rotate-180 scale-50"
                                x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                x-transition:leave="transition duration-200 ease-in"
                                x-transition:leave-start="opacity-100 rotate-0 scale-100"
                                x-transition:leave-end="opacity-0 -rotate-180 scale-50"
                                class="absolute inset-0 flex items-center justify-center"
                                aria-hidden="true">
                                <x-ri-menu-fill class="size-6 text-base/80 group-hover:text-primary transition-colors duration-200" />
                            </span>
                            <span
                                x-show="slideOverOpen"
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="opacity-0 rotate-180 scale-50"
                                x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                x-transition:leave="transition duration-200 ease-in"
                                x-transition:leave-start="opacity-100 rotate-0 scale-100"
                                x-transition:leave-end="opacity-0 -rotate-180 scale-50"
                                class="absolute inset-0 flex items-center justify-center"
                                aria-hidden="true">
                                <x-ri-close-fill class="size-6 text-base/80 group-hover:text-primary transition-colors duration-200" />
                            </span>
                        </button>
                    </div>
                </div>
            @else
                <div class="flex flex-row items-center justify-between w-full h-16 px-6">
                    <div class="flex items-center gap-6">
                        @if (theme('show_full_logo', true))
                            <a href="{{ route('home') }}" class="flex items-center h-10 gap-2 group" wire:navigate>
                                <x-logo class="h-10 w-auto group-hover:scale-105 transition-transform duration-300" />
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="flex items-center h-10 gap-2 group" wire:navigate>
                                <x-logo class="h-10 w-auto group-hover:scale-105 transition-transform duration-300" />
                                <span class="text-xl font-bold leading-none tracking-tight flex items-center group-hover:text-primary transition-colors duration-300">{{ config('app.name') }}</span>
                            </a>
                        @endif
                        <div class="hidden md:flex flex-row gap-2">
                            @foreach (\App\Classes\Navigation::getLinks() as $nav)
                                @if (isset($nav['children']) && count($nav['children']) > 0)
                                    <div class="relative">
                                        <x-dropdown>
                                            <x-slot:trigger>
                                                <span class="flex flex-row items-center p-3 text-sm font-semibold whitespace-nowrap text-base hover:text-primary transition-all duration-200 hover:bg-neutral/30 rounded-lg cursor-pointer">{{ $nav['name'] }}</span>
                                            </x-slot:trigger>
                                            <x-slot:content>
                                                @foreach ($nav['children'] as $child)
                                                    <x-navigation.link
                                                        :href="$child['url']"
                                                        :spa="isset($child['spa']) ? $nav['spa'] : true">
                                                        {{ $child['name'] }}
                                                    </x-navigation.link>
                                                @endforeach
                                            </x-slot:content>
                                        </x-dropdown>
                                    </div>
                                @else
                                    <x-navigation.link
                                        :href="$nav['url']"
                                        :spa="isset($nav['spa']) ? $nav['spa'] : true"
                                        class="flex items-center hover:bg-neutral/30 rounded-xl transition-all duration-200 hover:scale-105">
                                        {{ $nav['name'] }}
                                    </x-navigation.link>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="hidden md:flex items-center gap-2">

                            <x-dropdown>
                                <x-slot:trigger>
                                    <div class="px-3 py-2 hover:bg-neutral/30 rounded-lg transition-all duration-200 cursor-pointer group">
                                        <img 
                                            src="{{ asset('assets/flags/language-' . app()->getLocale() . '.svg') }}" 
                                            alt="{{ app()->getLocale() }}" 
                                            class="inline-block w-5 h-5 rounded-sm mr-2 object-cover" 
                                        >
                                        @if(theme('show_language_currency', false))
                                            <span class="text-sm font-semibold group-hover:text-primary transition-colors duration-200">{{ strtoupper(app()->getLocale()) }} | {{ session('currency', config('settings.default_currency')) }}</span>
                                        @else
                                            <span class="text-sm font-semibold group-hover:text-primary transition-colors duration-200">{{ strtoupper(app()->getLocale()) }}</span>
                                        @endif
                                    </div>
                                </x-slot:trigger>
                                <x-slot:content>
                                    <livewire:components.language-switch />
                                    <livewire:components.currency-switch />
                                </x-slot:content>
                            </x-dropdown>
                            <div class="hover:scale-110 transition-transform duration-200">
                                <x-theme-toggle />
                            </div>
                        </div>

                        <div class="hover:scale-110 transition-transform duration-200">
                            <livewire:components.cart />
                        </div>
                        
                        <livewire:components.notifications />
                        <div class="relative hidden md:block" x-data="{ open: false }">
                            <button @click="open = !open" 
                                     class="flex items-center gap-3 p-2 rounded-xl transition-all duration-300 group hover:bg-gradient-to-r hover:from-primary/5 hover:to-secondary/5 hover:scale-105">
                                <div class="hidden lg:block text-left">
                                    <div class="text-sm font-bold group-hover:text-primary transition-colors duration-200">
                                        {{ \Illuminate\Support\Str::limit(Auth::user()->first_name ?? Auth::user()->name, 10, '...') }}
                                    </div>
                                    <div class="text-xs text-base/70">
                                        {{ \Illuminate\Support\Str::limit(auth()->user()->email, 10, '...') }}
                                    </div>
                                </div>
                                <svg class="w-4 h-4 transform transition-all duration-300 group-hover:text-primary" 
                                     :class="{ 'rotate-180': open }" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-80 bg-background-secondary/90 backdrop-blur-md border border-neutral/50 rounded-xl shadow-2xl z-50 overflow-hidden">
                                    
                                <div class="p-6 border-b border-neutral/50 bg-gradient-to-br from-primary/5 to-secondary/5 backdrop-blur-md">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <img src="{{ auth()->user()->avatar }}" 
                                                 class="w-16 h-16 rounded-full border-3 border-primary/30 shadow-lg" 
                                                 alt="avatar" />
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-lg text-foreground">{{ auth()->user()->name }}</div>
                                            <div class="text-sm text-base/80">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-2 backdrop-blur-md">
                                    @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                                        <x-navigation.link 
                                            :href="$nav['url']" 
                                            :spa="isset($nav['spa']) ? $nav['spa'] : true"
                                            class="flex items-center gap-3 px-6 py-3 text-sm text-base rounded-xl hover:bg-gradient-to-r hover:from-primary/1 hover:to-secondary/5 hover:text-primary transition-all duration-200 hover:translate-x-1">
                                            {{ $nav['name'] }}
                                        </x-navigation.link>
                                    @endforeach
                                    <div class="px-2 pb-2">
                                        <livewire:auth.logout />
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                        <button
                            @click="slideOverOpen = !slideOverOpen"
                            class="relative w-12 h-12 flex lg:hidden items-center justify-center rounded-xl bg-background hover:bg-gradient-to-br hover:from-primary/10 hover:to-secondary/10 transition-all duration-300 group hover:scale-110"
                            aria-label="Toggle Menu">
                            <span
                                x-show="!slideOverOpen"
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="opacity-0 rotate-180 scale-50"
                                x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                x-transition:leave="transition duration-200 ease-in"
                                x-transition:leave-start="opacity-100 rotate-0 scale-100"
                                x-transition:leave-end="opacity-0 -rotate-180 scale-50"
                                class="absolute inset-0 flex items-center justify-center"
                                aria-hidden="true">
                                <x-ri-menu-fill class="size-6 text-base/80 group-hover:text-primary transition-colors duration-200" />
                            </span>
                            <span
                                x-show="slideOverOpen"
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="opacity-0 rotate-180 scale-50"
                                x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                x-transition:leave="transition duration-200 ease-in"
                                x-transition:leave-start="opacity-100 rotate-0 scale-100"
                                x-transition:leave-end="opacity-0 -rotate-180 scale-50"
                                class="absolute inset-0 flex items-center justify-center"
                                aria-hidden="true">
                                <x-ri-close-fill class="size-6 text-base/80 group-hover:text-primary transition-colors duration-200" />
                            </span>
                        </button>
                    </div>
                </div>
            @endif
            
            <template x-teleport="body">
                <div
                    x-show="slideOverOpen"
                    @keydown.window.escape="slideOverOpen=false"
                    x-cloak
                    class="fixed left-0 right-0 top-20 w-full z-[99]"
                    style="height:calc(100dvh - 5rem);"
                    aria-modal="true"
                    tabindex="-1">
                    <div
                        x-show="slideOverOpen"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition duration-200 ease-in"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-4"
                        @click.away="slideOverOpen = false"
                        class="absolute inset-0 bg-background-secondary/90 backdrop-blur-md border-t border-neutral/50 shadow-2xl overflow-y-auto flex flex-col rounded-t-2xl">

                        <div class="flex flex-col h-full p-6">
                            <div class="flex-1 min-h-0 overflow-y-auto">
                                <div class="space-y-2">
                                    <x-navigation.sidebar-links />
                                </div>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-neutral/50">
                                <div
                                    x-data="{ userPanelOpen: false }"
                                    @keydown.escape.window="userPanelOpen = false"
                                    x-cloak
                                    class="relative">
                                    <button @click="userPanelOpen = true" 
                                        aria-label="Open user menu" 
                                        class="flex gap-4 items-center justify-start w-full p-4 bg-gradient-to-br from-primary/5 to-secondary/5 hover:from-primary/10 hover:to-secondary/10 rounded-xl transition-all duration-300 group border border-primary/10">
                                        <div class="relative">
                                            <img src="{{ auth()->user()->avatar }}" 
                                                class="size-14 rounded-full border-2 border-primary/30 bg-background group-hover:border-primary transition-all duration-300 shadow-lg" 
                                                alt="avatar" />
                                        </div>
                                        <div class="flex flex-col items-start gap-1 flex-1">
                                            <span class="font-bold text-lg text-foreground group-hover:text-primary transition-colors duration-200">{{ auth()->user()->name }}</span>
                                            <span class="text-sm text-base/70">{{ auth()->user()->email }}</span>
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                <span class="text-xs text-green-500 font-medium">Online</span>
                                            </div>
                                        </div>
                                    </button>

                                    <div
                                        x-show="userPanelOpen"
                                        x-transition:enter="transition-opacity ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition-opacity ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        @click="userPanelOpen=false"
                                        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
                                        style="pointer-events: auto"></div>

                                    <div
                                        x-show="userPanelOpen"
                                        x-transition:enter="transition transform ease-out duration-300"
                                        x-transition:enter-start="translate-y-full opacity-0"
                                        x-transition:enter-end="translate-y-0 opacity-100"
                                        x-transition:leave="transition transform ease-in duration-200"
                                        x-transition:leave-start="translate-y-0 opacity-100"
                                        x-transition:leave-end="translate-y-full opacity-0"
                                        class="fixed bottom-0 left-0 right-0 z-50 mx-auto w-full max-w-md"
                                        style="pointer-events: auto"
                                        @click.away="userPanelOpen = false"
                                        tabindex="-1"
                                        aria-modal="true">
                                        <div class="bg-background-secondary/95 backdrop-blur-md shadow-2xl rounded-t-2xl border-t border-neutral/50 overflow-hidden">
                                            <div class="p-6 bg-gradient-to-br from-primary/5 to-secondary/5">
                                                <div class="flex gap-4 items-center justify-start mb-4">
                                                    <div class="relative">
                                                        <img src="{{ auth()->user()->avatar }}" 
                                                            class="size-16 rounded-full border-3 border-primary/30 bg-background shadow-lg" 
                                                            alt="avatar" />
                                                    </div>
                                                    <div class="flex flex-col gap-1 flex-1">
                                                        <span class="font-bold text-xl text-foreground">{{ auth()->user()->name }}</span>
                                                        <span class="text-sm text-base/60">{{ auth()->user()->email }}</span>
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                            <span class="text-xs text-green-500 font-medium">Online</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                
                                            <div class="p-6 pt-0">
                                                <div class="flex flex-col gap-2 w-full">
                                                    @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                                                        <x-navigation.link 
                                                            :href="$nav['url']" 
                                                            :spa="isset($nav['spa']) ? $nav['spa'] : true"
                                                            class="block px-4 py-3 text-base/80 hover:text-primary hover:bg-gradient-to-r hover:from-primary/5 hover:to-secondary/5 rounded-xl transition-all duration-200 font-medium hover:translate-x-1">
                                                            {{ $nav['name'] }}
                                                        </x-navigation.link>
                                                    @endforeach
                                                    <div class="h-px bg-neutral/50 my-2"></div>
                                                    <livewire:auth.logout />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</nav>

<aside class="mt-24 ml-8 w-64 h-[calc(100vh-2rem)] md:flex hidden flex-col fixed top-0 left-0 rtl:right-0 z-10">
    @if(theme('sidebar_style', 'fancy') === 'simple')
        <div class="h-9/10 border-r border-neutral/50 overflow-hidden">
    @else
        <div class="h-9/10 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-xl rounded-xl overflow-hidden">
    @endif
        <div class="flex flex-col h-full">
            <div class="flex-1 overflow-y-auto p-4 scrollbar-hide">
                <div class="space-y-2">
                    <div class="mb-6 md:hidden">
                        <div class="space-y-1">
                            @foreach (\App\Classes\Navigation::getLinks() as $nav)
                                @if (!empty($nav['children']))
                                    <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
                                         class="relative w-full overflow-hidden">
                                        <button @click="activeAccordion = !activeAccordion"
                                                class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                                                {{ $nav['active'] ? 'text-primary bg-primary/10 border border-primary/20 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-background' }}">
                                            <div class="flex items-center gap-3">
                                                @isset($nav['icon'])
                                                    <x-dynamic-component :component="$nav['icon']"
                                                        class="w-5 h-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                                                @endisset
                                                <span>{{ $nav['name'] }}</span>
                                            </div>
                                            <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                                                class="w-5 h-5 text-base/60 transition-transform duration-300" />
                                        </button>
                                        <div x-show="activeAccordion" x-collapse x-cloak>
                                            <div class="py-2 pl-8 pr-4 mt-2 border-l-2 border-primary/30 ml-4">
                                                @foreach ($nav['children'] as $child)
                                                    <x-navigation.link :href="$child['url']"
                                                        :spa="$child['spa'] ?? true"
                                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium mb-1 transition-all duration-200 group
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
                                    <x-navigation.link :href="$nav['url']"
                                        :spa="$nav['spa'] ?? true"
                                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                                            {{ $nav['active'] ? 'text-primary bg-primary/10 border border-primary/20 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-background' }}">
                                        @isset($nav['icon'])
                                            <x-dynamic-component :component="$nav['icon']"
                                                class="w-5 h-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                                        @endisset
                                        <span>{{ $nav['name'] }}</span>
                                        @if($nav['active'])
                                            <div class="ml-auto w-2 h-2 bg-primary rounded-full"></div>
                                        @endif
                                    </x-navigation.link>
                                @endif
                                @isset($nav['separator'])
                                    <div class="h-px w-full bg-neutral/30 my-3 mx-3"></div>
                                @endisset
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="space-y-1">
                            @foreach (\App\Classes\Navigation::getDashboardLinks() as $nav)
                                @if (!empty($nav['children']))
                                    <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
                                         class="relative w-full overflow-hidden">
                                        <button @click="activeAccordion = !activeAccordion"
                                                class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                                                {{ $nav['active'] ? 'text-primary bg-primary/10 border border-primary/20 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-background' }}">
                                            <div class="flex items-center gap-3">
                                                @isset($nav['icon'])
                                                    <x-dynamic-component :component="$nav['icon']"
                                                        class="w-5 h-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                                                @endisset
                                                <span>{{ $nav['name'] }}</span>
                                            </div>
                                            <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                                                class="w-5 h-5 text-base/60 transition-transform duration-300" />
                                        </button>
                                        <div x-show="activeAccordion" x-collapse x-cloak>
                                            <div class="py-2 pl-8 pr-4 mt-2 border-l-2 border-primary/30 ml-4">
                                                @foreach ($nav['children'] as $child)
                                                    @if ($child['condition'] ?? true)
                                                        <x-navigation.link :href="$child['url']"
                                                            :spa="$child['spa'] ?? true"
                                                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium mb-1 transition-all duration-200 group
                                                                {{ $child['active'] ? 'text-primary bg-primary/10 border border-primary/20' : 'text-base/70 hover:text-foreground hover:bg-background/50' }}">
                                                            @if($child['active'])
                                                                <div class="text-primary font-bold">|</div>
                                                            @endif
                                                            {{ $child['name'] }}
                                                        </x-navigation.link>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <x-navigation.link :href="$nav['url']"
                                        :spa="$nav['spa'] ?? true"
                                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 group
                                            {{ $nav['active'] ? 'text-primary bg-primary/10 border border-primary/20 shadow-sm' : 'text-base/80 hover:text-foreground hover:bg-background' }}">
                                        @if($nav['active'])
                                            <div class="text-primary font-bold">|</div>
                                        @endif
                                        @isset($nav['icon'])
                                            <x-dynamic-component :component="$nav['icon']"
                                                class="w-5 h-5 {{ $nav['active'] ? 'text-primary' : 'text-base/60 group-hover:text-primary' }} transition-colors" />
                                        @endisset
                                        <span>{{ $nav['name'] }}</span>
                                    </x-navigation.link>
                                @endif
                                @isset($nav['separator'])
                                    <div class="h-px w-full bg-neutral/30 my-3 mx-3"></div>
                                @endisset
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 pt-2 pb-1">
                <a href="{{ theme('new_order_link') ?? '/' }}" 
                   class="relative group block w-full overflow-hidden rounded-xl bg-gradient-to-br from-primary/10 to-transparent border border-primary/20 p-4 transition-all duration-300 hover:shadow-lg hover:shadow-primary/10 hover:border-primary/40">
                    
                    <div class="absolute -right-8 -top-8 w-20 h-20 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-all duration-500"></div>

                    <div class="relative flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/20 text-primary transition-colors group-hover:bg-primary group-hover:text-white">
                            <x-ri-shopping-bag-3-fill class="h-4 w-4" />
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <h4 class="text-md font-bold text-foreground">
                                {{ theme('new_order_title') ?? 'Need more services?' }}
                            </h4>
                            <p class="text-[10px] text-base/60 group-hover:text-base/80 transition-colors">
                                {{ theme('new_order_subtitle') ?? 'Head on our store' }}
                            </p>
                        </div>
                        <x-ri-arrow-right-s-line class="h-4 w-4 text-base/40 transition-transform duration-300 group-hover:translate-x-1 group-hover:text-primary" />
                    </div>
                </a>
            </div>

            <div class="px-4 py-3">
                <div class="flex items-center justify-between w-full gap-3">
                    @if(theme('discord_url'))
                        <div class="relative group rounded-xl transition-transform duration-300 hover:scale-110 hover:bg-background flex-1 flex justify-center" x-data="{ hover: false }">
                            <a href="{{ theme('discord_url') }}" target="_blank" rel="noopener noreferrer"
                               class="p-2 rounded-lg hover:bg-neutral/5 transition-colors duration-200 w-full flex justify-center"
                               title="Discord"
                               @mouseenter="hover = true" @mouseleave="hover = false">
                                <x-ri-discord-fill class="w-5 h-5 text-base/80" />
                            </a>

                            <span
                                x-cloak
                                x-show="hover"
                                x-transition.opacity
                                class="pointer-events-none absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 text-xs bg-background-secondary/90 border border-neutral/50 rounded-md shadow-sm whitespace-no-wrap">
                                Discord
                            </span>
                        </div>
                    @endif

                    @if(theme('docs_link'))
                        <div class="relative group rounded-xl transition-transform duration-300 hover:scale-110 hover:bg-background flex-1 flex justify-center" x-data="{ hover: false }">
                            <a href="{{ theme('docs_link') }}" target="_blank" rel="noopener noreferrer"
                               class="p-2 rounded-lg hover:bg-neutral/5 transition-colors duration-200 w-full flex justify-center"
                               title="Docs"
                               @mouseenter="hover = true" @mouseleave="hover = false">
                                <x-ri-book-open-fill class="w-5 h-5 text-base/80" />
                            </a>

                            <span
                                x-cloak
                                x-show="hover"
                                x-transition.opacity
                                class="pointer-events-none absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 text-xs bg-background-secondary/90 border border-neutral/50 rounded-md shadow-sm">
                                Documentation
                            </span>
                        </div>
                    @endif

                    @if(theme('control_panel_link'))
                        <div class="relative group rounded-xl transition-transform duration-300 hover:scale-110 hover:bg-background flex-1 flex justify-center" x-data="{ hover: false }">
                            <a href="{{ theme('control_panel_link') }}" target="_blank" rel="noopener noreferrer"
                               class="p-2 rounded-lg hover:bg-neutral/5 transition-colors duration-200 w-full flex justify-center"
                               title="Control Panel"
                               @mouseenter="hover = true" @mouseleave="hover = false">
                                <x-ri-computer-fill class="w-5 h-5 text-base/80" />
                            </a>

                            <span
                                x-cloak
                                x-show="hover"
                                x-transition.opacity
                                class="pointer-events-none absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 text-xs bg-background-secondary/90 border border-neutral/50 rounded-md shadow-sm">
                                Panel
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-4 border-t border-neutral/50">
                @if(auth()->check())
                    <div class="flex items-center gap-3 p-3 bg-background/80 backdrop-blur-sm rounded-xl border border-neutral/50 transition-colors hover:border-primary/30">
                        <div class="relative">
                            <img src="{{ auth()->user()->avatar }}" 
                                 class="w-10 h-10 rounded-full border-2 border-neutral/50 bg-background" 
                                 alt="User Avatar" 
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-foreground truncate">{{ auth()->user()->first_name }}</p>
                            <p class="text-xs text-base/60 truncate">{{ auth()->user()->email }}</p>
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
</aside>