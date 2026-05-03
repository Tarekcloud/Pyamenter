<div>
    <button wire:click="logout" class="group flex flex-row items-center p-3 gap-2 text-sm font-semibold text-error/80 rounded-xl hover:text-error bg-transparent hover:bg-error/5 transition-colors duration-200">
        <x-ri-logout-box-line class="w-5 h-5 shrink-0 text-error/80" aria-hidden="true" />
            <span>{{ __('auth.logout') }}</span>
        <x-ri-arrow-right-s-line class="w-4 h-4 ml-1 text-error/60 transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true" />
    </button>
</div>