<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">

    <h1 class="text-3xl lg:text-4xl font-bold mt-4 mb-2">
        {{ __('ticket.tickets') }}
    </h1>
    <p class="text-lg text-color-muted font-light max-w-2xl mb-8">
        Ticket #{{ $ticket->id }} - {{ $ticket->subject }}
    </p>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <div class="md:col-span-3 order-2 md:order-1">
            
            <div class="bg-background-secondary/50 border border-neutral p-6 rounded-xl shadow-lg mb-6">
                <div class="flex flex-col gap-6 max-h-[60vh] overflow-y-auto pr-4 custom-scrollbar" wire:poll.5s>
                    
                    @foreach ($ticket->messages()->with('user')->get() as $index => $message)
                        @php
                            $isMe = $message->user_id === auth()->id();
                        @endphp

                        <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }} gap-4 group"
                             @if ($loop->last) x-data x-init="$nextTick(() => $el.scrollIntoView({ block: 'end' }))" @endif>

                            @if(!$isMe)
                                <div class="flex-shrink-0">
                                    <img src="{{ $message->user->avatar }}" alt="{{ $message->user->name }}" class="w-10 h-10 rounded-full border-2 border-neutral/50 shadow-sm object-cover">
                                </div>
                            @endif

                            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} max-w-[80%]">
                                
                                <div class="flex items-center gap-2 mb-1 px-1">
                                    <span class="text-sm font-bold text-base">{{ $message->user->name }}</span>
                                    <span class="text-xs text-color-muted">{{ $message->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="p-4 rounded-2xl shadow-sm border
                                            {{ $isMe 
                                                ? 'bg-primary/10 border-primary/20 rounded-tr-none' 
                                                : 'bg-background/50 border-neutral/50 rounded-tl-none' 
                                            }}">
                                    
                                    <div class="prose dark:prose-invert max-w-none text-sm leading-relaxed">
                                        {!! Str::markdown($message->message, [
                                            'html_input' => 'escape',
                                            'allow_unsafe_links' => false,
                                            'renderer' => ['soft_break' => "<br>"]
                                        ]) !!}
                                    </div>

                                    @if($message->attachments->count() > 0)
                                        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-neutral/10">
                                            @foreach($message->attachments as $attachment)
                                                <a href="{{ route('tickets.attachments.show', $attachment) }}" 
                                                   target="_blank"
                                                   class="group/file flex items-center gap-2 text-xs bg-background p-2 rounded-lg border border-neutral/30 hover:border-primary/50 transition-colors">
                                                    @if($attachment->canPreview())
                                                        <img src="{{ route('tickets.attachments.show', $attachment) }}" alt="Preview" class="w-8 h-8 rounded object-cover">
                                                    @else
                                                        <div class="w-8 h-8 bg-neutral/10 rounded flex items-center justify-center text-color-muted group-hover/file:text-primary">
                                                            <x-ri-attachment-2 class="size-4" />
                                                        </div>
                                                    @endif
                                                    <span class="truncate max-w-[100px]">{{ $attachment->filename }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($isMe)
                                <div class="flex-shrink-0">
                                    <img src="{{ $message->user->avatar }}" alt="{{ $message->user->name }}" class="w-10 h-10 rounded-full border-2 border-primary/30 shadow-sm object-cover">
                                </div>
                            @endif

                        </div>
                    @endforeach

                </div>
            </div>

            <div class="bg-background-secondary/70 border border-neutral p-6 rounded-xl shadow-lg mt-6">
                <form wire:submit.prevent="save">
                    <label for="editor" class="block text-sm font-bold text-base mb-2">
                        {{ __('ticket.reply') }}
                    </label>
                    
                    <div wire:ignore> 
                        <textarea id="editor" class="form-input h-40"></textarea>
                    </div>
                    <x-easymde-editor />

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-color-muted mb-2">
                            {{ __('ticket.attachments') }}
                        </label>
                        <div x-data="{
                                drop: false,
                                selectedFiles: [],
                                handleDrop(event) {
                                    this.drop = false;
                                    if (event.dataTransfer.files && event.dataTransfer.files.length > 0) {
                                        this.selectedFiles = Array.from(event.dataTransfer.files);
                                        this.$refs.fileInput.files = event.dataTransfer.files;
                                        this.$refs.fileInput.dispatchEvent(new Event('change'));
                                    }
                                },
                                init() {
                                    this.$watch('$wire.attachments', (value) => {
                                        if (value.length == 0) this.selectedFiles = [];
                                    });
                                }
                            }">
                            
                            <div class="flex justify-center rounded-xl bg-background border-2 border-dashed border-neutral px-6 py-8 transition-colors"
                                 @dragover.prevent="drop = true" 
                                 @dragleave.prevent="drop = false"
                                 @drop.prevent="handleDrop($event)" 
                                 :class="{'border-primary bg-primary/5': drop, 'hover:bg-neutral/5': !drop}">
                                
                                <div class="text-center">
                                    <template x-if="selectedFiles.length === 0">
                                        <div>
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="p-3 bg-neutral/10 rounded-xl text-color-muted">
                                                    <x-ri-upload-cloud-2-line class="size-6" />
                                                </div>
                                                <label for="attachments" class="cursor-pointer">
                                                    <span class="font-bold text-primary hover:underline">{{ __('ticket.upload_attachments') }}</span>
                                                    <span class="text-color-muted">{{ __('ticket.or_drag_and_drop') }}</span>
                                                </label>
                                            </div>
                                            <p class="text-xs text-color-muted mt-2">{{ __('ticket.files_max') }}</p>
                                        </div>
                                    </template>

                                    <div x-show="selectedFiles.length > 0" class="w-full">
                                        <h4 class="text-sm font-semibold text-left mb-2">{{ __('ticket.selected_files') }}:</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="file in selectedFiles" :key="file.name">
                                                <div class="text-sm rounded-lg bg-background-secondary border border-neutral px-3 py-1.5 flex items-center gap-2 shadow-sm">
                                                    <x-ri-file-line class="size-4 text-primary" />
                                                    <span class="font-medium" x-text="file.name"></span>
                                                    <button type="button" class="text-red-500 hover:text-red-700 ml-1" @click="selectedFiles = selectedFiles.filter(f => f !== file)">
                                                        <x-ri-close-circle-fill class="size-4" />
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input id="attachments" type="file" multiple name="attachments[]" class="sr-only" wire:model.live="attachments" x-ref="fileInput" @change="selectedFiles = Array.from($event.target.files)" />
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end items-center border-t border-neutral/30 pt-4">
                        @if (!config('settings.ticket_client_closing_disabled', false) && $ticket->status !== 'closed')
                            <x-button.danger type="button" class="w-full sm:w-auto justify-center"
                                x-on:click.prevent="$store.confirmation.confirm({
                                    title: '{{ __('ticket.close_ticket') }}',
                                    message: '{{ __('ticket.close_ticket_confirmation') }}',
                                    confirmText: '{{ __('common.confirm') }}',
                                    cancelText: '{{ __('common.cancel') }}',
                                    callback: () => $wire.closeTicket()
                                })">
                                <x-ri-close-circle-line class="size-5 mr-2" />
                                {{ __('ticket.close_ticket') }}
                            </x-button.danger>
                        @endif
                        
                        <x-button.primary type="submit" class="w-full sm:w-auto justify-center items-center flex shadow-lg shadow-primary/20" wire:target="save">
                            <span class="flex items-center">
                                <x-loading wire:target="save" class="mr-2" />
                                <x-ri-send-plane-fill class="size-5 mr-2" wire:loading.remove wire:target="save" />
                                {{ __('ticket.reply') }}
                            </span>
                        </x-button.primary>
                    </div>
                </form>
            </div>
        </div>

        <div class="md:col-span-1 order-1 md:order-2">
            <div class="bg-background-secondary/50 border border-neutral p-6 rounded-xl shadow-lg sticky top-6">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <x-ri-information-line class="size-5 text-primary" />
                    {{ __('ticket.ticket_details') }}
                </h2>

                <div class="space-y-6"> 
                    <div class="group">
                        <span class="text-xs font-bold uppercase tracking-wider text-color-muted mb-1 block">{{ __('ticket.subject') }}</span>
                        <div class="text-sm font-medium leading-snug">{{ $ticket->subject }}</div>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-color-muted mb-2 block">{{ __('ticket.status') }}</span>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold border
                            @if ($ticket->status == 'open') text-success bg-success/10 border-success/20
                            @elseif($ticket->status == 'closed') text-gray-500 bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700
                            @elseif($ticket->status == 'replied') text-info bg-info/10 border-info/20
                            @else text-warning bg-warning/10 border-warning/20 
                            @endif">
                            
                            @if ($ticket->status == 'open')
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-xl bg-success opacity-75"></span>
                                  <span class="relative inline-flex rounded-xl h-2 w-2 bg-success"></span>
                                </span>
                                Open
                            @elseif($ticket->status == 'closed')
                                <x-ri-checkbox-circle-fill class="size-4" />
                                Closed
                            @elseif($ticket->status == 'replied')
                                <x-ri-chat-smile-2-fill class="size-4" />
                                Replied
                            @else
                                <x-ri-loader-2-fill class="size-4 animate-spin" />
                                {{ ucfirst($ticket->status) }}
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-color-muted mb-2 block">{{ __('ticket.priority') }}</span>
                        @php
                            $prio = strtolower($ticket->priority);
                        @endphp
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium border
                            @if ($prio == 'low') text-blue-500 bg-blue-500/10 border-blue-500/20
                            @elseif($prio == 'medium') text-yellow-500 bg-yellow-500/10 border-yellow-500/20
                            @elseif($prio == 'high') text-orange-500 bg-orange-500/10 border-orange-500/20
                            @elseif($prio == 'critical') text-red-500 bg-red-500/10 border-red-500/20
                            @endif">
                            
                            @if ($prio == 'low')
                                <x-ri-arrow-down-circle-line class="size-4" />
                            @elseif($prio == 'medium')
                                <x-ri-subtract-line class="size-4" />
                            @elseif($prio == 'high')
                                <x-ri-arrow-up-circle-line class="size-4" />
                            @elseif($prio == 'critical')
                                <x-ri-alarm-warning-fill class="size-4 animate-pulse" />
                            @endif
                            
                            {{ ucfirst($ticket->priority) }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-color-muted mb-1 block">{{ __('ticket.created_at') }}</span>
                            <span class="text-sm flex items-center gap-2">
                                <x-ri-calendar-line class="size-4 text-color-muted" />
                                {{ $ticket->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    </div>

                    @if ($ticket->department)
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-color-muted mb-1 block">{{ __('ticket.department') }}</span>
                            <span class="text-sm flex items-center gap-2">
                                <x-ri-building-4-line class="size-4 text-color-muted" />
                                {{ $ticket->department }}
                            </span>
                        </div>
                    @endif

                    @if ($ticket->service)
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-color-muted mb-1 block">{{ __('ticket.service') }}</span>
                            <a href="{{ route('services.show', $ticket->service) }}" class="text-sm font-semibold text-primary hover:underline flex items-center gap-2" wire:navigate>
                                <x-ri-server-line class="size-4" />
                                {{ $ticket->service->product->name }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>