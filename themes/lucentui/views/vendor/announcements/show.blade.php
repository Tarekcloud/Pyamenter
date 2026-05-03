<div class="container mx-auto px-8 py-8 mt-12">
    <div class="mb-6">
        <a href="{{ route('announcements.index') }}" wire:navigate
           class="inline-flex items-center text-primary hover:text-primary-dark transition-colors duration-200 group">
            <x-ri-arrow-left-line class="size-4 mr-2 transform transition-transform duration-300 group-hover:-translate-x-1" />
            <span class="font-medium">Back</span>
        </a>
    </div>

    <div class="bg-background-secondary/80 backdrop-blur-md border border-neutral/50 rounded-xl shadow-xl overflow-hidden">
        <div class="relative p-6 sm:p-8 lg:p-10">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-primary/10 opacity-70 z-0"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-color-base leading-tight mb-2">
                        {{ $announcement->title }}
                    </h1>
                    <p class="text-color-muted text-sm sm:text-base flex items-center gap-2">
                        <x-ri-calendar-line class="size-4" />
                        <span>{{ $announcement->published_at->format('F d, Y \a\t H:i') }} ({{ $announcement->published_at->diffForHumans() }})</span>
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="bg-primary text-white px-4 py-2 rounded-full text-xs font-semibold uppercase shadow-lg">
                        {{ __('New Update') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 lg:p-10 prose dark:prose-invert max-w-full text-color-base leading-relaxed">
            {!! $announcement->content !!}
        </div>
    </div>
</div>