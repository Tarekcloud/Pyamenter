<div>
    <!-- In work, do what you enjoy. -->
    <x-dropdown width="w-84" :showArrow="false">
        <x-slot:trigger>
            <div class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-primary/50 transition" x-data="{ hasNew: false }" x-on:new-notification.window="hasNew = true"
                @click="hasNew = false">
                <x-ri-notification-3-fill class="size-4" ::class="{'animate-wiggle': hasNew}"/>
                @if($this->notifications->where('read_at', null)->count() > 0)
                <span
                    class="absolute top-0 right-0 w-4 h-4 inline-flex items-center justify-center text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                    {{ $this->notifications->where('read_at', null)->count() }}
                </span>
                @endif
            </div>
        </x-slot:trigger>
        <x-slot:content>
            <div class="flex flex-col max-h-96">
                <div class="flex items-center justify-between p-3 border-b border-neutral/50">
                    <span class="font-semibold text-base">{{ __('navigation.notifications') }}</span>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @if ($this->notifications->isEmpty())
                        <div class="p-6 flex flex-col items-center justify-center text-center text-base/70">
                            <x-ri-check-double-line class="size-10 mb-2 text-base/50" />
                            <span class="font-medium">{{ __('No new notifications') }}</span>
                        </div>
                    @else
                        @foreach ($this->notifications as $notification)

                            <div wire:click="goToNotification({{ $notification->id }})"
                                class="group relative flex items-start gap-3 p-4 cursor-pointer transition-colors
                                    @if (!$notification->read_at)
                                        bg-primary/10 hover:bg-primary/20
                                    @else
                                        hover:bg-neutral/50
                                    @endif
                                    @if (!$loop->last) border-b border-neutral/50 @endif">

                                <div class="flex-shrink-0 size-8 mt-1 rounded-full flex items-center justify-center
                                            @if (!$notification->read_at) bg-primary/20 text-primary @else bg-neutral/60 text-base/70 @endif">
                                    <x-ri-notification-3-fill class="size-4" />
                                </div>

                                <div class="flex-1">
                                    <span class="font-medium block">{{ $notification->title }}</span>
                                    <span class="text-sm text-base/80 block">{{ $notification->body }}</span>
                                    <p class="text-xs text-base/60 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                @if (!$notification->read_at)
                                    <button wire:click.stop="markAsRead({{ $notification->id }})"
                                        class="absolute top-3 right-3 size-7 flex items-center justify-center rounded-full
                                            text-base/70 bg-white dark:bg-neutral
                                            opacity-0 group-hover:opacity-100 transition-opacity
                                            hover:text-primary hover:bg-primary/10
                                            focus:outline-none focus:ring-2 focus:ring-primary/50"
                                        title="{{ __('Mark as read') }}">
                                        <x-ri-check-line class="size-4" />
                                    </button>
                                @endif

                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </x-slot:content>
    </x-dropdown>
</div>