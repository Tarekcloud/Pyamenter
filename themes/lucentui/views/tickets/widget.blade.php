<div class="space-y-3">
    @forelse ($tickets->take(3) as $ticket)
    <a href="{{ route('tickets.show', $ticket) }}" wire:navigate>
        <div class="group bg-gradient-to-br from-background-secondary/50 to-background-secondary/30 border border-neutral/50 p-4 rounded-lg transition-all hover:border-primary/50 hover:shadow-lg mb-2">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="bg-secondary/10 p-2 rounded-lg">
                        <x-ri-ticket-line class="size-5 text-secondary" />
                    </div>
                    <div>
                        <p class="font-semibold text-base group-hover:text-primary transition-colors truncate" title="{{ $ticket->subject }}">
                            #{{ $ticket->id }} - {{ $ticket->subject }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-base/70">#{{ $ticket->id }}</span>
                    <div class="size-5 rounded-xl p-0.5
                        @if ($ticket->status == 'open') text-success bg-success/20
                        @elseif($ticket->status == 'closed') text-inactive bg-inactive/20
                        @else text-info bg-info/20 @endif">
                        @if ($ticket->status == 'open') <x-ri-add-circle-fill />
                        @elseif($ticket->status == 'closed') <x-ri-forbid-fill />
                        @else <x-ri-chat-smile-2-fill /> @endif
                    </div>
                </div>
            </div>
            <div class="pl-12 space-y-0.5">
                <p class="text-sm text-base/80">
                    {{ $ticket->department->name ?? 'No Department' }}
                </p>
                <p class="text-xs text-base/60">
                    {{ $ticket->messages()->orderBy('created_at', 'desc')->first()?->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
    </a>
    @empty
        <div class="text-center py-8">
            <div class="flex justify-center mb-3">
                <x-ri-inbox-line class="size-8 text-base/40" />
            </div>
            <p class="text-base/60">No Tickets Found</p>
        </div>
    @endforelse
</div>