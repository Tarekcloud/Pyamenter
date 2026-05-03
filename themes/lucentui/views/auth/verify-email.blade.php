<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto flex h-full px-4">

        <div class="hidden lg:flex w-full lg:w-1/2 p-16 flex-col justify-center items-center text-center">
            <h2 class="text-4xl font-extrabold text-color-base mb-4 leading-tight">
                {{ theme('login_title', 'Join Our Community!') }}
            </h2>
            <p class="text-xl text-color-muted font-light max-w-md">
                {{ theme('login_subtitle', 'Save hundreds of hours building and developing dashboards from scratch.') }}
            </p>

            <div class="mt-12 p-6 bg-primary/10 dark:bg-primary/20 rounded-xl shadow-2xl backdrop-blur-sm border border-primary/20">
                <p class="text-sm italic text-color-base">
                    {{ theme('login_testimonial_text', 'This product changed the way our service works. Very intuitive and fast!') }}
                </p>
                <p class="mt-2 text-xs font-semibold text-primary">
                    — {{ theme('login_testimonial_author', 'Satisfied User from Lucent') }}
                </p>
            </div>
        </div>

        <div class="bg-background-secondary/50 backdrop-blur-lg rounded-xl w-full lg:w-1/2 p-12 flex items-center justify-center shadow-2xl border border-neutral/20">
        
            <div class="w-full max-w-md">
                <div class="mb-6 text-left">
                    <div class="flex items-center justify-between mb-6 lg:hidden">
                        @if (theme('show_full_logo', true))
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center group" wire:navigate>
                                <x-logo class="h-12 w-auto group-hover:scale-105 transition-transform duration-300" />
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 justify-center group" wire:navigate>
                                <x-logo class="h-10 w-auto group-hover:scale-105 transition-transform duration-300" />
                                <h1 class="text-2xl font-extrabold text-color-base">
                                    {{ config('app.name', 'AppName') }}
                                </h1>
                            </a>
                        @endif
                    </div>
                    
                    <h1 class="text-3xl font-bold text-color-base mb-2">
                        {{ __('auth.verification.notice') }}
                    </h1>
                    <p class="text-md text-color-muted">
                        {{ __('auth.verification.check_your_email') }}
                    </p>
                </div>
                
                <form wire:submit.prevent="submit" id="verify-email" class="space-y-6">
                    
                    <div class="p-4 rounded-xl bg-background/50 border border-neutral/10">
                        <p class="text-sm text-color-muted">
                            {{ __('auth.verification.not_received') }}
                        </p>
                    </div>

                    <div>
                        <x-captcha :form="'verify-email'" />
                    </div>

                    <x-button.primary 
                        class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 transition-all duration-200 transform hover:-translate-y-0.5" 
                        type="submit">
                        {{ __('auth.verification.request_another') }}
                    </x-button.primary>

                </form>

                <div class="mt-8 text-center border-t border-neutral/10 pt-6">
                     <button wire:click="logout" class="text-sm font-medium text-primary hover:text-secondary transition-colors">
                        {{ __('Log Out') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>