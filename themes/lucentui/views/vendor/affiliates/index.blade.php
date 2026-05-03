<div class="mx-auto px-6 py-8 md:px-8 lg:px-12">
    <x-navigation.breadcrumb />
    
    @isset($affiliate)

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 mt-4 mb-12">
            <div class="bg-primary/10 border border-neutral/50 rounded-xl p-6 shadow-lg transition-all duration-300 hover:scale-[1.01] hover:shadow-xl hover:border-primary/50">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-primary/10 p-3 rounded-full flex-shrink-0 shadow-sm">
                        <x-ri-eye-line class="size-6 text-primary" />
                    </div>
                    <span class="text-sm font-medium text-primary bg-primary/20 px-3 py-1 rounded-full">
                        {{ __('affiliates::affiliate.visitors') }}
                    </span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-color-base mb-1">{{ Number::format($affiliate->visitors) }}</p>
                    <p class="text-sm text-color-muted">{{ __('affiliates::affiliate.total-visitors') }}</p>
                </div>
            </div>

            <div class="bg-success/10 border border-neutral/50 rounded-xl p-6 shadow-lg transition-all duration-300 hover:scale-[1.01] hover:shadow-xl hover:border-success/50">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-success/10 p-3 rounded-full flex-shrink-0 shadow-sm">
                        <x-ri-user-add-line class="size-6 text-success" />
                    </div>
                    <span class="text-sm font-medium text-success bg-success/20 px-3 py-1 rounded-full">
                        {{ __('affiliates::affiliate.signups') }}
                    </span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-color-base mb-1">{{ Number::format($affiliate->signups) }}</p>
                    <p class="text-sm text-color-muted">{{ __('affiliates::affiliate.total-signups') }}</p>
                    @if($affiliate->visitors > 0)
                        <div class="mt-2">
                            <span class="text-xs font-medium text-color-muted">
                                <span class="text-success">{{ number_format(($affiliate->signups / $affiliate->visitors) * 100, 1) }}%</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-warning/10 border border-neutral/50 rounded-xl p-6 shadow-lg transition-all duration-300 hover:scale-[1.01] hover:shadow-xl hover:border-warning/50">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-warning/10 p-3 rounded-full flex-shrink-0 shadow-sm">
                        <x-ri-money-dollar-circle-line class="size-6 text-warning" />
                    </div>
                    <span class="text-sm font-medium text-warning bg-warning/20 px-3 py-1 rounded-full">
                        {{ __('affiliates::affiliate.earnings') }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-color-muted mb-2">{{ __('affiliates::affiliate.total-earnings') }}</p>
                    <div class="space-y-1">
                        @foreach ($affiliate->earnings as $currency => $amount)
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-color-base">{{ $currency }}:</span>
                                <span class="text-lg font-bold text-warning">{{ $amount }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg mb-8" style="animation-duration: 0.8s;">
            <div class="flex items-center mb-4">
                <div class="bg-primary/10 p-3 rounded-full flex-shrink-0 shadow-sm mr-4">
                    <x-ri-link class="size-6 text-primary" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-color-base">{{ __('affiliates::affiliate.affiliate') }}</h3>
                    <p class="text-sm text-color-muted">{{ __('affiliates::affiliate.your-affiliate-link') }}</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <x-form.input 
                        value="{{ url('/') }}?ref={{ $affiliate->code }}" 
                        name="ref"
                        divClass="!mt-0" 
                        type="text" 
                        readonly 
                        class="bg-background/50 border-neutral/50 focus:ring-primary focus:border-primary"
                    />
                </div>
                <div class="flex gap-2">
                    <button 
                        class="inline-flex items-center px-6 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200" 
                        type="button"
                        onclick="copyToClipboard('{{ url('/?ref=' . $affiliate->code) }}')"
                    >
                        <x-ri-file-copy-line class="size-4 mr-2" />
                        {{ __('affiliates::affiliate.copy') }}
                    </button>
                    
                    <button 
                        type="button" 
                        onclick="shareAffiliateLink('{{ url('/?ref=' . $affiliate->code) }}')"
                        class="inline-flex items-center px-4 border border-neutral/50 text-base font-medium rounded-xl shadow-sm text-color-base bg-background-secondary hover:bg-background transition-colors duration-200"
                    >
                        <x-ri-share-line class="size-4" />
                    </button>
                </div>
            </div>

            <div id="copy-success" class="hidden mt-4 p-4 bg-success/10 border border-neutral/50 rounded-xl">
                <div class="flex items-center">
                    <x-ri-checkbox-circle-fill class="size-5 text-success mr-2" />
                    <p class="text-sm font-medium text-success">Link Copied</p>
                </div>
            </div>
        </div>

        <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl p-6 shadow-lg" style="animation-duration: 1.0s;">
            <h3 class="text-xl font-bold text-color-base mb-6">Quick Stats</h3>
            <div class="grid grid-cols-2 md:grid-cols-{{ $affiliate->visitors > 0 ? 4 : 3 }} gap-4">
            <div class="bg-primary/10 border border-neutral/50 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center mb-2">
                <x-ri-hashtag class="size-6 text-primary mr-2" />
                <span class="text-2xl font-bold text-primary">{{ $affiliate->code }}</span>
                </div>
                <p class="text-sm font-medium text-primary">{{ __('affiliates::affiliate.code') }}</p>
            </div>
            
            @if($affiliate->visitors > 0)
                <div class="bg-success/10 border border-neutral/50 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center mb-2">
                    <x-ri-percent-line class="size-6 text-success mr-2" />
                    <span class="text-2xl font-bold text-success">{{ number_format(($affiliate->signups / $affiliate->visitors) * 100, 1) }}%</span>
                </div>
                <p class="text-sm font-medium text-success">Conversion Rate</p>
                </div>
            @endif
                
                <div class="bg-warning/10 border border-neutral/50 rounded-xl p-4 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <x-ri-coins-line class="size-6 text-warning mr-2" />
                        <span class="text-2xl font-bold text-warning">{{ count($affiliate->earnings) }}</span>
                    </div>
                    <p class="text-sm font-medium text-warning">Earnings</p>
                </div>
                
                <div class="bg-inactive/10 border border-neutral/50 rounded-xl p-4 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <x-ri-calendar-line class="size-6 text-inactive mr-2" />
                        <span class="text-2xl font-bold text-inactive">{{ Carbon\Carbon::parse($affiliate->created_at)->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm font-medium text-inactive">Member Since</p>
                </div>
            </div>
        </div>
    @else
        <!-- Sign Up Section -->
        <div class="max-w-md mx-auto">
            <div class="bg-background-secondary/50 border border-neutral/50 rounded-xl shadow-lg p-8">
                <div class="text-center mb-6">
                    <div class="bg-primary/10 p-4 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <x-ri-team-line class="size-10 text-primary" />
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-color-base mb-4">Join Program</h2>
                    <p class="text-lg text-color-muted font-light">You haven't signed up yet</p>
                </div>

                <form wire:submit.prevent="signup" method="POST" class="space-y-4">
                    @if ($signup_type === 'custom')
                        <div>
                            <x-form.input 
                                name="referral_code" 
                                type="text" 
                                :label="__('affiliates::affiliate.code')" 
                                wire:model="referral_code"
                                class="bg-background border-neutral/50 focus:ring-primary focus:border-primary"
                                required 
                            />
                        </div>
                    @endif

                    <button 
                        type="submit" 
                        class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                    >
                        <x-ri-user-add-line class="size-5 mr-2" />
                        {{ __('auth.sign_up') }}
                    </button>
                </form>

                <!-- Benefits List -->
                <div class="mt-6 pt-6 border-t border-neutral/50">
                    <h4 class="text-lg font-bold text-color-base mb-4">Benefits</h4>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="bg-success/10 p-2 rounded-full mr-3">
                                <x-ri-checkbox-circle-fill class="size-4 text-success" />
                            </div>
                            <span class="text-color-muted">Earn Commissions</span>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-success/10 p-2 rounded-full mr-3">
                                <x-ri-checkbox-circle-fill class="size-4 text-success" />
                            </div>
                            <span class="text-color-muted">Track Performance</span>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-success/10 p-2 rounded-full mr-3">
                                <x-ri-checkbox-circle-fill class="size-4 text-success" />
                            </div>
                            <span class="text-color-muted">Easy Sharing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endisset

    <script>
        function copyToClipboard(textToCopy) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    showCopySuccess();
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    fallbackCopyToClipboard(textToCopy);
                });
            } else {
                fallbackCopyToClipboard(textToCopy);
            }
        }

        function fallbackCopyToClipboard(textToCopy) {
            const temp = document.createElement("input");
            temp.type = "text";
            temp.value = textToCopy;
            temp.style.position = "absolute";
            temp.style.left = "-9999px";

            document.body.appendChild(temp);
            temp.select();
            temp.setSelectionRange(0, 99999);

            try {
                document.execCommand("copy");
                showCopySuccess();
            } catch (err) {
                console.error('Fallback copy failed: ', err);
            }

            document.body.removeChild(temp);
        }

        function showCopySuccess() {
            const successMsg = document.getElementById('copy-success');
            successMsg.classList.remove('hidden');
            setTimeout(() => {
                successMsg.classList.add('hidden');
            }, 3000);
        }

        function shareAffiliateLink(url) {
            if (navigator.share) {
                navigator.share({
                    title: '{{ __("affiliates::affiliate.share-title") }}',
                    text: '{{ __("affiliates::affiliate.share-text") }}',
                    url: url
                }).catch(err => console.log('Error sharing:', err));
            } else {
                copyToClipboard(url);
            }
        };
    </script>
</div>