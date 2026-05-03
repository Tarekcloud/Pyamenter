<div class="mx-auto px-6 py-8 md:px-8 lg:px-12" x-data="{
    search: '', 
    statusFilter: 'all',
    get filteredTickets() {
        const searchTerm = this.search.toLowerCase();
        const status = this.statusFilter;
        
        return Array.from(this.$refs.ticketsContainer?.children || [])
            .filter(child => child.tagName === 'A')
            .filter(child => {
                if (status !== 'all' && child.dataset.status !== status) {
                    return false;
                }
                
                if (this.search) {
                    const ticketSubject = child.dataset.ticketSubject?.toLowerCase() || '';
                    return ticketSubject.includes(searchTerm);
                }
                
                return true;
            }).length > 0;
    },
    get visibleTicketsCount() {
        const searchTerm = this.search.toLowerCase();
        const status = this.statusFilter;
        
        return Array.from(this.$refs.ticketsContainer?.children || [])
            .filter(child => child.tagName === 'A')
            .filter(child => {
                if (status !== 'all' && child.dataset.status !== status) {
                    return false;
                }
                
                if (this.search) {
                    const ticketSubject = child.dataset.ticketSubject?.toLowerCase() || '';
                    return ticketSubject.includes(searchTerm);
                }
                
                return true;
            }).length;
    }
}">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <h1 class="text-3xl lg:text-4xl font-bold mt-4">
            {{ __('ticket.tickets') }}
        </h1>
        <x-navigation.link :href="route('tickets.create')" class="inline-flex items-center justify-center px-5 py-2 border border-transparent font-medium rounded-xl shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200 order-first md:order-last">
            <x-ri-add-line class="size-5 mr-2" />
            <span>{{ __('ticket.create_ticket') }}</span>
        </x-navigation.link>
    </div>

    <div class="mb-8">
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
                placeholder="{{ __('Search by ticket subject...') }}"
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
                Showing <span class="font-bold text-primary" x-text="visibleTicketsCount"></span> results for "<strong class="text-primary" x-text="search"></strong>" in <strong class="text-primary capitalize" x-text="statusFilter"></strong> tickets
            </span>
            <span x-show="search && statusFilter === 'all'">
                Showing <span class="font-bold text-primary" x-text="visibleTicketsCount"></span> results for "<strong class="text-primary" x-text="search"></strong>"
            </span>
            <span x-show="!search && statusFilter !== 'all'">
                Showing <span class="font-bold text-primary" x-text="visibleTicketsCount"></span> <strong class="text-primary capitalize" x-text="statusFilter"></strong> tickets
            </span>
        </div>
    </div>

    <div class="mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <div class="inline-flex p-1 bg-background-secondary/40 border border-neutral/50 rounded-xl backdrop-blur-md">
            
            <button @click="statusFilter = 'all'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'all' ? 'bg-background shadow-sm text-primary font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-apps-line class="size-4" x-bind:class="statusFilter === 'all' ? 'text-primary' : 'opacity-70'" />
                All Tickets
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10" 
                      :class="statusFilter === 'all' ? 'bg-primary/10 text-primary' : 'text-color-muted'">
                    {{ $tickets->count() }}
                </span>
            </button>

            <div class="w-px bg-neutral/30 my-2 mx-1"></div>

            <button @click="statusFilter = 'open'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'open' ? 'bg-background shadow-sm text-success font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-add-circle-line class="size-4" x-bind:class="statusFilter === 'open' ? 'text-success' : 'opacity-70'" />
                Open
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10"
                      :class="statusFilter === 'open' ? 'bg-success/10 text-success' : 'text-color-muted'">
                    {{ $tickets->where('status', 'open')->count() }}
                </span>
            </button>

            <button @click="statusFilter = 'replied'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'replied' ? 'bg-background shadow-sm text-info font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-chat-smile-2-line class="size-4" x-bind:class="statusFilter === 'replied' ? 'text-info' : 'opacity-70'" />
                Replied
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10"
                      :class="statusFilter === 'replied' ? 'bg-info/10 text-info' : 'text-color-muted'">
                    {{ $tickets->where('status', 'replied')->count() }}
                </span>
            </button>

            <button @click="statusFilter = 'closed'" 
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 whitespace-nowrap"
                    :class="statusFilter === 'closed' ? 'bg-background shadow-sm text-color-muted font-bold ring-1 ring-neutral/20' : 'text-color-muted hover:text-color-base hover:bg-white/5'">
                <x-ri-close-circle-line class="size-4 opacity-70" />
                Closed
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-md bg-neutral/10">
                    {{ $tickets->where('status', 'closed')->count() }}
                </span>
            </button>

        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:gap-8" x-ref="ticketsContainer">
        @forelse ($tickets as $ticket)
        <a href="{{ route('tickets.show', $ticket) }}" 
           wire:navigate 
           class="block group"
           data-ticket-subject="{{ strtolower($ticket->subject) }}"
           data-status="{{ $ticket->status }}"
           x-show="(statusFilter === 'all' || '{{ $ticket->status }}' === statusFilter) && (search === '' || '{{ strtolower($ticket->subject) }}'.includes(search.toLowerCase()))"
           x-transition:enter="transition ease-out duration-500 delay-75"
           x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
           x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
           x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
        >
            <div class="bg-background-secondary/20 backdrop-blur-md hover:from-background-secondary/70 hover:to-background-secondary/50 border border-neutral/50 p-6 rounded-xl shadow-lg
                        transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:border-primary/50 hover:shadow-primary/10
                        flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

                <div class="flex items-start md:items-center gap-4 flex-grow">
                    <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm group-hover:bg-primary/20 group-hover:scale-110 transition-all duration-300">
                        <x-ri-ticket-line class="size-6 text-primary group-hover:text-primary/80 transition-colors duration-300" />
                    </div>
                    
                    <div class="flex flex-col">
                        <span class="text-xl font-bold leading-tight group-hover:text-primary transition-colors duration-200">
                            #{{ $ticket->id }} - {{ $ticket->subject }}
                        </span>
                        <p class="text-color-muted text-sm mt-1 group-hover:text-color-base transition-colors duration-300">
                            {{ __('ticket.last_activity') }}: 
                            {{ $ticket->messages()->orderBy('created_at', 'desc')->first()->created_at->diffForHumans() }}
                            @if($ticket->department)
                                <span class="mx-2 text-color-muted/50">•</span> {{ $ticket->department }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-shrink-0 mt-3 md:mt-0">
                    <div class="text-sm font-semibold px-3 py-1 rounded-xl flex items-center gap-1.5 transition-all duration-300 group-hover:scale-105
                        @if ($ticket->status == 'open') text-success bg-success/20 group-hover:bg-success/30
                        @elseif($ticket->status == 'closed') text-inactive bg-inactive/20 group-hover:bg-inactive/30
                        @elseif($ticket->status == 'replied') text-info bg-info/20 group-hover:bg-info/30
                        @else text-warning bg-warning/20 group-hover:bg-warning/30
                        @endif">
                        @if ($ticket->status == 'open')
                            <x-ri-add-circle-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                            Open
                        @elseif($ticket->status == 'closed')
                            <x-ri-forbid-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                            Closed
                        @elseif($ticket->status == 'replied')
                            <x-ri-chat-smile-2-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                            Replied
                        @else
                            <x-ri-error-warning-fill class="size-4 transition-transform duration-300 group-hover:rotate-12" />
                            {{ ucfirst($ticket->status) }} 
                        @endif
                    </div>
                    <x-ri-arrow-right-s-line class="size-6 text-color-muted group-hover:text-primary group-hover:translate-x-1 transition-all duration-300" />
                </div>
            </div>
        </a>
        @empty
        <div class="bg-background-secondary/20 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50 text-center">
            <x-ri-inbox-line class="size-16 text-color-muted mx-auto mb-4 opacity-60" />
            <h3 class="text-xl font-bold mb-2">No Tickets yet.</h3>
        </div>
        @endforelse

        @if($tickets->isNotEmpty())
        <div x-show="(search !== '' || statusFilter !== 'all') && !filteredTickets"
             x-cloak
             class="bg-background-secondary/20 backdrop-blur-md rounded-xl shadow-lg p-6 lg:p-8 border border-neutral/50 text-center"
             x-transition:enter="transition ease-out duration-500 delay-200"
             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2">
            <x-ri-search-2-line class="size-16 text-color-muted mx-auto mb-4 opacity-60" />
            <h3 class="text-xl font-bold mb-2">{{ __('ticket.no_tickets') }}</h3>
            
            <div x-show="search && statusFilter !== 'all'">
                <p class="text-color-muted mb-4">
                    No <strong class="text-primary capitalize" x-text="statusFilter"></strong> tickets match "<strong class="text-primary" x-text="search"></strong>"
                </p>
            </div>
            <div x-show="search && statusFilter === 'all'">
                <p class="text-color-muted mb-4">
                    Your search for "<strong class="text-primary" x-text="search"></strong>" did not match any tickets.
                </p>
            </div>
            <div x-show="!search && statusFilter !== 'all'">
                <p class="text-color-muted mb-4">
                    No <strong class="text-primary capitalize" x-text="statusFilter"></strong> tickets found.
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
            {{ $tickets->links() }}
        </div>
    </div>
</div>