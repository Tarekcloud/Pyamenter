<div class="mx-auto px-6 py-8 md:px-8 lg:px-12" x-data="{
    search: '', 
    statusFilter: 'all',
    get filteredServices() {
        const searchTerm = this.search.toLowerCase();
        const status = this.statusFilter;
        
        return Array.from(this.$refs.servicesContainer?.children || [])
            .filter(child => child.tagName === 'A')
            .filter(child => {
                if (status !== 'all' && child.dataset.status !== status) {
                    return false;
                }
                
                if (this.search) {
                    const serviceName = child.dataset.serviceName?.toLowerCase() || '';
                    const categoryName = child.dataset.categoryName?.toLowerCase() || '';
                    return serviceName.includes(searchTerm) || categoryName.includes(searchTerm);
                }
                
                return true;
            }).length > 0;
    },
    get visibleServicesCount() {
        const searchTerm = this.search.toLowerCase();
        const status = this.statusFilter;
        
        return Array.from(this.$refs.servicesContainer?.children || [])
            .filter(child => child.tagName === 'A')
            .filter(child => {
                if (status !== 'all' && child.dataset.status !== status) {
                    return false;
                }
                
                if (this.search) {
                    const serviceName = child.dataset.serviceName?.toLowerCase() || '';
                    const categoryName = child.dataset.categoryName?.toLowerCase() || '';
                    return serviceName.includes(searchTerm) || categoryName.includes(searchTerm);
                }
                
                return true;
            }).length;
    }
}">
    <h1 class="text-3xl lg:text-4xl font-bold mt-4 mb-8">
        {{ __('navigation.services') }}
    </h1>

    <div class="mb-8 delay-100">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 transition-colors duration-200" 
                     :class="search ? 'text-primary' : 'text-gray-400'"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input
                x-model.debounce.300ms="search"
                type="text"
                placeholder="{{ __('Search services by name or category...') }}"
                class="w-full pl-11 pr-4 py-3 bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 hover:bg-background-secondary/60 focus:bg-background-secondary/70"
            >
            <button x-show="search" 
                    x-cloak
                    @click="search = ''"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-primary transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div x-show="search || statusFilter !== 'all'" 
             x-cloak 
             class="mt-2 text-sm text-color-muted transition-all duration-300 transform"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">
            <span x-show="search && statusFilter !== 'all'">
                Showing <span class="font-bold text-primary" x-text="visibleServicesCount"></span> results for "<strong class="text-primary" x-text="search"></strong>" in <strong class="text-primary capitalize" x-text="statusFilter"></strong> services
            </span>
            <span x-show="search && statusFilter === 'all'">
                Showing <span class="font-bold text-primary" x-text="visibleServicesCount"></span> results for "<strong class="text-primary" x-text="search"></strong>"
            </span>
            <span x-show="!search && statusFilter !== 'all'">
                Showing <span class="font-bold text-primary" x-text="visibleServicesCount"></span> <strong class="text-primary capitalize" x-text="statusFilter"></strong> services
            </span>
        </div>
    </div>

    <div class="mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <div class="inline-flex p-1 bg-background-secondary/40 border border-neutral/50 rounded-xl backdrop-blur-md">
            
            <button @click="statusFilter = 'all'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'all' ? 'bg-background shadow-sm text-primary font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-apps-line class="size-4" x-bind:class="statusFilter === 'all' ? 'text-primary' : 'opacity-70'" />
                All Services
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10" 
                      :class="statusFilter === 'all' ? 'bg-primary/10 text-primary' : 'text-color-muted'">
                    {{ $services->count() }}
                </span>
            </button>

            <div class="w-px bg-neutral/30 my-2 mx-1"></div>

            <button @click="statusFilter = 'active'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'active' ? 'bg-background shadow-sm text-success font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-checkbox-circle-line class="size-4" x-bind:class="statusFilter === 'active' ? 'text-success' : 'opacity-70'" />
                {{ __('Active') }}
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10"
                      :class="statusFilter === 'active' ? 'bg-success/10 text-success' : 'text-color-muted'">
                    {{ $services->where('status', 'active')->count() }}
                </span>
            </button>

            <button @click="statusFilter = 'pending'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'pending' ? 'bg-background shadow-sm text-warning font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-loader-4-line class="size-4" x-bind:class="statusFilter === 'pending' ? 'text-warning' : 'opacity-70'" />
                {{ __('Pending') }}
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10"
                      :class="statusFilter === 'pending' ? 'bg-warning/10 text-warning' : 'text-color-muted'">
                    {{ $services->where('status', 'pending')->count() }}
                </span>
            </button>

            <button @click="statusFilter = 'suspended'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'suspended' ? 'bg-background shadow-sm text-red-500 font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-forbid-line class="size-4" x-bind:class="statusFilter === 'suspended' ? 'text-red-500' : 'opacity-70'" />
                {{ __('Suspended') }}
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10"
                      :class="statusFilter === 'suspended' ? 'bg-red-500/10 text-red-500' : 'text-color-muted'">
                    {{ $services->where('status', 'suspended')->count() }}
                </span>
            </button>

            <button @click="statusFilter = 'cancelled'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'cancelled' ? 'bg-background shadow-sm text-color-muted font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-close-circle-line class="size-4 opacity-70" />
                {{ __('Cancelled') }}
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10">
                    {{ $services->where('status', 'cancelled')->count() }}
                </span>
            </button>

        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 md:gap-5 delay-300" x-ref="servicesContainer">
        @forelse ($services as $service)
            <a href="{{ route('services.show', $service) }}"
               wire:navigate
               class="block service-item"
               data-service-name="{{ strtolower($service->product->name) }}"
               data-category-name="{{ strtolower($service->product->category->name) }}"
               data-status="{{ $service->status }}"
               x-show="(statusFilter === 'all' || '{{ $service->status }}' === statusFilter) && (search === '' || '{{ strtolower($service->product->name . ' ' . $service->product->category->name) }}'.includes(search.toLowerCase()))"
               x-transition:enter="transition ease-out duration-500 delay-75"
               x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
               x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
               x-transition:leave="transition ease-in duration-300"
               x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
               x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
            >
                <div class="bg-background-secondary/20 backdrop-blur-md hover:from-background-secondary/70 hover:to-background-secondary/50 border border-neutral/50 p-6 rounded-xl shadow-lg
                            transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:border-primary/50 hover:shadow-primary/10
                            flex flex-col md:flex-row items-start md:items-center justify-between gap-4 group">

                    <div class="flex items-start md:items-center gap-4 flex-grow">
                        <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm group-hover:bg-primary/20 group-hover:scale-110 transition-all duration-300">
                            <x-ri-instance-line class="size-6 text-primary group-hover:text-primary/80 transition-colors duration-300" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold leading-tight group-hover:text-primary transition-colors duration-300">{{ $service->product->name }}</span>
                            <p class="text-color-muted text-sm mt-1 group-hover:text-color-base transition-colors duration-300">
                                <strong>{{ __('services.services') }}:</strong> {{ $service->product->category->name }}
                                @if(in_array($service->plan->type, ['recurring']))
                                    - {{ __('services.every_period', [
                                        'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                                        'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)
                                    ]) }}
                                @else
                                    - {{ __('One time Payment') }}
                                @endif
                            </p>
                            @if($service->expires_at)
                                <p class="text-xs text-warning mt-1 group-hover:text-warning/80 transition-colors duration-300">
                                    <span class="font-extrabold">{{ __('services.expires_at') }}: </span><span class="font-medium">{{ $service->expires_at->format('d M Y') }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0 mt-3 md:mt-0">
                        <div class="text-sm font-semibold px-3 py-1 rounded-xl flex items-center gap-1.5 transition-all duration-300 group-hover:scale-105
                            @if ($service->status == 'active') text-success bg-success/20 group-hover:bg-success/30
                            @elseif($service->status == 'suspended') text-red-500 bg-red-500/20 group-hover:bg-red-500/30
                            @elseif($service->status == 'cancelled') text-inactive bg-inactive/20 group-hover:bg-inactive/30
                            @else text-warning bg-warning/20 group-hover:bg-warning/30
                            @endif">
                            @if ($service->status == 'active')
                                <x-ri-checkbox-circle-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                                {{ __('Active') }}
                            @elseif($service->status == 'suspended')
                                <x-ri-forbid-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                                {{ __('Suspended') }}
                            @elseif($service->status == 'cancelled')
                                <x-ri-close-circle-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                                {{ __('Canceled') }}
                            @elseif($service->status == 'pending')
                                <x-ri-error-warning-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                                {{ __('Pending') }}
                            @endif
                        </div>
                        <x-ri-arrow-right-s-line class="size-6 text-color-muted group-hover:text-primary group-hover:translate-x-1 transition-all duration-300" />
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-background-secondary/20 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50 text-center">
                <x-ri-inbox-line class="size-16 text-color-muted mx-auto mb-4 opacity-60" />
                <h3 class="text-xl font-bold mb-2">No services yet.</h3>
            </div>
        @endforelse

        @if($services->isNotEmpty())
        <div x-show="(search !== '' || statusFilter !== 'all') && !filteredServices"
             x-cloak
             class="bg-background-secondary/20 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50 text-center"
             x-transition:enter="transition ease-out duration-500 delay-200"
             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2">
            <x-ri-search-2-line class="size-16 text-color-muted mx-auto mb-4 opacity-60" />
            <h3 class="text-xl font-bold mb-2">{{ __('services.no_services') }}</h3>
            
            <div x-show="search && statusFilter !== 'all'">
                <p class="text-color-muted mb-4">
                    No <strong class="text-primary capitalize" x-text="statusFilter"></strong> services match "<strong class="text-primary" x-text="search"></strong>"
                </p>
            </div>
            <div x-show="search && statusFilter === 'all'">
                <p class="text-color-muted mb-4">
                    Your search for "<strong class="text-primary" x-text="search"></strong>" did not match any services.
                </p>
            </div>
            <div x-show="!search && statusFilter !== 'all'">
                <p class="text-color-muted mb-4">
                    No <strong class="text-primary capitalize" x-text="statusFilter"></strong> services found.
                </p>
            </div>
            
            <div class="flex gap-2 justify-center flex-wrap">
                <button x-show="search" 
                        @click="search = ''" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary rounded-lg transition-all duration-300 hover:scale-105">
                    <x-ri-close-line class="size-4" />
                    Clear Search
                </button>
                <button x-show="statusFilter !== 'all'" 
                        @click="statusFilter = 'all'" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-secondary/10 hover:bg-secondary/20 text-secondary rounded-lg transition-all duration-300 hover:scale-105">
                    <x-ri-filter-off-line class="size-4" />
                    Show All
                </button>
            </div>
        </div>
        @endif

        <div x-show="search === '' && statusFilter === 'all'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            {{ $services->links() }}
        </div>
    </div>
</div>