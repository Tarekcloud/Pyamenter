<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <h1 class="text-3xl lg:text-4xl font-bold mt-4 mb-8">
        {{ __('ticket.create_ticket') }}
    </h1>

    <div class="rounded-xl border border-neutral bg-background-secondary shadow-sm p-6 md:p-8">
        <form wire:submit.prevent="create" wire:ignore>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-sm font-medium text-foreground ml-1">
                        {{ __('ticket.subject') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="subject" required
                        class="w-full px-4 py-3 bg-background border border-neutral rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-base/30"
                        placeholder="Briefly describe your issue...">
                    @error('subject') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                </div>

                @if (count($departments) > 0)
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-foreground ml-1">
                        {{ __('ticket.department') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select wire:model="department" required
                            class="appearance-none w-full px-4 py-3 bg-background border border-neutral rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all cursor-pointer">
                            <option value="">{{ __('ticket.select_department') }}</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-base/50">
                            <x-ri-arrow-down-s-line class="size-5" />
                        </div>
                    </div>
                    @error('department') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                </div>
                @endif

                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-foreground ml-1">
                        {{ __('ticket.priority') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select wire:model="priority" required
                            class="appearance-none w-full px-4 py-3 bg-background border border-neutral rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all cursor-pointer">
                            <option value="">{{ __('ticket.select_priority') }}</option>
                            <option value="low">{{ __('ticket.low') }}</option>
                            <option value="medium">{{ __('ticket.medium') }}</option>
                            <option value="high">{{ __('ticket.high') }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-base/50">
                            <x-ri-arrow-down-s-line class="size-5" />
                        </div>
                    </div>
                    @error('priority') <span class="text-xs text-red-500 ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-sm font-medium text-foreground ml-1">{{ __('ticket.service') }}</label>
                    <div class="relative">
                        <select wire:model="service"
                            class="appearance-none w-full px-4 py-3 bg-background border border-neutral rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all cursor-pointer">
                            <option value="">{{ __('ticket.select_service') }}</option>
                            @foreach ($services as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->product->name }} ({{ ucfirst($product->status) }})
                                    {{ $product->expires_at ? ' - Ends ' . $product->expires_at->format('Y-m-d') : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-base/50">
                            <x-ri-arrow-down-s-line class="size-5" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="mb-6 space-y-1.5">
                <label for="editor" class="text-sm font-medium text-foreground ml-1">
                    {{ __('ticket.reply') }} <span class="text-red-500">*</span>
                </label>
                <div class="[&_.EasyMDEContainer]:rounded-xl [&_.CodeMirror]:rounded-xl [&_.CodeMirror]:border-neutral [&_.editor-toolbar]:border-neutral [&_.editor-toolbar]:opacity-70 hover:[&_.editor-toolbar]:opacity-100">
                    <textarea id="editor" placeholder="Type your message here..."></textarea>
                </div>
            </div>

            <div class="mb-8">
                <label class="text-sm font-medium text-foreground ml-1 mb-2 block">
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
                    <div class="relative flex flex-col items-center justify-center w-full min-h-[120px] rounded-xl border-2 border-dashed border-neutral bg-background hover:bg-neutral/5 hover:border-primary/50 transition-all duration-300 cursor-pointer group"
                         @dragover.prevent="drop = true" 
                         @dragleave.prevent="drop = false"
                         @drop.prevent="handleDrop($event)" 
                         :class="{'border-primary bg-primary/5 ring-2 ring-primary/20': drop}"
                         @click="$refs.fileInput.click()">
                        
                        <div class="text-center p-6" x-show="selectedFiles.length === 0">
                            <div class="mx-auto bg-neutral/10 rounded-xl w-12 h-12 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <x-ri-upload-cloud-2-line class="size-6 text-primary" />
                            </div>
                            <p class="text-sm font-semibold text-foreground">
                                {{ __('ticket.upload_attachments') }}
                            </p>
                            <p class="text-xs text-base/50 mt-1">
                                {{ __('ticket.or_drag_and_drop') }} <span class="hidden md:inline">&bull; {{ __('ticket.files_max') }}</span>
                            </p>
                        </div>

                        <div x-show="selectedFiles.length > 0" class="w-full p-4 flex flex-wrap gap-2 justify-center" x-cloak>
                            <template x-for="file in selectedFiles" :key="file.name">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-background-secondary border border-neutral rounded-lg shadow-sm">
                                    <x-ri-file-line class="size-4 text-primary" />
                                    <span class="text-xs font-medium text-foreground truncate max-w-[150px]" x-text="file.name"></span>
                                    <button type="button" class="text-base/40 hover:text-red-500 transition-colors"
                                            @click.stop="selectedFiles = selectedFiles.filter(f => f !== file); $refs.fileInput.value = '';">
                                        <x-ri-close-circle-fill class="size-4" />
                                    </button>
                                </div>
                            </template>
                        </div>

                        <input id="attachments" type="file" multiple name="attachments[]" class="hidden"
                               wire:model.live="attachments" x-ref="fileInput"
                               @change="selectedFiles = Array.from($event.target.files)" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-neutral/50">
                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center px-8 py-3 rounded-xl bg-primary font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary/90 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <x-ri-send-plane-fill class="size-4 mr-2" wire:loading.remove />
                    <x-ri-loader-4-line class="size-4 mr-2 animate-spin" wire:loading />
                    <span>{{ __('ticket.create') }}</span>
                </button>
            </div>

        </form>
    </div>

    <x-easymde-editor />
</div>