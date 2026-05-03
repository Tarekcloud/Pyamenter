<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <x-navigation.breadcrumb class="mb-6" />

    <h1 class="text-3xl lg:text-4xl font-bold text-color-base mt-4 mb-8">
        {{ __('account.security') }}
    </h1>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8">
        <div class="flex items-center mb-6">
            <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-device-line class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.sessions') }}</h2>
                <p class="text-sm text-color-muted">Manage your active login sessions across all devices</p>
            </div>
        </div>

        <div class="space-y-4"> 
            @foreach (Auth::user()->sessions->filter(fn ($session) => !$session->impersonating()) as $session)
                <div class="bg-background/50 border border-neutral/50 rounded-xl p-4 transition-all duration-300 hover:scale-[1.01] hover:shadow-lg hover:border-primary/50">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="bg-primary/10 p-2 rounded-xl flex-shrink-0 shadow-sm mr-3">
                                <x-ri-computer-line class="size-4 text-primary" />
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-color-base mb-1">{{ $session->ip_address }}</p>
                                <p class="text-sm text-color-muted">{{ $session->formatted_device }} - {{ $session->last_activity->diffForHumans() }}</p>
                            </div>
                        </div>
                        <button 
                            wire:click="logoutSession('{{ $session->id }}')" 
                            class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 border border-red-500/50 text-sm font-medium rounded-xl shadow-sm text-red-700 bg-red-500/20 hover:bg-red-100/20 transition-colors duration-200"
                        >
                            <x-ri-logout-box-line class="size-4 mr-2" />
                            {{ __('account.logout_sessions') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-neutral/50">
            <div class="bg-primary/10 border border-neutral/50 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center mb-2">
                    <x-ri-smartphone-line class="size-6 text-primary mr-2" />
                    <span class="text-2xl font-bold text-primary">{{ Auth::user()->sessions->count() }}</span>
                </div>
                <p class="text-sm font-medium text-primary">Active Sessions</p>
            </div>
            
            <div class="bg-primary/10 border border-neutral/50 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center mb-2">
                    <x-ri-shield-check-line class="size-6 text-primary mr-2" />
                    <span class="text-2xl font-bold text-primary">Secure</span>
                </div>
                <p class="text-sm font-medium text-primary">Connection Status</p>
            </div>
            
            <div class="bg-warning/10 border border-neutral/50 rounded-xl p-4 text-center md:col-span-1 col-span-2">
                <div class="flex items-center justify-center mb-2">
                    <x-ri-time-line class="size-6 text-warning mr-2" />
                    <span class="text-2xl font-bold text-warning">{{ Auth::user()->sessions->first()?->last_activity->diffForHumans() ?? 'N/A' }}</span>
                </div>
                <p class="text-sm font-medium text-warning">Last Activity</p>
            </div>
        </div>
    </div>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8" style="animation-duration: 0.8s;">
        <div class="flex items-center mb-6">
            <div class="bg-warning/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-lock-password-line class="size-6 text-warning" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.change_password') }}</h2>
                <p class="text-sm text-color-muted">Update your password to keep your account secure</p>
            </div>
        </div>

        <form wire:submit="changePassword">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> 
                <x-form.input 
                    divClass="md:col-span-2" 
                    name="current_password" 
                    type="password"
                    :label="__('account.input.current_password')"
                    :placeholder="__('account.input.current_password_placeholder')" 
                    wire:model="current_password"
                    required 
                    class="bg-background/50 border-neutral/50 focus:ring-warning focus:border-warning" />
                <x-form.input 
                    name="password" 
                    :label="__('general.input.password')" 
                    :placeholder="__('general.input.password_placeholder')"
                    wire:model="password" 
                    required
                    class="bg-background/50 border-neutral/50 focus:ring-warning focus:border-warning" />
                <x-form.input 
                    name="password_confirmation" 
                    type="password" 
                    :label="__('account.input.confirm_password')"
                    :placeholder="__('account.input.confirm_password_placeholder')" 
                    wire:model="password_confirmation"
                    required 
                    class="bg-background/50 border-neutral/50 focus:ring-warning focus:border-warning" />
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-end mt-6">
                <button 
                    type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-warning hover:bg-warning/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warning transition-colors duration-200"
                >
                    <x-ri-save-line class="size-4 mr-2" />
                    {{ __('account.change_password') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg" style="animation-duration: 1.0s;">
        <div class="flex items-center mb-6">
            <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-shield-keyhole-line class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.two_factor_authentication') }}</h2>
                <p class="text-sm text-color-muted">
                    @if ($twoFactorEnabled)
                        Two-factor authentication is currently enabled for your account
                    @else  
                        Add an extra layer of security to your account
                    @endif
                </p>
            </div>
        </div>

        @if ($twoFactorEnabled)
            <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                            <x-ri-checkbox-circle-fill class="size-6 text-primary" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-primary">2FA Enabled</h3>
                            <p class="text-sm text-color-muted mt-1">{{ __('account.two_factor_authentication_enabled') }}</p>
                        </div>
                    </div>
                    <div class="bg-primary/10 border border-primary/20 rounded-full px-4 py-2">
                        <span class="text-sm font-medium text-primary">Protected</span>
                    </div>
                </div>
            </div>
            
            <button 
                wire:click="disableTwoFactor" 
                class="inline-flex items-center px-6 py-3 border border-red-300 text-base font-medium rounded-xl shadow-sm text-red-700 bg-red-50 hover:bg-red-100 transition-colors duration-200"
            >
                <x-ri-shield-cross-line class="size-4 mr-2" />
                {{ __('Disable two factor authentication') }}
            </button>
        @else
            <div class="bg-warning/5 border border-warning/20 rounded-xl p-6 mb-6">
                <div class="flex items-center">
                    <div class="bg-warning/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                        <x-ri-error-warning-line class="size-6 text-warning" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-warning">2FA Not Enabled</h3>
                        <p class="text-sm text-color-muted mt-1">{{ __('account.two_factor_authentication_description') }}</p>
                    </div>
                </div>
            </div>

            <button 
                wire:click="enableTwoFactor" 
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
            >
                <x-ri-shield-check-line class="size-4 mr-2" />
                {{ __('account.two_factor_authentication_enable') }}
            </button>

            @if ($showEnableTwoFactor)
                <x-modal :title="__('account.two_factor_authentication_enable')" open="true">
                    <div class="text-center">
                        <div class="bg-primary/10 p-4 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                            <x-ri-qr-code-line class="size-10 text-primary" />
                        </div>
                        
                        <p class="text-color-base mb-6">{{ __('account.two_factor_authentication_enable_description') }}</p>
                        
                        <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 mb-6">
                            <img src="{{ $twoFactorData['image'] }}" alt="QR code" class="w-64 h-64 border border-neutral/50 rounded-xl p-4 bg-white mx-auto mb-4" />
                            <div class="bg-primary/5 border border-primary/20 rounded-xl p-4">
                                <p class="text-color-muted text-sm mb-2">{{ __('account.two_factor_authentication_secret') }}</p>
                                <code class="text-color-base text-base font-mono bg-background-secondary px-3 py-1 rounded-lg">{{ $twoFactorData['secret'] }}</code>
                            </div>
                        </div>
                    </div>
                    
                    <form wire:submit="enableTwoFactor">
                        <x-form.input 
                            name="two_factor_code" 
                            type="text"
                            :label="__('account.input.two_factor_code')"
                            :placeholder="__('account.input.two_factor_code_placeholder')" 
                            wire:model="twoFactorCode"
                            required 
                            class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary" />
                        
                        <button 
                            type="submit" 
                            class="w-full mt-6 inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                        >
                            <x-ri-shield-check-line class="size-4 mr-2" />
                            {{ __('account.two_factor_authentication_enable') }}
                        </button>
                    </form>
                    
                    <x-slot name="closeTrigger">
                        <button wire:click="$set('showEnableTwoFactor', false)" class="text-color-muted hover:text-color-base transition-colors duration-200">
                            <x-ri-close-fill class="size-6" />
                        </button>
                    </x-slot>
                </x-modal>
            @endif
        @endif
    </div>
</div>