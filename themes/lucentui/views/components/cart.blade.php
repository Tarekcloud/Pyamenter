@if($cartCount > 0)
<div class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-primary/50 transition mr-1">
    <x-navigation.link :href="route('cart')" class="relative">
        <x-ri-shopping-cart-2-fill class="size-4" />
        <span class="absolute -top-1 -right-1 bg-primary text-white text-xs font-bold rounded-xl px-1.5 py-0.5">
            {{ $cartCount }}
        </span>
    </x-navigation.link>
</div>
@endif