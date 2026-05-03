{!! $iconAssets !!}

<div class="my-6 overflow-hidden rounded-xl border border-neutral/50 bg-background-secondary/50 shadow-lg">
    <!-- Header -->
    <div class="flex items-center gap-4 border-b border-neutral/50 px-4 py-4 md:px-6 md:py-5">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary shrink-0 shadow-sm border border-primary/10">
            <x-ri-server-line class="size-6" />
        </span>
        <div>
            <h2 class="text-lg font-bold text-color-base">Server Credentials</h2>
            <p class="mt-1 mb-0 text-sm text-color-muted">Everything you need to reach your Pterodactyl panel.</p>
        </div>
    </div>

    <div class="space-y-6 px-4 py-4 md:px-6 md:py-6">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <!-- Server Name -->
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-color-muted/80 ml-1">Server Name</label>
                <div class="flex overflow-hidden rounded-xl border border-neutral/50 bg-background shadow-sm focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary transition-all group">
                    <input
                        type="text"
                        value="{{ $serverName }}"
                        data-copy-value="{{ $serverName }}"
                        readonly
                        class="h-12 w-full border-none bg-transparent px-4 text-sm font-semibold text-color-base focus:outline-none focus:ring-0 placeholder-muted/50"
                    >
                    <button
                        type="button"
                        onclick="window.pterodactylSSO.copy(this.previousElementSibling.dataset.copyValue)"
                        aria-label="Copy server name"
                        class="flex h-12 w-12 cursor-pointer items-center justify-center border-l border-neutral/50 bg-neutral/5 text-color-muted group-hover:text-primary group-hover:bg-primary/5 transition-all"
                    >
                        <x-ri-file-copy-2-line class="size-5" />
                    </button>
                </div>
            </div>

            <!-- Server ID -->
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-color-muted/80 ml-1">Server ID</label>
                <div class="flex overflow-hidden rounded-xl border border-neutral/50 bg-background shadow-sm focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary transition-all group">
                    <input
                        type="text"
                        value="{{ $serverIdentifier }}"
                        data-copy-value="{{ $serverIdentifier }}"
                        readonly
                        class="h-12 w-full border-none bg-transparent px-4 text-sm font-semibold text-color-base focus:outline-none focus:ring-0 placeholder-muted/50"
                    >
                    <button
                        type="button"
                        onclick="window.pterodactylSSO.copy(this.previousElementSibling.dataset.copyValue)"
                        aria-label="Copy server identifier"
                        class="flex h-12 w-12 cursor-pointer items-center justify-center border-l border-neutral/50 bg-neutral/5 text-color-muted group-hover:text-primary group-hover:bg-primary/5 transition-all"
                    >
                        <x-ri-file-copy-2-line class="size-5" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Email -->
        <div class="space-y-2">
            <label class="text-xs font-bold uppercase tracking-wider text-color-muted/80 ml-1">Email Address</label>
            <div class="flex overflow-hidden rounded-xl border border-neutral/50 bg-background shadow-sm focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary transition-all group">
                <input
                    type="text"
                    value="{{ $userEmail }}"
                    data-copy-value="{{ $userEmail }}"
                    readonly
                    class="h-12 w-full border-none bg-transparent px-4 text-sm font-semibold text-color-base focus:outline-none focus:ring-0 placeholder-muted/50"
                >
                <button
                    type="button"
                    onclick="window.pterodactylSSO.copy(this.previousElementSibling.dataset.copyValue)"
                    aria-label="Copy email"
                    class="flex h-12 w-12 cursor-pointer items-center justify-center border-l border-neutral/50 bg-neutral/5 text-color-muted group-hover:text-primary group-hover:bg-primary/5 transition-all"
                >
                    <x-ri-file-copy-2-line class="size-5" />
                </button>
            </div>
        </div>

        <!-- Access Info Alert -->
        <div class="rounded-xl border border-primary/20 bg-primary/5 px-5 py-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 opacity-10 pointer-events-none">
                <x-ri-lock-password-line class="size-24 text-primary" />
            </div>
            
            <div class="relative z-10">
                <h3 class="text-sm font-bold text-primary flex items-center gap-2 mb-3">
                    <x-ri-information-fill class="size-5" />
                    Access Information
                </h3>
                
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-sm">
                        <span class="text-color-muted font-medium">Panel URL:</span>
                        <a href="{{ $panelUrl }}" target="_blank" class="font-bold text-primary hover:text-primary/80 underline-offset-4 hover:underline inline-flex items-center gap-1 transition-colors">
                            {{ $panelUrl }} <x-ri-external-link-line class="size-3.5" />
                        </a>
                    </div>
                    
                    <p class="text-sm text-color-base/80 leading-relaxed max-w-2xl">
                        Panel passwords are no longer stored here. Please use <strong class="text-primary">Auto Login</strong> for instant access or generate a fresh password via the panel if required.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        window.pterodactylSSO = window.pterodactylSSO || {
            copy(value) {
                if (!navigator.clipboard || !navigator.clipboard.writeText) {
                    return window.pterodactylSSO.fallbackCopy(value);
                }

                navigator.clipboard.writeText(value).then(() => {
                    window.pterodactylSSO.notify('Copied to clipboard');
                }).catch(() => window.pterodactylSSO.fallbackCopy(value));
            },
            fallbackCopy(value) {
                const area = document.createElement('textarea');
                area.value = value;
                area.style.position = 'fixed';
                area.style.left = '-9999px';
                document.body.appendChild(area);
                area.focus();
                area.select();
                try {
                    document.execCommand('copy');
                    window.pterodactylSSO.notify('Copied to clipboard');
                } catch (err) {
                    window.pterodactylSSO.notify('Copy failed, copy manually', true);
                }
                document.body.removeChild(area);
            },
            notify(message, isError = false) {
                if (window.Alpine && Alpine.store && Alpine.store('notifications')) {
                    Alpine.store('notifications').addNotification([{ message, type: isError ? 'error' : 'success' }]);
                    return;
                }
                if (isError) {
                    console.error(message);
                } else {
                    console.log(message);
                }
            }
        };
    </script>
@endonce