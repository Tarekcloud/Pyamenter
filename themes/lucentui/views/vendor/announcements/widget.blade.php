@if($announcements->count() > 0) 
<div class="bg-background-secondary/20 backdrop-blur-md border border-neutral/50 rounded-xl shadow-lg overflow-hidden mb-2">
    <div class="p-6 pb-4 border-b border-neutral/30">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-primary/10 text-primary p-3 rounded-full shadow-md">
                    <x-ri-megaphone-fill class="size-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-color-base">{{ __('Announcements') }}</h2>
                    <p class="text-color-muted text-sm">Latest updates and news</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-2 text-color-muted">
                <div class="size-2 bg-primary rounded-full animate-pulse"></div>
                <span class="text-sm font-medium">{{ $announcements->count() }} {{ Str::plural('new', $announcements->count()) }}</span>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($announcements->take(4) as $index => $announcement)
            <a href="{{ route('announcements.show', $announcement) }}" wire:navigate 
               class="group block animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                <div class="bg-background-tertiary hover:bg-background-tertiary/80 border border-neutral/50 hover:border-primary/30 p-4 rounded-xl transition-all duration-500 hover:shadow-lg hover:scale-[1.02] relative overflow-hidden h-full">
                    <!-- Content -->
                    <div class="flex flex-col h-full">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="bg-primary/10 text-primary p-2 rounded-full shrink-0 group-hover:bg-primary/20 transition-colors duration-500 flex items-center">
                                <x-ri-newspaper-line class="size-4" />
                                <h3 class="font-semibold text-color-base group-hover:text-primary transition-colors duration-500 line-clamp-2 ml-2 text-sm">
                                    {{ $announcement->title }}
                                </h3>
                            </div>
                            <div class="text-color-muted group-hover:text-primary transition-colors duration-500 shrink-0 ml-auto">
                                <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-500 group-hover:translate-x-1" />
                            </div>
                        </div>
                        
                        <div class="flex-1">
                            <p class="text-color-muted text-xs line-clamp-3 leading-relaxed mb-3">
                                {{ $announcement->description }}
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-2 text-color-muted text-xs mt-auto">
                            <x-ri-time-fill class="size-3" />
                            <span>{{ $announcement->published_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-primary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    @if($announcements->count() > 4)
    <div class="px-6 pb-6">
        <div class="bg-background-tertiary/50 border border-neutral/30 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 text-primary p-2 rounded-full">
                        <x-ri-add-line class="size-4" />
                    </div>
                    <div>
                        <p class="text-color-base font-medium text-sm">More Announcements</p>
                        <p class="text-color-muted text-xs">{{ $announcements->count() - 4 }} more {{ Str::plural('announcement', $announcements->count() - 4) }} available</p>
                    </div>
                </div>
                <a href="{{ route('announcements.index') }}" wire:navigate 
                   class="group bg-primary/10 hover:bg-primary hover:text-white text-primary px-4 py-2 rounded-lg font-medium transition-all duration-500 hover:shadow-md flex items-center gap-2 text-sm">
                    {{ __('dashboard.view_all') }}
                    <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-500 group-hover:translate-x-1" />
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="p-6 pt-0">
        <a href="{{ route('announcements.index') }}" wire:navigate 
           class="group w-full bg-background-tertiary hover:bg-background-tertiary/80 border border-neutral/50 hover:border-primary/30 text-color-base hover:text-primary px-6 py-3 rounded-xl font-medium transition-all duration-500 hover:shadow-lg flex items-center justify-center gap-2">
            {{ __('dashboard.view_all') }}
            <x-ri-arrow-right-fill class="size-4 transform transition-transform duration-500 group-hover:translate-x-1" />
        </a>
    </div>
    @endif
</div>
@endif