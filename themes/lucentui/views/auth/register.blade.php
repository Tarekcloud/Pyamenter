<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto flex h-full px-4">

        <div class="hidden lg:flex w-full lg:w-1/2 p-16 flex-col justify-center items-center text-center">
            <h2 class="text-4xl font-extrabold text-color-base mb-4 leading-tight">
                {{ theme('register_title', 'Join Our Community!') }}
            </h2>
            <p class="text-xl text-color-muted font-light max-w-md">
                {{ theme('register_subtitle', 'Save hundreds of hours building and developing dashboards from scratch.') }}
            </p>

            <div class="mt-12 p-6 bg-primary/10 dark:bg-primary/20 rounded-xl shadow-lg backdrop-blur-sm border border-primary/20">
                <p class="text-sm italic text-color-base">
                    {{ theme('register_testimonial_text', 'This product changed the way our service works. Very intuitive and fast!') }}
                </p>
                <p class="mt-2 text-xs font-semibold text-primary">
                    — {{ theme('register_testimonial_author', 'Satisfied User from Lucent') }}
                </p>
            </div>
        </div>

        <div class="bg-background-secondary/20 backdrop-blur-md backdrop-blur-lg rounded-xl w-full lg:w-1/2 p-8 md:p-12 flex items-center justify-center shadow-2xl border border-neutral/20">
            
            <div class="w-full max-w-md" 
                 x-data="{ 
                    step: 1,
                    totalSteps: 3,
                    nextStep() { 
                        const currentStepEl = this.$refs['step' + this.step];
                        const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
                        let valid = true;
                        
                        inputs.forEach(input => {
                            input.setCustomValidity('');
                            if (!input.checkValidity()) {
                                input.reportValidity();
                                valid = false;
                            }
                        });

                        if (this.step === 2 && valid) {
                            const pwd = currentStepEl.querySelector('input[wire\\:model=\'password\']');
                            const pwdConf = currentStepEl.querySelector('input[wire\\:model=\'password_confirmation\']');
                            if (pwd && pwdConf && pwd.value !== pwdConf.value) {
                                pwdConf.setCustomValidity('{{ __('Passwords do not match') }}');
                                pwdConf.reportValidity();
                                valid = false;
                            }
                        }
                        
                        if (valid && this.step < this.totalSteps) {
                            this.step++;
                        }
                    },
                    prevStep() { 
                        if (this.step > 1) {
                            this.step--; 
                        }
                    }
                 }">
                
                <div class="mb-8 text-left flex justify-between items-end">
                    <div>
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
                            {{ __('auth.sign_up_title') }}
                        </h1>
                        <p class="text-md text-color-muted">
                            Create your account to get started.
                        </p>
                    </div>
                    
                    <div class="text-sm font-medium text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                        Step <span x-text="step"></span>
                    </div>
                </div>
 
 
 
 @if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 backdrop-blur-sm">
        <div class="flex items-center gap-2 mb-2">
            <x-ri-error-warning-line class="size-5 text-red-500" />
            <h3 class="text-sm font-bold text-red-500">{{ __('Whoops! Something went wrong.') }}</h3>
        </div>
        <ul class="list-disc pl-5 text-sm text-red-400 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif



                <form wire:submit="submit" id="register" class="relative">
                    
                    <div x-show="step === 1" x-ref="step1" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200 absolute w-full top-0"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-4">
                        <div class="space-y-4">
                            <x-form.input 
                                name="first_name" type="text" 
                                :label="__('general.input.first_name')" :placeholder="__('general.input.first_name_placeholder')" 
                                wire:model="first_name" required 
                                class="w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <x-form.input 
                                name="last_name" type="text" 
                                :label="__('general.input.last_name')" :placeholder="__('general.input.last_name_placeholder')" 
                                wire:model="last_name" required 
                                class="w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                        </div>
                        <button type="button" @click="nextStep" class="group mt-6 w-full py-3 px-4 rounded-xl bg-primary text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center">
                            Continue
                            <x-ri-arrow-right-line class="size-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" />
                        </button>
                    </div>

                    <div x-show="step === 2" x-ref="step2" x-cloak
                         x-transition:enter="transition ease-out duration-300 delay-100"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200 absolute w-full top-0"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-4">
                        <div class="space-y-4">
                            <x-form.input 
                                name="email" type="email" 
                                :label="__('general.input.email')" :placeholder="__('general.input.email_placeholder')" 
                                wire:model="email" required 
                                class="w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <x-form.input 
                                name="password" type="password" 
                                :label="__('general.input.password')" :placeholder="__('general.input.password_placeholder')" 
                                wire:model="password" required 
                                class="w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <x-form.input 
                                name="password_confirmation" type="password" 
                                :label="__('general.input.password_confirmation')" :placeholder="__('general.input.password_confirmation_placeholder')" 
                                wire:model="password_confirmation" required 
                                class="w-full px-4 py-3 rounded-xl border border-neutral/30 bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="prevStep" class="w-1/3 py-3 px-4 rounded-xl bg-background border border-neutral/30 text-color-base font-semibold transition-all hover:bg-background-secondary">
                                Back
                            </button>
                            <button type="button" @click="nextStep" class="group w-2/3 py-3 px-4 rounded-xl bg-primary text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center">
                                Continue
                                <x-ri-arrow-right-line class="size-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" />
                            </button>
                        </div>
                    </div>

                    <div x-show="step === 3" x-ref="step3" x-cloak
                         x-transition:enter="transition ease-out duration-300 delay-100"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200 absolute w-full top-0"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-4">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                <x-form.properties :custom_properties="$custom_properties" :properties="$properties" />
                            </div>

                            @if(config('settings.tos'))
                                <x-form.checkbox wire:model="tos" name="tos" required class="text-sm text-color-base">
                                    {{ __('product.tos') }}
                                    <a href="{{ config('settings.tos') }}" target="_blank" class="font-medium text-primary hover:text-secondary">
                                        {{ __('product.tos_link') }}
                                    </a>
                                </x-form.checkbox>
                            @endif
                            
                            <x-captcha :form="'register'" />
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="prevStep" class="w-1/3 py-3 px-4 rounded-xl bg-background border border-neutral/30 text-color-base font-semibold transition-all hover:bg-background-secondary">
                                Back
                            </button>
                            <button type="submit" class="group w-2/3 py-3 px-4 rounded-xl bg-primary text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center">
                                {{ __('auth.sign_up') }}
                                <x-ri-check-line class="size-4 ml-2 transition-transform duration-200 group-hover:scale-110" />
                            </button>
                        </div>
                    </div>

                </form>

                <div class="mt-8 text-center" x-show="step === 1">
                    <p class="text-sm text-color-muted">
                        {{ __("auth.already_have_account") }}
                        <a class="font-medium text-primary hover:text-secondary transition-colors ml-1" 
                            href="{{ route('login') }}"
                            wire:navigate>
                            {{ __('auth.sign_in') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>