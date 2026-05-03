<div class="space-y-3">
    @forelse ($services->take(3) as $service)
    <a href="{{ route('services.show', $service) }}" wire:navigate>
        <div class="group bg-gradient-to-br from-background-secondary/50 to-background-secondary/30 border border-neutral/50 p-4 rounded-lg transition-all hover:border-primary/50 hover:shadow-lg mb-2">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="bg-secondary/10 p-2 rounded-lg">
                        <x-ri-instance-line class="size-5 text-secondary" />
                    </div>
                    <div>
                        <p class="font-semibold text-base group-hover:text-primary transition-colors truncate" title="{{ $service->product->name }}">
                           {{ $service->product->name }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-base/80">{{ $service->plan->formattedPrice }}</span>
                    <div class="size-5 rounded-xl p-0.5
                        @if ($service->status == 'active') text-success bg-success/20
                        @elseif($service->status == 'suspended') text-inactive bg-inactive/20
                        @else text-warning bg-warning/20 @endif">
                        @if ($service->status == 'active') <x-ri-checkbox-circle-fill />
                        @elseif($service->status == 'suspended') <x-ri-forbid-fill />
                        @else <x-ri-error-warning-fill /> @endif
                    </div>
                </div>
            </div>
            <div class="pl-12 space-y-0.5">
                <p class="text-sm text-base/80">
                    {{ $service->product->category->name }}
                </p>
                <p class="text-xs text-base/60">
                    @if($service->expires_at)
                        {{ __('services.expires_at') }}: {{ $service->expires_at->format('d M Y') }}
                    @else
                        {{ Str::ucfirst($service->plan->billing_unit) }}ly Billing
                    @endif
                </p>
            </div>
        </div>
    </a>
    @empty
        <div class="text-center py-8">
            <div class="flex justify-center mb-3">
            <x-ri-inbox-line class="size-8 text-base/40" />
            </div>
            <p class="text-base/60">No Services Found</p>
        </div>
    @endforelse
</div>