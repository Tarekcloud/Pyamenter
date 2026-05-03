{!! $iconAssets !!}

<div class="my-6 overflow-hidden rounded-xl border border-neutral/50 bg-background-secondary/50 shadow-lg">
    <div class="flex items-center gap-4 border-b border-neutral/50 px-6 py-5">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-warning/10 text-warning shrink-0 shadow-sm border border-warning/10">
            <x-ri-key-2-line class="size-6" />
        </span>
        <div>
            <h2 class="text-lg font-bold text-color-base">Generate New Password</h2>
            <p class="mt-1 text-sm text-color-muted">Confirm the action to rotate the saved panel password.</p>
        </div>
    </div>

    <div class="px-6 py-6">
        <div class="rounded-xl border border-warning/30 bg-warning/10 px-5 py-4">
            <div class="flex items-start gap-3">
                <x-ri-alert-line class="size-5 text-warning mt-0.5 shrink-0" />
                <div class="space-y-2 text-sm text-warning">
                    <p class="font-bold">Regenerating the password immediately invalidates the old one.</p>
                    <ul class="list-disc space-y-1 pl-4 marker:text-warning/60 text-warning/90">
                        <li>Existing sessions signed in with the old password may be disconnected.</li>
                        <li>Share the new credentials securely once generation completes.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row mt-6">
            <a
                href="{{ $confirmUrl }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-primary/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary shadow-sm hover:shadow-md"
            >
                <x-ri-refresh-line class="size-5" />
                Generate Password
            </a>
            <a
                href="{{ $cancelUrl }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-neutral/50 bg-background/50 px-6 py-3 text-sm font-bold text-color-base transition hover:bg-background-secondary hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral shadow-sm"
            >
                <x-ri-arrow-go-back-line class="size-5" />
                Cancel
            </a>
        </div>
    </div>
</div>