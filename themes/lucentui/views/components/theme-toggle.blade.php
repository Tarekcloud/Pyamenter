<button 
    @click="darkMode = !darkMode" 
    type="button" 
    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-primary/50 transition mr-2"
    aria-label="Toggle Dark Mode"
>
    <template x-if="!darkMode">
        <x-ri-sun-fill class="size-4 text-base hover:text-yellow-500 hover:animate-spin transition-transform" />
    </template>
    <template x-if="darkMode">
        <x-ri-moon-fill class="size-4 text-base hover:text-primary transition-transform" />
    </template>
</button>
