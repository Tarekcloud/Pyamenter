@push('head')
    <title>You Got a Gift! - {{ $giftName }}</title>
    <meta name="description" content="{{ $giftDescription }}">
    <meta name="robots" content="noindex, nofollow">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="You Got a Gift! - {{ $giftName }}">
    <meta property="og:description" content="{{ $giftDescription }}">
    <meta property="og:url" content="{{ route('gifts.redeem.direct', ['code' => $code]) }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="You Got a Gift! - {{ $giftName }}">
    <meta name="twitter:description" content="{{ $giftDescription }}">
@endpush

<div class="container mt-14">
    <x-navigation.breadcrumb />
    <div class="px-2">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-2xl font-bold text-primary-100">You Got a Gift! 🎁</h3>
                <p class="text-sm text-base/70">Redirecting you to redeem your gift...</p>
            </div>
        </div>

        <div class="bg-background-secondary border border-neutral rounded-lg p-6">
            @if($gift)
                <div class="text-center py-8">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V5z" />
                            <path d="M3 13a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-primary-100 mb-2">{{ $giftName }}</h4>
                    @if($gift->description)
                        <p class="text-base/70 mb-4">{{ $gift->description }}</p>
                    @endif
                    <p class="text-sm text-base/70">Please wait while we redirect you...</p>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-base/70">Gift code not found. Redirecting...</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if(Auth::check())
    <script>
        setTimeout(function() {
            window.location.href = '{{ route('gifts.redeem.code', ['code' => $code]) }}';
        }, 1000);
    </script>
@else
    <script>
        setTimeout(function() {
            window.location.href = '{{ route('login') }}';
        }, 1000);
    </script>
@endif
