<footer class="w-full rounded-xl bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-xl mt-4">
    <div class="mx-auto px-10 py-10">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex flex-col space-y-4 max-w-md">
                <div class="flex items-center gap-3">
                    <x-logo class="h-12 w-auto" />
                    @unless(theme('show_full_logo'))
                        <h3 class="text-lg font-bold text-base">{{ config('app.name') }}</h3>
                    @endunless
                    </div>

                <div class="prose prose-sm text-base/70 prose-headings:text-base prose-p:text-base/60 prose-a:text-primary hover:prose-a:text-primary/80 prose-a:no-underline hover:prose-a:underline">
                    {!! Str::markdown(theme('footer_text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vitae erat ac dui dignissim molestie. Morbi pulvinar pharetra quam, luctus finibus eros tincidunt ac.'), [
                        'allow_unsafe_links' => false,
                    ]) !!}
                </div>
                <div class="text-xs text-base/50">
                    {{ __('© :year :app_name. | All rights reserved.', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
                </div>
                <span style="display:none;">TariqIsHuman-DEMO_SERVER</span>
            </div>

            <div class="flex flex-col lg:items-end gap-4">
                <div class="flex flex-wrap gap-4 text-sm">
                    @if (theme('privacy_policy_url'))
                        <a href="{{ theme('privacy_policy_url') }}" class="text-base/60 hover:text-secondary transition-colors duration-200">{{ __('Privacy Policy') }}</a>
                    @endif
                    @if (theme('terms_of_service_url'))
                        <a href="{{ theme('terms_of_service_url') }}" class="text-base/60 hover:text-secondary transition-colors duration-200">{{ __('Terms of Service') }}</a>
                    @endif
                    @if (theme('support_url'))
                        <a href="{{ theme('support_url') }}" class="text-base/60 hover:text-secondary transition-colors duration-200">{{ __('Support') }}</a>
                    @endif
                </div>

                <div class="flex gap-3">
                    @if (theme('instagram_url'))
                        <a href="{{ theme('instagram_url') }}" class="p-2 bg-bg-secondary/50 hover:bg-bg-secondary rounded-lg border border-neutral/20 hover:border-neutral/30 transition-all duration-200 group">
                            <x-ri-instagram-fill class="size-4 text-base/60 group-hover:text-secondary" />
                        </a>
                    @endif
                    @if (theme('youtube_url'))
                        <a href="{{ theme('youtube_url') }}" class="p-2 bg-bg-secondary/50 hover:bg-bg-secondary rounded-lg border border-neutral/20 hover:border-neutral/30 transition-all duration-200 group">
                            <x-ri-youtube-fill class="size-4 text-base/60 group-hover:text-secondary" />
                        </a>
                    @endif
                    @if (theme('tiktok_url'))
                        <a href="{{ theme('tiktok_url') }}" class="p-2 bg-bg-secondary/50 hover:bg-bg-secondary rounded-lg border border-neutral/20 hover:border-neutral/30 transition-all duration-200 group">
                            <x-ri-tiktok-fill class="size-4 text-base/60 group-hover:text-secondary" />
                        </a>
                    @endif
                    @if (theme('github_url'))
                        <a href="{{ theme('github_url') }}" class="p-2 bg-bg-secondary/50 hover:bg-bg-secondary rounded-lg border border-neutral/20 hover:border-neutral/30 transition-all duration-200 group">
                            <x-ri-github-fill class="size-4 text-base/60 group-hover:text-secondary" />
                        </a>
                    @endif
                    @if (theme('discord_url'))
                        <a href="{{ theme('discord_url') }}" class="p-2 bg-bg-secondary/50 hover:bg-bg-secondary rounded-lg border border-neutral/20 hover:border-neutral/30 transition-all duration-200 group">
                            <x-ri-discord-fill class="size-4 text-base/60 group-hover:text-secondary" />
                        </a>
                    @endif
                    @if (theme('twitter_url'))
                        <a href="{{ theme('twitter_url') }}" class="p-2 bg-bg-secondary/50 hover:bg-bg-secondary rounded-lg border border-neutral/20 hover:border-neutral/30 transition-all duration-200 group">
                            <x-ri-twitter-fill class="size-4 text-base/60 group-hover:text-secondary" />
                        </a>
                    @endif
                </div>

                <a href="https://paymenter.org" target="_blank" 
                   class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border border-neutral/20 bg-background-secondary/20 backdrop-blur-md hover:bg-background-secondary/40 hover:border-neutral/30 transition-all duration-300 self-start lg:self-end"
                   title="Powered by Paymenter">
                    <div class="p-1 rounded bg-[#4667FF]/10 group-hover:bg-[#4667FF]/20 transition-colors duration-300">
                        <svg class="size-3.5 text-[#4667FF]" 
                             viewBox="0 0 150 205" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0 107V205H42.8571V139.638H100C133.333 139.638 150 123 150 89.7246V69.5L75 107V69.5L148.227 32.8863C143.133 10.9621 127.057 0 100 0H0V107ZM0 107V69.5L75 32V69.5L0 107Z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-base/60 group-hover:text-base/80 transition-colors duration-300">
                        Powered by <span class="font-bold text-[#4667FF] group-hover:text-[#4667FF]/90">Paymenter</span>
                    </span>
                </a> 
            </div>
        </div>
    </div>
</footer>