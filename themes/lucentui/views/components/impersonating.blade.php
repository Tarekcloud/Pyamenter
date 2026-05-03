@if(session()->has('impersonating'))
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] w-auto max-w-[90%] animate-in slide-in-from-bottom-10 fade-in duration-500">
        
        <div class="flex items-center gap-3 pl-4 pr-2 py-2 rounded-full bg-background-secondary/95 backdrop-blur-md border border-neutral/10 shadow-2xl text-white ring-1 ring-neutral/50">
            
            <div class="flex items-center justify-center size-8 rounded-full bg-primary/20 text-primary shrink-0">
                <x-ri-spy-fill class="size-5" />
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-0.5 sm:gap-2 text-sm pr-2">
                <span class="text-white/60 font-medium">Viewing as:</span>
                <span class="font-bold text-white tracking-wide truncate max-w-[150px] sm:max-w-[200px]">
                    {{ auth()->user()->name }}
                </span>
            </div>

            <div class="h-6 w-px bg-white/10 mx-1"></div>

            <a href="/admin/users/{{ auth()->user()->id }}/edit" 
               class="group flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 hover:bg-red-500/20 hover:text-red-400 border border-neutral/5 transition-all duration-200">
                <x-ri-logout-box-r-line class="size-4" />
            </a>

        </div>
    </div>
@endif