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
                <div class="mb-4 text-left">
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
                        {{ __('auth.sign_in_title') }}
                    </h1>
                    <p class="text-md text-color-muted">
                        Enter your email and password to continue.
                    </p>
                </div>
                
                <form wire:submit="submit" id="login" class="space-y-6">
                    
                    <div>
                        <x-form.input 
                            name="email" 
                            type="email" 
                            :label="__('general.input.email')"
                            :placeholder="__('general.input.email_placeholder')" 
                            wire:model="email" 
                            hideRequiredIndicator 
                            required 
                            class="-mb-8 w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    
                    <div>
                        <x-form.input 
                            name="password" 
                            type="password" 
                            :label="__('general.input.password')"
                            :placeholder="__('general.input.password_placeholder')" 
                            required 
                            hideRequiredIndicator 
                            wire:model="password"
                            class="w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <x-form.checkbox 
                            name="remember" 
                            label="Keep me logged in" 
                            wire:model="remember"
                            class="text-sm text-color-base" />
                        <a class="text-sm font-medium text-primary hover:text-secondary transition-colors"
                            href="{{ route('password.request') }}">
                            {{ __('auth.forgot_password') }}
                        </a>
                    </div>

                    <div>
                        <x-captcha :form="'login'" />
                    </div>

                    <x-button.primary 
                        class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary/90 text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 transition-all duration-200 transform hover:-translate-y-0.5" 
                        type="submit">
                        {{ __('auth.sign_in') }}
                    </x-button.primary>

                </form>

                @if (config('settings.oauth_github') || config('settings.oauth_google') || config('settings.oauth_discord'))
                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-neutral/30"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-background-secondary/50 text-color-muted font-medium">
                                {{ __('auth.or_sign_in_with') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-{{ collect(['github', 'google', 'discord'])->filter(fn($p) => config('settings.oauth_' . $p))->count() > 1 ? '2' : '1' }} gap-4">
                        @foreach (['github', 'google', 'discord'] as $provider)
                            @if (config('settings.oauth_' . $provider))
                            <a href="{{ route('oauth.redirect', $provider) }}"
                                class="flex items-center justify-center px-4 py-3 rounded-xl border border-neutral/30 bg-background hover:bg-background-tertiary/50 text-color-base font-medium transition-all duration-200 hover:shadow-md space-x-2">
                                <img src="/assets/images/{{ $provider }}-light.svg" alt="{{ ucfirst($provider) }}"
                                    class="w-5 h-5 block dark:hidden">
                                <img src="/assets/images/{{ $provider }}-dark.svg" alt="{{ ucfirst($provider) }}"
                                    class="w-5 h-5 hidden dark:block">
                                <span class="text-sm font-medium">Sign in with {{ ucfirst($provider) }}</span>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!config('settings.registration_disabled', false))
                <div class="mt-8 text-center">
                    <p class="text-sm text-color-muted">
                        {{ __("auth.dont_have_account") }}
                        <a class="font-medium text-primary hover:text-secondary transition-colors ml-1" 
                            href="{{ route('register') }}"
                            wire:navigate>
                            {{ __('auth.sign_up') }}
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>