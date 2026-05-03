<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">

    @if(theme('custom_dashboard_html'))
        <div class="custom-dashboard-content animate-enter">
            {!! theme('custom_dashboard_html') !!}
        </div>
    @endif

    @if(theme('banner_enabled', false))

        <!-- Banner system - TODO: maybe redesign this later -->
        <div x-data="{ 
            showBanner: !localStorage.getItem('banner_dismissed_{{ Auth::user()->id }}_{{ md5(theme('banner_message', '')) }}'),
            dismissBanner() {
                this.showBanner = false;
                @if(theme('banner_dismissible', true))
                    localStorage.setItem('banner_dismissed_{{ Auth::user()->id }}_{{ md5(theme('banner_message', '')) }}', 'true');
                @endif
            }
        }" 
        x-show="showBanner" 
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="w-full mb-4 p-4 rounded-xl shadow-md relative overflow-hidden animate-enter
            @switch(theme('banner_type', 'info'))
                @case('critical')
                    bg-red-600/20 border-l-4 border-red-500 text-red-500
                    @break
                @case('warning')
                    bg-yellow-600/20 border-l-4 border-yellow-500 text-yellow-500
                    @break
                @case('success')
                    bg-green-600/20 border-l-4 border-green-500 text-green-500
                    @break
                @default
                    bg-primary/20 border-l-4 border-primary text-primary
            @endswitch
        ">
            <div class="relative flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="flex-shrink-0">
                        @switch(theme('banner_type', 'info'))
                            @case('critical')
                                <x-ri-error-warning-line class="size-5 text-red-500" />
                                @break
                            @case('warning')
                                <x-ri-alert-line class="size-5 text-yellow-500" />
                                @break
                            @case('success')
                                <x-ri-checkbox-circle-line class="size-5 text-green-500" />
                                @break
                            @default
                                <x-ri-information-line class="size-5 text-primary" />
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm leading-relaxed">
                            {!! Str::markdown(theme('banner_message', '**This is an important announcement for all users.** You can use **bold**, *italic*, or even [links](https://example.com).'), ['html_input' => 'strip']) !!}
                        </p>
                    </div>
                </div>
                @if(theme('banner_dismissible', true))
                    <button @click="dismissBanner()" 
                            class="flex-shrink-0 bg-primary/20 hover:bg-primary/30 p-1.5 rounded-full transition-all duration-500 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <x-ri-close-line class="size-4 text-primary" />
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- welcome section -->
    <div class="rounded-xl p-8 mb-8 shadow-2xl bg-primary relative overflow-hidden">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 relative z-10">
            <div>
                <div class="mb-4">
                    <div class="flex items-center gap-6 rounded-xl py-2">
                        <x-ri-time-line class="text-white size-4" />
                        <div class="text-white text-sm font-medium" id="current-time">
                            <span class="animate-pulse">Loading...</span>
                        </div>
                        <div class="w-px h-6 bg-white/20 mx-4"></div>
                        <x-ri-calendar-line class="text-white size-4" />
                        <div class="text-white text-sm font-medium" id="current-date">
                            <span class="animate-pulse">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <h1 class="text-4xl lg:text-5xl font-bold text-white mb-2 underline decoration-primary/50 underline-offset-8">
                        {{ __('dashboard.welcome_back', ['name' => Auth::user()->first_name]) }}
                        <span class="inline-block animate-wave">👋</span>
                    </h1>
                    <p class="text-xl text-white/80 font-medium max-w-2xl">
                        {{ __('dashboard.dashboard_description') }}
                    </p>
                </div>
                <div x-data="{ showBalance: false }" class="mt-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-white/90 text-l font-medium">{{ __('account.credits') }}:</h3>
                        @if (Auth::user()->credits->count() > 0)
                            <button @click="showBalance = !showBalance" 
                                    class="text-white/70 hover:text-white transition-all duration-500 focus:outline-none transform hover:scale-110 pulse-glow">
                                <template x-if="showBalance">
                                    <x-ri-eye-off-line class="size-4" />
                                </template>
                                <template x-if="!showBalance">
                                    <x-ri-eye-line class="size-4" />
                                </template>
                            </button>
                        @endif
                    </div>

                    @if (Auth::user()->credits->count() > 0)
                        <div class="flex flex-wrap gap-x-6 gap-y-2 mt-2">
                            @foreach (Auth::user()->credits as $credit)
                                <div class="flex items-center text-white/90 font-bold text-l">
                                    <span x-bind:class="showBalance ? '' : 'blur-sm'" 
                                          class="transition-all duration-500 transform"
                                          x-transition:enter="transition ease-out duration-500"
                                          x-transition:enter-start="scale-75 opacity-0"
                                          x-transition:enter-end="scale-100 opacity-100">
                                    {{ $credit->formattedAmount }}
                                    </span>
                                    <span class="ml-2 text-white/70 text-base font-semibold">{{ $credit->currency->code }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center gap-2 mt-2 text-white/70">
                            <x-ri-emotion-sad-line class="size-5" />
                            <span class="text-md">{{ __('account.no_credit') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick action buttons -->
    <div class="mb-8 animate-enter delay-200">
        <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 flex flex-wrap items-center gap-2 p-2 rounded-xl shadow-xl w-fit transition-all duration-300">
            
            <a href="{{ theme('buy_server_link', '#') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-color-muted hover:text-primary hover:bg-primary/10 transition-all duration-200">
                <x-ri-server-fill class="size-5" />
                <span class="font-medium text-sm">{{ theme('buy_server_title', 'Buy Server') }}</span>
            </a>

            <div class="w-px h-4 bg-neutral/20"></div>

            <a href="{{ theme('help_link', '#') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 rounded-lg text-color-muted hover:text-purple-500 hover:bg-purple-500/10 transition-all duration-200">
                <x-ri-question-answer-fill class="size-5" />
                <span class="font-medium text-sm">{{ theme('help_title', 'Help') }}</span>
            </a>

            <div class="w-px h-4 bg-neutral/20"></div>

            <a href="{{ route('account') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 rounded-lg text-color-muted hover:text-blue-500 hover:bg-blue-500/10 transition-all duration-200">
                <x-ri-user-settings-fill class="size-5" />
                <span class="font-medium text-sm">{{ __('navigation.account') }}</span>
            </a>

            <div class="w-px h-4 bg-neutral/20"></div>

            <a href="{{ theme('docs_link', '#') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 rounded-lg text-color-muted hover:text-emerald-500 hover:bg-emerald-500/10 transition-all duration-200">
                <x-ri-book-open-fill class="size-5" />
                <span class="font-medium text-sm">{{ theme('docs_title', 'Docs') }}</span>
            </a>

        </div>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-8 animate-enter delay-100">
        <div class="group bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-sm rounded-xl p-6 hover:border-primary/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors duration-300">
                    <x-ri-archive-stack-fill class="size-6 text-primary" />
                </div>
                <a href="{{ route('services') }}" wire:navigate
                   class="text-base/40 hover:text-primary transition-colors duration-200">
                    <x-ri-arrow-right-line class="size-5" />
                </a>
            </div>
            <div class="space-y-1">
                <p class="text-base/60 text-sm font-medium">{{ __('dashboard.active_services') }}</p>
                <p class="text-3xl font-bold text-foreground">
                    {{ Auth::user()->services()->where('status', 'active')->count() }}
                </p>
            </div>
        </div>

        <div class="group bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-sm rounded-xl p-6 hover:border-orange-500/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-500/10 rounded-xl flex items-center justify-center group-hover:bg-orange-500/20 transition-colors duration-300">
                    <x-ri-receipt-fill class="size-6 text-orange-500" />
                </div>
                <a href="{{ route('invoices') }}" wire:navigate
                   class="text-base/40 hover:text-orange-500 transition-colors duration-200">
                    <x-ri-arrow-right-line class="size-5" />
                </a>
            </div>
            <div class="space-y-1">
                <p class="text-base/60 text-sm font-medium">{{ __('dashboard.unpaid_invoices') }}</p>
                <p class="text-3xl font-bold text-foreground">
                    {{ Auth::user()->invoices()->where('status', 'pending')->count() }}
                </p>
            </div>
        </div>

        @if(!config('settings.tickets_disabled', false))
            <div class="group bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-sm rounded-xl p-6 hover:border-green-500/30 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center group-hover:bg-green-500/20 transition-colors duration-300">
                        <x-ri-customer-service-fill class="size-6 text-green-500" />
                    </div>
                    <a href="{{ route('tickets') }}" wire:navigate
                       class="text-base/40 hover:text-green-500 transition-colors duration-200">
                        <x-ri-arrow-right-line class="size-5" />
                    </a>
                </div>
                <div class="space-y-1">
                    <p class="text-base/60 text-sm font-medium">{{ __('dashboard.open_tickets') }}</p>
                    <p class="text-3xl font-bold text-foreground">
                        {{ Auth::user()->tickets()->where('status', '!=', 'closed')->count() }}
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-10 grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 animate-enter delay-300">
        
        <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-sm rounded-xl p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                        <x-ri-archive-stack-fill class="size-5 text-primary" />
                    </div>
                    <h2 class="text-lg font-bold text-foreground">{{ __('dashboard.active_services') }}</h2>
                </div>
                <span class="text-sm font-semibold text-base/60">
                    {{ Auth::user()->services()->where('status', 'active')->count() }}
                </span>
            </div>
            <div class="space-y-3 mb-6">
                <livewire:services.widget status="active" />
            </div>
            <x-navigation.link 
                class="w-full bg-background/50 hover:bg-background/80 border border-neutral/30 shadow-md flex items-center justify-center px-4 py-3 rounded-xl font-medium text-foreground transition-all duration-200 group"
                :href="route('services')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-line class="size-4 ml-2 group-hover:translate-x-1 transition-transform duration-200" />
            </x-navigation.link>
        </div>
        <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-sm rounded-xl p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center">
                        <x-ri-receipt-fill class="size-5 text-orange-500" />
                    </div>
                    <h2 class="text-lg font-bold text-foreground">{{ __('dashboard.unpaid_invoices') }}</h2>
                </div>
                <span class="text-sm font-semibold text-base/60">
                    {{ Auth::user()->invoices()->where('status', 'pending')->count() }}
                </span>
            </div>
            <div class="space-y-3 mb-6">
                <livewire:invoices.widget :limit="3" />
            </div>
            <x-navigation.link 
                class="w-full bg-background/50 hover:bg-background/80 border border-neutral/50 shadow-md flex items-center justify-center px-4 py-3 rounded-xl font-medium text-foreground transition-all duration-200 group"
                :href="route('invoices')">
                {{ __('dashboard.view_all') }}
                <x-ri-arrow-right-line class="size-4 ml-2 group-hover:translate-x-1 transition-transform duration-200" />
            </x-navigation.link>
        </div>

        @if(!config('settings.tickets_disabled', false))
            <div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-sm rounded-xl p-6 lg:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/10 rounded-xl flex items-center justify-center">
                            <x-ri-customer-service-fill class="size-5 text-green-500" />
                        </div>
                        <h2 class="text-lg font-bold text-foreground">{{ __('dashboard.open_tickets') }}</h2>
                    </div>
                    <a href="{{ route('tickets.create') }}" wire:navigate 
                       class="text-base/60 hover:text-green-500 transition-colors duration-200">
                        <x-ri-add-circle-fill class="size-6" />
                    </a>
                </div>
                <div class="space-y-3 mb-6">
                    <livewire:tickets.widget />
                </div>
                <x-navigation.link 
                    class="w-full bg-background/50 hover:bg-background/80 border border-neutral/50 shadow-md flex items-center justify-center px-4 py-3 rounded-xl font-medium text-foreground transition-all duration-200 group"
                    :href="route('tickets')">
                    {{ __('dashboard.view_all') }}
                    <x-ri-arrow-right-line class="size-4 ml-2 group-hover:translate-x-1 transition-transform duration-200" />
                </x-navigation.link>
            </div>
        @endif
    </div>

    {!! hook('pages.dashboard') !!}

</div>