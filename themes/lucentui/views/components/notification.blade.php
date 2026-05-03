<div x-data>
    <template x-for="(notification, index) in $store.notifications.notifications" :key="notification.id">
        <div x-show="notification.show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2" 
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed z-50 mb-4 mt-20 pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-background/20 backdrop-blur-md shadow-lg border border-neutral/50 ring-opacity-5"
            :style="'top: ' + (20 + index * 100) + 'px; right: 30px;'">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <x-ri-checkbox-circle-fill x-show="notification.type === 'success'" class="w-6 h-6 text-green-500" />
                        <x-ri-error-warning-fill x-show="notification.type === 'error'" class="w-6 h-6 text-red-500" />
                        <x-ri-alert-fill x-show="notification.type === 'warning'" class="w-6 h-6 text-yellow-500" />
                        <x-ri-information-fill x-show="notification.type === 'info'" class="w-6 h-6 text-blue-500" />
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-base">{{ __('account.notification') }}</p>
                        <p class="text-sm text-base/80" x-text="notification.message"></p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button @click="$store.notifications.removeNotification(notification.id)" 
                            type="button" 
                            class="inline-flex rounded-md bg-background text-base/90 hover:text-base focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <x-ri-close-line class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>