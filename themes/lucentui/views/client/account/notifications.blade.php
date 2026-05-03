<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <x-navigation.breadcrumb class="mb-6" />

    <h1 class="text-3xl lg:text-4xl font-extrabold text-color-base mt-4 mb-8">
        {{ __('account.notification') }}
    </h1>
    
    @if($this->supportsPush())
    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8" x-data="pushNotifications">
        
        <div class="flex items-center mb-6">
            <div class="bg-primary/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-notification-4-line class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.push_notifications') }}</h2>
                <p class="text-sm text-color-muted">{{ __('account.push_notifications_description') }}</p>
            </div>
        </div>

        <div class="pt-6 border-t border-neutral/50">
            <x-button.primary type="button" class="!w-fit" @click="subscribe"
                x-bind:disabled="subscriptionStatus !== 'not_subscribed'">
                
                <x-ri-notification-line class="size-5 mr-2" />
                
                {{ __('account.enable_push_notifications') }}
            </x-button.primary>
            
            <div x-show="subscriptionStatus !== 'unknown'" class="mt-4">
                <template x-if="subscriptionStatus === 'not_supported'">
                    <p class="text-sm text-red-600">{{ __('account.push_status.not_supported') }}</p>
                </template>
                <template x-if="subscriptionStatus === 'denied'">
                    <p class="text-sm text-red-600">{{ __('account.push_status.denied') }}</p>
                </template>
                <template x-if="subscriptionStatus === 'subscribed'">
                    <p class="text-sm text-green-600">{{ __('account.push_status.subscribed') }}</p>
                </template>
            </div>
        </div>
    </div>

    @script
    <script>
        Alpine.data('pushNotifications', () => ({
            subscriptionStatus: 'unknown',

            init() {
                console.log(this.subscriptionStatus)
                if ('serviceWorker' in navigator && 'PushManager' in window) {
                    navigator.serviceWorker.ready.then((registration) => {
                        registration.pushManager.getSubscription().then((subscription) => {
                            if (subscription) {
                                this.subscriptionStatus = 'subscribed';
                            } else {
                                this.subscriptionStatus = Notification.permission === 'denied' ? 'denied' : 'not_subscribed';
                            }
                        });
                    });
                } else {
                    this.subscriptionStatus = 'not_subscribed';
                }
            },

            subscribe() {
                if ('serviceWorker' in navigator && 'PushManager' in window) {
                    navigator.serviceWorker.ready.then((registration) => {
                        registration.pushManager.getSubscription().then((subscription) => {
                            if (subscription) {
                                @this.call('storePushSubscription', JSON.stringify(subscription));
                                this.subscriptionStatus = 'subscribed';
                                return;
                            }

                            registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array('{{ config('settings.vapid_public_key') }}')
                            }).then((newSubscription) => {
                                @this.call('storePushSubscription', JSON.stringify(newSubscription));
                                this.subscriptionStatus = 'subscribed';
                            }).catch((e) => {
                                if (Notification.permission === 'denied') {
                                    this.subscriptionStatus = 'denied';
                                } else {
                                    console.error('Failed to subscribe the user: ', e);
                                    this.subscriptionStatus = 'not_subscribed';
                                }
                            });
                        });
                    });
                } else {
                    this.subscriptionStatus = 'not_supported';
                }
            }
        }));

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

    </script>
    @endscript
    @endif

    <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg">
        
        <div class="flex items-center mb-6">
            <div class="bg-success/10 p-3 rounded-xl flex-shrink-0 shadow-sm mr-4">
                <x-ri-list-settings-line class="size-6 text-success" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-color-base">{{ __('account.notification') }}</h2>
                <p class="text-sm text-color-muted">{{ __('account.notifications_description') }}</p>
            </div>
        </div>
        
        <div class="pt-6 border-t border-neutral/50">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral/20">
                            <th class="text-left py-4 px-6 text-primary font-medium">
                                {{ __('account.notification') }}
                            </th>
                            <th class="text-center py-4 px-4 text-primary font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <x-ri-mail-line class="size-4" />
                                    <span>{{ __('account.email_notifications') }}</span>
                                </div>
                            </th>
                            <th class="text-center py-4 px-4 text-primary font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <x-ri-notification-line class="size-4" />
                                    <span>{{ __('account.in_app_notifications') }}</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody x-data="{ preferences: $wire.entangle('preferences') }">
                        @foreach($this->notifications as $notification)
                        <tr class="border-b border-neutral/10 hover:bg-background/50 transition-colors">
                            <td class="py-4 px-6 text-base/70">
                                {{ $notification->name }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex justify-center items-center">
                                    <x-form.toggle :disabled="!$notification->mail_controllable"
                                        wire:model.defer="preferences.{{ $notification->key }}.mail_enabled" />
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex justify-center items-center">
                                    <x-form.toggle :disabled="!$notification->in_app_controllable"
                                        wire:model.defer="preferences.{{ $notification->key }}.in_app_enabled" />
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-8">
                <x-button.primary wire:click="savePreferences" class="!w-fit" wire:loading.attr="disabled">
                    <x-loading wire:loading wire:target="savePreferences" />
                    <span wire:loading.remove wire:target="savePreferences">
                        {{ __('general.save') }}
                    </span>
                </x-button.primary>
            </div>
        </div>
    </div>
</div>