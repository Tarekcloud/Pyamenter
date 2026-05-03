<x-app-layout>
    <x-slot name="title">
        {{ __('errors.404.title') }}
    </x-slot>

    <div class="container mt-8 mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-center min-h-[70vh] text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary/10 to-secondary/10 border border-primary/20 rounded-full mb-6 animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="w-2 h-2 bg-primary rounded-full"></div>
                <span class="text-sm font-bold text-primary uppercase tracking-wider">Error 404</span>
            </div>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-color-base mb-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                {{ __('errors.404.title') }}
            </h2>
            <p class="text-lg sm:text-xl text-color-muted max-w-2xl mb-8 leading-relaxed animate-fade-in-up" style="animation-delay: 0.4s;">
                {{ __('errors.404.message') }}
            </p>
            <div class="w-full max-w-md mb-10 animate-fade-in-up" style="animation-delay: 0.5s;">
                <div class="bg-background-secondary/50 border border-neutral rounded-xl p-6 shadow-lg">
                    <h3 class="text-sm font-semibold text-color-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Quick Suggestions
                    </h3>
                    <ul class="space-y-3 text-left">
                        <li class="flex items-start gap-3 text-color-muted">
                            <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span>Check the URL for typos</span>
                        </li>
                        <li class="flex items-start gap-3 text-color-muted">
                            <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span>Use the search bar to find what you need</span>
                        </li>
                        <li class="flex items-start gap-3 text-color-muted">
                            <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span>Return to the homepage and start fresh</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.6s;">
                <a href="{{ route('home') }}" wire:navigate>
                    <button class="group relative px-8 py-4 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            {{ __('navigation.home') }}
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-secondary to-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                </a>

                <button onclick="history.back()" class="px-8 py-4 bg-background-secondary border-2 border-neutral text-color-base font-semibold rounded-xl hover:bg-background transition-all duration-300 hover:scale-105 hover:border-primary shadow-lg">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </span>
                </button>
            </div>
        </div>
    </div>
</x-app-layout>