<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto flex h-full px-4">

        <div class="hidden lg:flex w-full lg:w-1/2 p-16 flex-col justify-center items-center text-center">
            <h2 class="text-4xl font-extrabold text-color-base mb-4 leading-tight">
                {{ theme('login_title', 'Join Our Community!') }}
            </h2>
            <p class="text-xl text-color-muted font-light max-w-md">
                {{ theme('login_subtitle', 'Secure your account with Two-Factor Authentication.') }}
            </p>

            <div class="mt-12 p-6 bg-primary/10 dark:bg-primary/20 rounded-xl shadow-2xl backdrop-blur-sm border border-primary/20">
                <p class="text-sm italic text-color-base">
                    {{ theme('login_testimonial_text', 'Security is our top priority.') }}
                </p>
                <p class="mt-2 text-xs font-semibold text-primary">
                    — {{ theme('login_testimonial_author', 'System Security') }}
                </p>
            </div>
        </div>

        <div class="bg-background-secondary/50 backdrop-blur-lg rounded-xl w-full lg:w-1/2 p-12 flex items-center justify-center shadow-2xl border border-neutral/20">
        
            <div class="w-full max-w-md">
                <div class="mb-6 text-left">
                    <div class="flex items-center justify-between mb-6 lg:hidden">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 justify-center group" wire:navigate>
                            <x-logo class="h-10 w-auto group-hover:scale-105 transition-transform duration-300" />
                            <h1 class="text-2xl font-extrabold text-color-base">
                                {{ config('app.name', 'AppName') }}
                            </h1>
                        </a>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-color-base mb-2">
                        {{ __('auth.verify_2fa') }}
                    </h1>
                    <p class="text-md text-color-muted">
                        Please enter the authentication code from your app to continue.
                    </p>
                </div>
                
                <form wire:submit="verify" class="space-y-6">
                    <div>
                        <x-form.input 
                            name="code" 
                            type="text" 
                            :label="__('account.input.two_factor_code')"
                            :placeholder="__('000 000')"
                            wire:model="code"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-center tracking-[0.5em] font-mono text-lg" 
                        />
                        </div>

                    @if (session('error'))
                        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <x-button.primary 
                        class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 transition-all duration-200 transform hover:-translate-y-0.5" 
                        type="submit">
                        {{ __('auth.verify') }}
                    </x-button.primary>
                </form>

                <div class="mt-8 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-color-muted hover:text-primary transition-colors cursor-pointer">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>