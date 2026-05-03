<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <x-navigation.breadcrumb class="mb-6" />

    <h1 class="text-3xl lg:text-4xl font-bold text-color-base mt-4 mb-8">
        {{ __('account.personal_details') }}
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <div class="lg:col-span-1">
            <div class="bg-background-secondary/70 border border-neutral/50 rounded-2xl p-6 shadow-xl relative overflow-hidden sticky top-24">
                <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-primary/20 to-secondary/20 blur-2xl -z-10"></div>

                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-6 group">
                        <div class="relative size-32 rounded-full p-1.5 bg-gradient-to-tr from-primary to-secondary bg-[length:200%_200%] animate-gradient-xy shadow-2xl">
                            <div class="size-full rounded-full overflow-hidden border-4 border-background-secondary bg-background">
                                <img src="{{ auth()->user()->avatar }}" class="size-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                        </div>
                        <div class="absolute bottom-2 right-2 size-6 bg-primary border-[3px] border-background-secondary rounded-full z-20 shadow-sm" title="Active"></div>
                    </div>

                    <h2 class="text-2xl font-black text-color-base tracking-tight mb-1">
                        {{ auth()->user()->name }}
                    </h2>
                    <p class="text-sm font-medium text-color-muted break-all mb-4">
                        {{ auth()->user()->email }}
                    </p>
                    
                    @if(auth()->user()->role_id == 1)
                        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-red-500/10 text-red-500 border border-red-500/20 shadow-sm mb-8">
                            <x-ri-shield-keyhole-line class="size-3.5 mr-1.5" />
                            {{ __('navigation.admin') }}
                        </div>
                    @else
                        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20 shadow-sm mb-8">
                            <x-ri-shield-user-line class="size-3.5 mr-1.5" />
                            Member
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 w-full mb-6">
                        <div class="bg-background/50 border border-neutral/30 p-3 rounded-xl flex flex-col items-center justify-center hover:bg-background/80 transition-colors">
                            <span class="text-xl font-bold text-color-base">{{ auth()->user()->services()->count() }}</span>
                            <span class="text-[10px] uppercase tracking-wider font-bold text-color-muted">{{ __('services.services') }}</span>
                        </div>
                        <div class="bg-background/50 border border-neutral/30 p-3 rounded-xl flex flex-col items-center justify-center hover:bg-background/80 transition-colors">
                            <span class="text-xl font-bold text-color-base">{{ auth()->user()->invoices()->count() }}</span>
                            <span class="text-[10px] uppercase tracking-wider font-bold text-color-muted">{{ __('invoices.invoices') }}</span>
                        </div>
                        <div class="bg-background/50 border border-neutral/30 p-3 rounded-xl flex flex-col items-center justify-center hover:bg-background/80 transition-colors">
                            <span class="text-xl font-bold text-color-base">{{ auth()->user()->tickets()->count() }}</span>
                            <span class="text-[10px] uppercase tracking-wider font-bold text-color-muted">{{ __('ticket.tickets') }}</span>
                        </div>
                        <div class="bg-background/50 border border-neutral/30 p-3 rounded-xl flex flex-col items-center justify-center hover:bg-background/80 transition-colors">
                            <span class="text-xl font-bold text-color-base">{{ auth()->user()->orders()->count() }}</span>
                            <span class="text-[10px] uppercase tracking-wider font-bold text-color-muted">Orders</span>
                        </div>
                    </div>

                    <div class="w-full h-px bg-neutral/50 mb-6"></div>

                    <div class="w-full space-y-4">
                        <div class="flex justify-between items-center group">
                            <span class="text-sm text-color-muted flex items-center">
                                <x-ri-calendar-line class="size-4 mr-2 opacity-70" /> Joined
                            </span>
                            <span class="text-sm font-semibold text-color-base">
                                {{ auth()->user()->created_at->format('d M Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center group">
                            <span class="text-sm text-color-muted flex items-center">
                                <x-ri-fingerprint-line class="size-4 mr-2 opacity-70" /> User ID
                            </span>
                            <span class="text-sm font-mono font-semibold text-color-base bg-neutral/30 px-2 py-0.5 rounded">
                                #{{ auth()->user()->id }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center group">
                            <span class="text-sm text-color-muted flex items-center">
                                <x-ri-global-line class="size-4 mr-2 opacity-70" /> Country
                            </span>
                            <span class="text-sm font-semibold text-color-base">
                                Indonesia
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-background-secondary/40 border border-neutral/50 rounded-2xl p-6 md:p-8 shadow-lg backdrop-blur-sm">
                
                <div class="flex items-center mb-8">
                    <div class="bg-primary/10 p-3 rounded-xl mr-4 text-primary shadow-inner">
                        <x-ri-edit-2-line class="size-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-color-base">Edit Details</h3>
                        <p class="text-sm text-color-muted">Update your personal information accurately.</p>
                    </div>
                </div>

                <form wire:submit.prevent="submit">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input name="first_name" type="text" 
                                :label="__('general.input.first_name')"
                                :placeholder="__('general.input.first_name_placeholder')" 
                                wire:model="first_name" required dirty 
                                class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary transition-all hover:bg-background/80" />
                            
                            <x-form.input name="last_name" type="text" 
                                :label="__('general.input.last_name')"
                                :placeholder="__('general.input.last_name_placeholder')" 
                                wire:model="last_name" required dirty 
                                class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary transition-all hover:bg-background/80" />
                        </div>

                        <x-form.input name="email" type="email" 
                            :label="__('general.input.email')"
                            :placeholder="__('general.input.email_placeholder')" 
                            required wire:model="email" dirty 
                            class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary transition-all hover:bg-background/80" />
                    </div>

                    @if($custom_properties || $properties)
                        <div class="mt-10 pt-8 border-t border-dashed border-neutral/50">
                            <div class="flex items-center mb-6">
                                <x-ri-settings-4-line class="size-5 text-color-muted mr-2" />
                                <h4 class="text-lg font-bold text-color-base">Additional Configuration</h4>
                            </div>
                            <div class="grid grid-cols-1 gap-6">
                                <x-form.properties :custom_properties="$custom_properties" :properties="$properties" dirty />
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end mt-10 pt-6 border-t border-neutral/50">
                        <x-button.primary 
                            type="submit" 
                            class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white transition-all duration-300 bg-primary rounded-xl shadow-lg shadow-primary/20 hover:bg-primary/90 hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary group"
                        >
                            <span class="flex items-center">
                                <x-ri-save-3-line class="size-5 mr-2 group-hover:rotate-12 transition-transform duration-300" />
                                {{ __('general.update') }}
                            </span>
                        </x-button.primary>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>