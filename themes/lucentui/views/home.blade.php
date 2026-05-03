<div class="min-h-screen">

@php
    if (theme('disable_homepage', false)) {
        return redirect()->route('dashboard');
    }
@endphp

    @if(theme('homepage_layout', 'modern') === 'modern')

        @if(theme('custom_homepage_html'))
            <div class="custom-homepage-content">
                {!! theme('custom_homepage_html') !!}
            </div>
        @endif

        <section id="hero" class="relative isolate overflow-hidden pt-20 pb-24 lg:pt-28 lg:pb-32 px-6">

            <div class="container mx-auto px-4 lg:px-8">
                <div class="max-w-7xl lg:grid lg:grid-cols-12 lg:gap-x-12 lg:px-0 items-center">
                    <div class="lg:col-span-7 lg:px-0 text-center lg:text-left">
                        
                        <div class="animate-fade-in-down mb-6 flex justify-center lg:justify-start">
                            <div class="inline-flex items-center gap-2 rounded-full bg-primary/10 border border-primary/20 px-4 py-1.5 backdrop-blur-sm transition-colors hover:bg-primary/20">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                </span>
                                <span class="text-sm font-semibold text-primary tracking-wide">{{ theme('homepage_hero_badge', 'Now 50% Off!') }}</span>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h1 class="text-5xl font-bold tracking-tight sm:text-6xl lg:text-7xl animate-enter leading-[1.1]">
                                {{ theme('homepage_hero_title1', 'The Best') }}
                                <br class="hidden lg:block" />
                                <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent animate-gradient-x">
                                    {{ theme('homepage_hero_title2', 'Hosting') }}
                                </span>
                            </h1>
                        </div>

                        <div class="text-lg text-base/80 max-w-2xl mx-auto lg:mx-0 animate-enter animation-delay-200 leading-relaxed mb-10 prose dark:prose-invert">
                            {!! \Illuminate\Support\Str::markdown(theme('homepage_hero_desc', 'Experience next-gen game hosting with blazing-fast SSD performance, enterprise-grade security, and 24/7 expert support.')) !!}
                        </div>

                        <div class="flex flex-wrap justify-center lg:justify-start gap-4 animate-enter animation-delay-300 mb-10">
                            <a href="#services" class="group inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3.5 font-semibold text-white shadow-lg shadow-primary/20 hover:bg-primary/90 hover:-translate-y-0.5 transition-all duration-300 text-base">
                                <x-ri-rocket-line class="h-5 w-5" />
                                <span>{{ theme('hero_cta_primary_text', 'Get Started') }}</span>
                                <x-ri-arrow-right-line class="h-5 w-5 group-hover:translate-x-1 transition-transform" />
                            </a>
                            <a href="#features" class="group inline-flex items-center gap-2 rounded-xl bg-background-secondary border border-neutral px-8 py-3.5 font-semibold text-color-base hover:border-primary/50 hover:-translate-y-0.5 transition-all duration-300 text-base">
                                <x-ri-flashlight-line class="h-5 w-5 text-color-muted group-hover:text-primary transition-colors" />
                                <span>{{ theme('hero_cta_secondary_text', 'Features') }}</span>
                            </a>
                        </div>

                        <div class="flex flex-wrap justify-center lg:justify-start items-center gap-x-6 gap-y-3 text-sm text-color-muted animate-enter animation-delay-400">
                            @php
                            $badgeDefaults = ['Money-back', 'Instant Setup', '24/7 Support'];
                            @endphp

                            @for ($i = 1; $i <= 3; $i++)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-5 h-5 rounded-full bg-success/10">
                                    <x-ri-check-line class="h-3 w-3 text-success" />
                                </div>
                                <span class="font-medium">{{ theme("hero_trust_badge_{$i}", $badgeDefaults[$i - 1]) }}</span>
                            </div>
                            @endfor
                        </div>
                    </div>

                    <div class="hidden lg:block lg:col-span-5 animate-enter animation-delay-800 relative">
                         <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-primary/20 rounded-full blur-[80px] -z-10"></div>
                         
                        <div class="relative w-full max-w-[450px] mx-auto transform hover:scale-[1.02] transition-transform duration-500">
                            @php
                            $heroImg = theme('homepage_hero_illustration_url') ?: 'https://www.svgrepo.com/show/474371/disk1.svg';
                            $heroAlt = theme('homepage_hero_illustration_alt', 'Hero Illustration');
                            @endphp

                            <img
                            src="{{ $heroImg }}"
                            alt="{{ $heroAlt }}"
                            class="relative w-full h-auto object-contain animate-float select-none drop-shadow-2xl"
                            draggable="false"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="statistics" class="relative py-8 px-8">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                    <div class="group text-center p-6 rounded-xl bg-gradient-to-br from-background-secondary/20 to-background/20 border border-neutral/50 hover:border-success/20 transition-all duration-300 hover:scale-105 animate-enter">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-success/10 text-success mb-4">
                            <x-ri-time-line class="h-7 w-7" />
                        </div>
                        <div class="text-3xl font-bold text-success mb-2">99.9%</div>
                        <div class="text-sm font-semibold text-color-base">{{ theme('stats_uptime_title', 'Uptime') }}</div>
                        <div class="text-xs text-color-muted mt-1">{{ theme('stats_uptime_subtitle', 'Guaranteed') }}</div>
                    </div>

                    <div class="group text-center p-6 rounded-xl bg-gradient-to-br from-background-secondary/20 to-background/20 border border-neutral/50 hover:border-primary/20 transition-all duration-300 hover:scale-105 animate-enter">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-primary/10 text-primary mb-4">
                            <x-ri-customer-service-line class="h-7 w-7" />
                        </div>
                        <div class="text-3xl font-bold text-primary mb-2">24/7</div>
                        <div class="text-sm font-semibold text-color-base">{{ theme('stats_support_title', 'Support') }}</div>
                        <div class="text-xs text-color-muted mt-1">{{ theme('stats_support_subtitle', 'Always available') }}</div>
                    </div>

                    <div class="group text-center p-6 rounded-xl bg-gradient-to-br from-background-secondary/20 to-background/20 border border-neutral/50 hover:border-purple-500/20 transition-all duration-300 hover:scale-105 animate-enter">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-purple-500/10 text-purple-500 mb-4">
                            <x-ri-user-heart-line class="h-7 w-7" />
                        </div>
                        <div class="text-3xl font-bold text-purple-500 mb-2">{{ number_format(\App\Models\User::count()) }}+</div>
                        <div class="text-sm font-semibold text-color-base">{{ theme('stats_users_title', 'Customers') }}</div>
                        <div class="text-xs text-color-muted mt-1">{{ theme('stats_users_subtitle', 'Worldwide') }}</div>
                    </div>

                    <div class="group text-center p-6 rounded-xl bg-gradient-to-br from-background-secondary/20 to-background/20 border border-neutral/50 hover:border-orange-500/20 transition-all duration-300 hover:scale-105 animate-enter">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-orange-500/10 text-orange-500 mb-4">
                            <x-ri-server-line class="h-7 w-7" />
                        </div>
                        <div class="text-3xl font-bold text-orange-500 mb-2">{{ number_format(\App\Models\Service::where('status', 'active')->count()) }}+</div>
                        <div class="text-sm font-semibold text-color-base">{{ theme('stats_servers_title', 'Servers') }}</div>
                        <div class="text-xs text-color-muted mt-1">{{ theme('stats_servers_subtitle', 'Running worldwide') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="relative py-20 px-8">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-color-base mb-4">
                        {{ theme('features_title', 'Why Choose Us?') }}
                    </h2>
                    <p class="text-lg text-color-muted max-w-2xl mx-auto">
                        {{ theme('features_subtitle', 'We\'ve built the most advanced game hosting platform with features that give you the competitive edge') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="group p-8 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/20 hover:border-primary/40 transition-all duration-300 hover:translate-y-[-4px]">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-6">
                            <x-ri-shield-check-line class="h-6 w-6" />
                        </div>
                            <h3 class="text-xl font-bold text-color-base mb-3">{{ theme('feature_1_title', 'Game Security') }}</h3>
                            <p class="text-color-muted leading-relaxed">{{ theme('feature_1_desc', 'Advanced DDoS protection and secure connections keep your game servers safe 24/7.') }}</p>
                        </div>

                    <div class="group p-8 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/20 hover:border-primary/40 transition-all duration-300 hover:translate-y-[-4px]">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-6">
                            <x-ri-dashboard-line class="h-6 w-6" />
                        </div>
                            <h3 class="text-xl font-bold text-color-base mb-3">{{ theme('feature_2_title', 'Smart Analytics') }}</h3>
                            <p class="text-color-muted leading-relaxed">{{ theme('feature_2_desc', 'Real-time performance monitoring, player analytics, and detailed insights help you optimize your server performance and player experience.') }}</p>
                        </div>

                    <div class="group p-8 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/20 hover:border-primary/40 transition-all duration-300 hover:translate-y-[-4px]">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-6">
                            <x-ri-flashlight-line class="h-6 w-6" />
                        </div>
                            <h3 class="text-xl font-bold text-color-base mb-3">{{ theme('feature_3_title', 'Lightning Setup') }}</h3>
                            <p class="text-color-muted leading-relaxed">{{ theme('feature_3_desc', 'Deploy your game servers in under 60 seconds with our automated setup and instant configuration system. No technical knowledge required.') }}</p>
                        </div>

                    <div class="group p-8 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/20 hover:border-primary/40 transition-all duration-300 hover:translate-y-[-4px]">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-6">
                            <x-ri-cpu-line class="h-6 w-6" />
                        </div>
                            <h3 class="text-xl font-bold text-color-base mb-3">{{ theme('feature_4_title', 'Extreme Performance') }}</h3>
                            <p class="text-color-muted leading-relaxed">{{ theme('feature_4_desc', 'Premium NVMe SSD storage, high-frequency CPUs, and optimized network routing deliver zero-lag gaming experience for your players.') }}</p>
                        </div>

                    <div class="group p-8 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/20 hover:border-primary/40 transition-all duration-300 hover:translate-y-[-4px]">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-6">
                            <x-ri-global-line class="h-6 w-6" />
                        </div>
                            <h3 class="text-xl font-bold text-color-base mb-3">{{ theme('feature_5_title', 'Global Reach') }}</h3>
                            <p class="text-color-muted leading-relaxed">{{ theme('feature_5_desc', '15+ datacenter locations across 6 continents ensure your players get ultra-low latency wherever they are in the world.') }}</p>
                        </div>

                    <div class="group p-8 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/20 hover:border-primary/40 transition-all duration-300 hover:translate-y-[-4px]">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-6">
                            <x-ri-line-chart-line class="h-6 w-6" />
                        </div>
                            <h3 class="text-xl font-bold text-color-base mb-3">{{ theme('feature_6_title', 'Performance Optimization') }}</h3>
                            <p class="text-color-muted leading-relaxed">{{ theme('feature_6_desc', 'Advanced optimization to improve server performance and predict issues before they impact your players.') }}</p>
                        </div>
                    </div>
                </div>
        </section>

        <section id="services" class="relative py-20 px-8">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-color-base mb-4">
                        {{ theme('services_title', 'Our Services') }}
                    </h2>
                    <p class="text-lg text-color-muted max-w-2xl mx-auto">
                        {{ theme('services_subtitle', 'Choose from our range of hosting solutions') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($categories as $category)
                    <div class="group relative rounded-xl bg-background-secondary/20 backdrop-blur-md border border-neutral/50 hover:border-primary/50 overflow-hidden transition-all duration-300 hover:scale-[1.02]">
                        @if ($category->image)
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-background-secondary to-transparent opacity-60"></div>
                        </div>
                        @endif
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-color-base mb-2 group-hover:text-primary transition-colors">
                                {{ $category->name }}
                            </h3>
                            
                            @if(theme('show_category_description', true))
                            <div class="prose dark:prose-invert prose-sm text-color-muted mb-4">
                                {!! $category->description !!}
                            </div>
                            @endif
                            
                            <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all">
                                <span>{{ __('common.button.view_all') }}</span>
                                <x-ri-arrow-right-line class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="relative py-20 px-8">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-color-base mb-4">
                        {{ theme('testimonials_title', 'What Customers Say') }}
                    </h2>
                    <p class="text-lg text-color-muted max-w-2xl mx-auto">
                        {{ theme('testimonials_subtitle', 'Join thousands of satisfied customers') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    @for ($i = 1; $i <= 3; $i++)
                    <div class="p-8 rounded-xl bg-background-secondary/20 backdrop-blur-md border border-neutral hover:border-primary/30 transition-all duration-300">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-primary/60 flex items-center justify-center text-white font-bold">
                                {{ substr(theme("testimonial_{$i}_name", 'Customer'), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-color-base">{{ theme("testimonial_{$i}_name", 'Customer Name') }}</h4>
                                <p class="text-sm text-color-muted">{{ theme("testimonial_{$i}_role", 'Role') }}</p>
                            </div>
                        </div>
                        <p class="text-color-muted italic mb-4">"{{ theme("testimonial_{$i}_quote", 'Great service!') }}"</p>
                        <div class="flex gap-1">
                            @for ($j = 0; $j < 5; $j++)
                            <x-ri-star-fill class="h-4 w-4 text-warning" />
                            @endfor
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </section>

        <section class="relative py-20 px-8">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-color-base mb-4">
                        {{ theme('faq_title', 'Frequently Asked Questions') }}
                    </h2>
                    <p class="text-lg text-color-muted max-w-2xl mx-auto">
                        {{ theme('faq_subtitle', 'Find answers to common questions about our services') }}
                    </p>
                </div>

                <div class="max-w-7xl mx-auto w-full" x-data="{ openFaq: null }">
                    <div class="space-y-4">
                    @php
                        $faqs = [
                        [
                            'question' => 'How quickly can I get my server up and running?',
                            'answer' => 'Your server will be automatically deployed within 60 seconds after payment confirmation. Our automated system ensures instant setup with zero manual intervention required.'
                        ],
                        [
                            'question' => 'What payment methods do you accept?',
                            'answer' => 'We accept all major payment methods including credit/debit cards, PayPal, cryptocurrency, and local payment options. All transactions are secured with industry-standard encryption.'
                        ],
                        [
                            'question' => 'Can I upgrade or downgrade my plan anytime?',
                            'answer' => 'Yes! You can upgrade or downgrade your hosting plan at any time through your dashboard. Changes take effect immediately, and we\'ll pro-rate any pricing differences.'
                        ],
                        [
                            'question' => 'Do you offer refunds if I\'m not satisfied?',
                            'answer' => 'We offer a 30-day money-back guarantee. If you\'re not completely satisfied with our service within the first 30 days, contact our support team for a full refund.'
                        ],
                        [
                            'question' => 'What kind of support do you provide?',
                            'answer' => 'We provide 24/7 customer support through live chat, email, and ticket system. Our expert team is always ready to help you with technical issues, setup assistance, or any questions.'
                        ],
                        [
                            'question' => 'How do you handle DDoS attacks?',
                            'answer' => 'All our servers come with enterprise-grade DDoS protection that automatically detects and mitigates attacks in real-time, ensuring your server stays online even during large-scale attacks.'
                        ]
                        ];
                    @endphp

                    @foreach($faqs as $index => $faq)
                    <div class="group border border-neutral rounded-xl overflow-hidden bg-background-secondary/20 backdrop-blur-md hover:border-primary/30 transition-all duration-300">
                        <button 
                            @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}"
                            class="w-full flex items-center justify-between p-6 text-left"
                            :aria-expanded="openFaq === {{ $index }}"
                        >
                        <span class="text-lg font-semibold text-color-base pr-8">
                            {{ theme('faq_'.($index + 1).'_question', $faq['question']) }}
                        </span>
                        <span class="flex-shrink-0 transition-transform duration-300" 
                            :class="{ 'rotate-45': openFaq === {{ $index }} }">
                            <x-ri-add-line class="h-6 w-6 text-primary" />
                        </span>
                        </button>
                            <div 
                                x-show="openFaq === {{ $index }}"
                                x-collapse
                                class="overflow-hidden"
                            >
                            <div class="px-6 pb-6 text-color-muted leading-relaxed">
                                {{ theme('faq_'.($index + 1).'_answer', $faq['answer']) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="relative py-24 overflow-hidden px-8">
            <div class="container mx-auto px-4">
                <div class="mx-auto w-full max-w-7xl bg-gradient-to-br from-primary/10 via-secondary/5 to-background border border-neutral/10 rounded-xl p-8 sm:p-12 shadow-xl backdrop-blur-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <div class="text-left">
                            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                                <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                                    {{ theme('cta_title', 'Ready to Get Started?') }}
                                </span>
                            </h2>
                            <p class="text-xl text-color-muted mb-0">
                                {{ theme('cta_subtitle', 'Join thousands of satisfied customers today') }}
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="#services" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary to-secondary px-8 py-4 text-base font-semibold text-white shadow-lg hover:shadow-primary/50 transition-all duration-300 hover:scale-105">
                                    <x-ri-sparkling-2-line class="h-5 w-5" />
                                    <span>{{ theme('cta_primary_text', 'View Pricing') }}</span>
                                </a>
                                <a href="{{ theme('help_link', '#') }}" class="inline-flex items-center gap-2 rounded-xl bg-background border border-primary/30 px-8 py-4 text-base font-semibold text-color-base hover:border-primary/50 transition-all duration-300">
                                    <x-ri-chat-3-line class="h-5 w-5" />
                                    <span>{{ theme('cta_secondary_text', 'Contact Us') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @elseif(theme('homepage_layout', 'modern') === 'portal')

        @if(theme('custom_homepage_html'))
            <div class="custom-homepage-content">
                {!! theme('custom_homepage_html') !!}
            </div>
        @endif

        <section class="mt-20 relative py-16 border-b border-neutral">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 rounded-full px-4 py-2 mb-6">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        <span class="text-sm font-semibold text-primary">{{ theme('homepage_hero_badge', '🚀 Welcome!') }}</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-bold text-color-base mb-4">
                        {{ theme('homepage_hero_title1', 'The Best') }}
                        <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            {{ theme('homepage_hero_title2', 'Hosting') }}
                        </span>
                    </h1>
                    
                    <p class="text-lg text-color-muted mb-8">
                        {!! \Illuminate\Support\Str::markdown(theme('homepage_hero_desc', 'Experience next-gen game hosting with blazing-fast SSD performance, enterprise-grade security, and 24/7 expert support.')) !!}
                    </p>
                </div>
            </div>
        </section>

        <section class="py-12 bg-background">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ theme('buy_server_link', '#services') }}" class="group p-6 rounded-xl bg-gradient-to-br from-primary to-primary/80 text-white hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                            <x-ri-server-fill class="h-6 w-6" />
                        </div>
                        <h3 class="font-bold text-lg mb-1">{{ theme('buy_server_title', 'Buy Server') }}</h3>
                        <p class="text-sm text-white/80">{{ theme('buy_server_subtitle', 'Get started') }}</p>
                    </a>
                    <a href="{{ theme('help_link', '#') }}" target="_blank" class="group p-6 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                            <x-ri-customer-service-line class="h-6 w-6" />
                        </div>
                        <h3 class="font-bold text-lg mb-1">{{ theme('help_title', 'Support') }}</h3>
                        <p class="text-sm text-white/80">{{ theme('help_subtitle', '24/7 Help') }}</p>
                    </a>

                    @guest
                    <a href="{{ route('login') }}" class="group p-6 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                            <x-ri-login-box-line class="h-6 w-6" />
                        </div>
                        <h3 class="font-bold text-lg mb-1">{{ theme('login_title', 'Login') }}</h3>
                        <p class="text-sm text-white/80">{{ theme('login_subtitle', 'Access Panel') }}</p>
                    </a>
                    @else
                    <a href="{{ route('dashboard') }}" class="group p-6 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                            <x-ri-dashboard-fill class="h-6 w-6" />
                        </div>
                        <h3 class="font-bold text-lg mb-1">{{ theme('dashboard_title', 'Dashboard') }}</h3>
                        <p class="text-sm text-white/80">{{ theme('dashboard_subtitle', 'Manage') }}</p>
                    </a>
                    @endguest

                    <a href="{{ theme('docs_link', '#') }}" target="_blank" class="group p-6 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-4">
                            <x-ri-book-open-line class="h-6 w-6" />
                        </div>
                        <h3 class="font-bold text-lg mb-1">{{ theme('docs_title', 'Docs') }}</h3>
                        <p class="text-sm text-white/80">{{ theme('docs_subtitle', 'Learn more') }}</p>
                    </a>
                </div>
            </div>
        </section>

        <section class="py-12 bg-background-secondary/20 backdrop-blur-md">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-success mb-1">99.9%</div>
                        <div class="text-sm text-color-muted">{{ theme('stats_uptime_title', 'Uptime') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary mb-1">24/7</div>
                        <div class="text-sm text-color-muted">{{ theme('stats_support_title', 'Support') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-500 mb-1">{{ number_format(\App\Models\User::count()) }}+</div>
                        <div class="text-sm text-color-muted">{{ theme('stats_users_title', 'Users') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-500 mb-1">{{ number_format(\App\Models\Service::where('status', 'active')->count()) }}+</div>
                        <div class="text-sm text-color-muted">{{ theme('stats_servers_title', 'Servers') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="py-16 bg-background">
            <div class="container mx-auto px-4">
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-color-base mb-2">
                        {{ theme('services_title', 'Our Services') }}
                    </h2>
                    <p class="text-color-muted">
                        {{ theme('services_subtitle', 'Select a service to get started') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($categories as $category)
                    <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="group block p-6 rounded-xl bg-background-secondary border border-neutral hover:border-primary/50 hover:shadow-lg transition-all duration-300">
                        @if ($category->image)
                        <div class="w-16 h-16 rounded-lg overflow-hidden mb-4">
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                            <x-ri-server-line class="h-8 w-8 text-primary" />
                        </div>
                        @endif
                        
                        <h3 class="text-lg font-bold text-color-base mb-2 group-hover:text-primary transition-colors">
                            {{ $category->name }}
                        </h3>
                        
                        @if(theme('show_category_description', true))
                        <div class="prose dark:prose-invert prose-sm text-color-muted mb-3">
                            {!! $category->description !!}
                        </div>
                        @endif
                        
                        <div class="flex items-center text-primary text-sm font-semibold">
                            <span>{{ __('common.button.view_all') }}</span>
                            <x-ri-arrow-right-line class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-16 bg-background-secondary/30">
            <div class="container mx-auto px-4">
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-color-base mb-2">
                        {{ theme('features_title', 'Why Choose Us') }}
                    </h2>
                    <p class="text-color-muted">
                        {{ theme('features_subtitle', 'Premium features included') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex gap-4 p-6 rounded-xl bg-background border border-neutral">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <x-ri-shield-check-line class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <h3 class="font-bold text-color-base mb-1">{{ theme('feature_1_title', 'Secure') }}</h3>
                            <p class="text-sm text-color-muted">{{ theme('feature_1_desc', 'Advanced security') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 rounded-xl bg-background border border-neutral">
                        <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center flex-shrink-0">
                            <x-ri-flashlight-line class="h-5 w-5 text-secondary" />
                        </div>
                        <div>
                            <h3 class="font-bold text-color-base mb-1">{{ theme('feature_3_title', 'Fast Setup') }}</h3>
                            <p class="text-sm text-color-muted">{{ theme('feature_3_desc', 'Quick deployment') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 rounded-xl bg-background border border-neutral">
                        <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                            <x-ri-cpu-line class="h-5 w-5 text-warning" />
                        </div>
                        <div>
                            <h3 class="font-bold text-color-base mb-1">{{ theme('feature_4_title', 'Performance') }}</h3>
                            <p class="text-sm text-color-muted">{{ theme('feature_4_desc', 'High-speed servers') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 rounded-xl bg-background border border-neutral">
                        <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center flex-shrink-0">
                            <x-ri-global-line class="h-5 w-5 text-info" />
                        </div>
                        <div>
                            <h3 class="font-bold text-color-base mb-1">{{ theme('feature_5_title', 'Global') }}</h3>
                            <p class="text-sm text-color-muted">{{ theme('feature_5_desc', 'Worldwide coverage') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 rounded-xl bg-background border border-neutral">
                        <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center flex-shrink-0">
                            <x-ri-customer-service-line class="h-5 w-5 text-success" />
                        </div>
                        <div>
                            <h3 class="font-bold text-color-base mb-1">24/7 Support</h3>
                            <p class="text-sm text-color-muted">Always available</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-6 rounded-xl bg-background border border-neutral">
                        <div class="w-10 h-10 rounded-lg bg-error/10 flex items-center justify-center flex-shrink-0">
                            <x-ri-line-chart-line class="h-5 w-5 text-error" />
                        </div>
                        <div>
                            <h3 class="font-bold text-color-base mb-1">{{ theme('feature_6_title', 'Smart') }}</h3>
                            <p class="text-sm text-color-muted">{{ theme('feature_6_desc', 'AI optimization') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-background">
            <div class="container mx-auto px-4">
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-color-base mb-2">
                        {{ theme('testimonials_title', 'Testimonials') }}
                    </h2>
                    <p class="text-color-muted">
                        {{ theme('testimonials_subtitle', 'What our customers say') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    @for ($i = 1; $i <= 3; $i++)
                    <div class="p-6 rounded-xl bg-background-secondary border border-neutral">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary/60 flex items-center justify-center text-white font-bold text-sm">
                                {{ substr(theme("testimonial_{$i}_name", 'User'), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-color-base text-sm">{{ theme("testimonial_{$i}_name", 'Customer') }}</h4>
                                <p class="text-xs text-color-muted">{{ theme("testimonial_{$i}_role", 'Role') }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-color-muted italic mb-3">"{{ theme("testimonial_{$i}_quote", 'Great service!') }}"</p>
                        <div class="flex gap-1">
                            @for ($j = 0; $j < 5; $j++)
                            <x-ri-star-fill class="h-3 w-3 text-warning" />
                            @endfor
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </section>

        <section class="py-16 bg-gradient-to-r from-primary/10 to-secondary/10">
            <div class="container mx-auto px-4">
                <div class="max-w-xl mx-auto text-center">
                    <h2 class="text-3xl font-bold mb-4">
                        <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            {{ theme('cta_title', 'Ready to Start?') }}
                        </span>
                    </h2>
                    <p class="text-color-muted mb-8">
                        {{ theme('cta_subtitle', 'Host your Minecraft server with us today and experience unparalleled performance and support.') }}
                    </p>
                    <div class="flex gap-4 justify-center">
                        <a href="#services" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white hover:bg-primary/90 transition-all">
                            <span>{{ theme('cta_primary_text', 'Get Started') }}</span>
                            <x-ri-arrow-right-line class="h-4 w-4" />
                        </a>
                        <a href="{{ theme('help_link', '#') }}" class="inline-flex items-center gap-2 rounded-xl bg-background-secondary border border-neutral px-6 py-3 text-sm font-semibold text-color-base hover:border-primary/50 transition-all">
                            <span>{{ theme('cta_secondary_text', 'Learn More') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    @elseif(theme('homepage_layout', 'modern') === 'business')

    @if(theme('custom_homepage_html'))
        <div class="custom-homepage-content">
            {!! theme('custom_homepage_html') !!}
        </div>
    @endif

    <div class="absolute inset-0 -z-10 h-full w-full bg-background">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808008_1px,transparent_1px),linear-gradient(to_bottom,#80808008_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
    </div>

    <section id="hero" class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
        <div class="container mx-auto px-6 relative z-10 w-full">
            <div class="max-w-4xl mx-auto text-center flex flex-col items-center justify-center">
                <a href="#services" class="group inline-flex items-center gap-2 py-1.5 px-4 rounded-full border border-primary/10 bg-primary/5 hover:bg-primary/10 transition-all duration-300 mb-8 cursor-pointer">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <span class="text-xs font-bold tracking-wide uppercase text-primary/80 group-hover:text-primary transition-colors">
                        {{ theme('homepage_hero_badge', 'Enterprise Grade Hosting') }}
                    </span>
                </a>

                <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold tracking-tighter text-color-base mb-8 leading-[1.1]" 
                    x-data="{ words: ['{{ theme('homepage_rotating_title1', 'Infrastructure') }}', '{{ theme('homepage_rotating_title2', 'Cloud') }}', '{{ theme('homepage_rotating_title3', 'Server') }}'], index: 0 }" 
                    x-init="setInterval(() => index = (index + 1) % words.length, 3000)">
                    {{ theme('homepage_hero_title1', 'Build Your') }}
                    <br />
                    <span class="text-primary md:text-transparent bg-clip-text bg-gradient-to-r from-primary to-primary/70 relative inline-grid grid-cols-1 grid-rows-1">
                        <template x-for="(word, i) in words" :key="i">
                            <span class="col-start-1 row-start-1 transition-all duration-700 ease-in-out"
                                :class="index === i ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-4 scale-95 pointer-events-none'"
                                x-text="word"></span>
                        </template>
                    </span>
                </h1>

                <p class="text-lg md:text-xl text-color-muted font-medium leading-relaxed max-w-2xl mb-10">
                    {{ theme('homepage_hero_desc', 'Deploy high-performance servers in seconds. Engineered for scalability, security, and enterprise businesses.') }}
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto mb-16">
                    <a href="#pricing" class="group relative w-full sm:w-auto px-8 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/20 hover:-translate-y-0.5 transition-all duration-300">
                        <span class="flex items-center justify-center gap-2">
                            {{ theme('hero_cta_primary_text', 'Deploy Now') }}
                            <x-ri-rocket-2-fill class="size-5 group-hover:translate-x-1 transition-transform duration-300" />
                        </span>
                    </a>
                    <a href="#contact" class="group w-full sm:w-auto px-8 py-4 bg-background-secondary border border-neutral/50 text-color-base font-semibold rounded-xl hover:border-neutral hover:bg-background-secondary/20 backdrop-blur-md transition-all duration-300">
                        <span>{{ theme('hero_cta_secondary_text', 'Need help?') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-background">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-neutral/30">
                <div class="text-center px-4">
                    <div class="text-3xl md:text-4xl font-black text-color-base mb-1">99.9%</div>
                    <div class="text-xs uppercase tracking-wider text-color-muted font-semibold">{{ theme('stats_uptime_title', 'Uptime SLA') }}</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-3xl md:text-4xl font-black text-color-base mb-1">{{ number_format(\App\Models\User::count()) }}+</div>
                    <div class="text-xs uppercase tracking-wider text-color-muted font-semibold">{{ theme('stats_users_title', 'Happy Clients') }}</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-3xl md:text-4xl font-black text-color-base mb-1">24/7</div>
                    <div class="text-xs uppercase tracking-wider text-color-muted font-semibold">{{ theme('stats_support_title', 'Support') }}</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-3xl md:text-4xl font-black text-color-base mb-1">{{ theme('stats_locations_count', '15+') }}</div>
                    <div class="text-xs uppercase tracking-wider text-color-muted font-semibold">Data Centers</div>
                </div>
            </div>
        </div>
    </section>

    @if(theme('homepage_show_pricing', true))
    <section id="pricing" class="py-24 bg-background relative overflow-hidden px-6">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-color-base mb-4">
                    {{ theme('pricing_title', 'Simple, Transparent Pricing') }}
                </h2>
                <p class="text-lg text-color-muted">
                    {{ theme('pricing_subtitle', 'Choose the perfect plan for your business needs. Upgrade anytime.') }}
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto items-start">
                {{-- PLAN 1 --}}
                <div class="group relative bg-background border {{ theme('pricing_popular_plan', '2') == '1' ? 'border-2 border-primary/50 shadow-2xl shadow-primary/10 transform scale-105 z-10' : 'border-neutral/50' }} rounded-2xl p-8 {{ theme('pricing_popular_plan', '2') == '1' ? '' : 'hover:border-primary/30 transition-all duration-300 hover:shadow-xl' }}">
                    @if(theme('pricing_popular_plan', '2') == '1')
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-xs font-bold uppercase tracking-widest rounded-full shadow-lg">
                            Most Popular
                        </div>
                    @endif
                    
                    <h3 class="text-xl font-bold text-color-base mb-2">{{ theme('pricing_1_name', 'Starter') }}</h3>
                    <p class="text-color-muted text-sm mb-6 min-h-[40px]">{{ theme('pricing_1_desc', 'Essential resources for small projects and testing environments.') }}</p>
                    
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-3xl font-bold text-color-base">{{ theme('pricing_1_price', '$9.99') }}</span>
                        <span class="text-sm text-color-muted">/mo</span>
                    </div>

                    @if(theme('pricing_1_discount'))
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 mb-6 rounded-md bg-green-500/10 border border-green-500/20 text-green-600 text-xs font-bold w-fit">
                            <x-ri-price-tag-3-fill class="size-3" />
                            {{ theme('pricing_1_discount') }}
                        </div>
                    @endif

                    <a href="{{ theme('pricing_1_url', '#') }}" class="block w-full py-3 px-6 text-center rounded-xl font-bold {{ theme('pricing_popular_plan', '2') == '1' ? 'bg-primary text-white hover:bg-primary/90 shadow-lg shadow-primary/25' : 'bg-background-secondary text-color-base border border-neutral/50 hover:bg-background-secondary/20 backdrop-blur-md' }} transition-all mt-auto">
                        {{ theme('pricing_popular_plan', '2') == '1' ? 'Get Started' : 'Choose Plan' }}
                    </a>

                    <ul class="mt-8 space-y-4">
                        @foreach(explode(',', theme('pricing_1_features', '1 vCPU Core, 2 GB RAM, 20 GB NVMe Storage, 1 TB Bandwidth')) as $feature)
                        <li class="flex items-start gap-3 text-sm {{ theme('pricing_popular_plan', '2') == '1' ? 'text-color-base font-medium' : 'text-color-muted' }}">
                            <x-ri-checkbox-circle-fill class="size-5 text-primary shrink-0" />
                            <span>{{ trim($feature) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- PLAN 2 --}}
                <div class="group relative bg-background border {{ theme('pricing_popular_plan', '2') == '2' ? 'border-2 border-primary/50 shadow-2xl shadow-primary/10 transform scale-105 z-10' : 'border-neutral/50' }} rounded-2xl p-8 {{ theme('pricing_popular_plan', '2') == '2' ? '' : 'hover:border-primary/30 transition-all duration-300 hover:shadow-xl' }}">
                    @if(theme('pricing_popular_plan', '2') == '2')
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-xs font-bold uppercase tracking-widest rounded-full shadow-lg">
                            Most Popular
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-color-base mb-2">{{ theme('pricing_2_name', 'Business Pro') }}</h3>
                    <p class="text-color-muted text-sm mb-6 min-h-[40px]">{{ theme('pricing_2_desc', 'Power and reliability for growing businesses and high-traffic sites.') }}</p>

                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-4xl font-bold {{ theme('pricing_popular_plan', '2') == '2' ? 'text-primary' : 'text-color-base' }}">{{ theme('pricing_2_price', '$29.99') }}</span>
                        <span class="text-sm text-color-muted">/mo</span>
                    </div>

                    @if(theme('pricing_2_discount'))
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 mb-6 rounded-md bg-green-500/10 border border-green-500/20 text-green-600 text-xs font-bold w-fit">
                            <x-ri-price-tag-3-fill class="size-3" />
                            {{ theme('pricing_2_discount') }}
                        </div>
                    @endif

                    <a href="{{ theme('pricing_2_url', '#') }}" class="block w-full py-3 px-6 text-center rounded-xl font-bold {{ theme('pricing_popular_plan', '2') == '2' ? 'bg-primary text-white hover:bg-primary/90 shadow-lg shadow-primary/25 transition-all hover:-translate-y-0.5' : 'bg-background-secondary text-color-base border border-neutral/50 hover:bg-background-secondary/20 backdrop-blur-md transition-all' }} mt-auto">
                        {{ theme('pricing_popular_plan', '2') == '2' ? 'Get Started' : 'Choose Plan' }}
                    </a>

                    <ul class="mt-8 space-y-4">
                        @foreach(explode(',', theme('pricing_2_features', '4 vCPU Cores, 8 GB RAM, 100 GB NVMe Storage, Unmetered Bandwidth, Priority Support')) as $feature)
                        <li class="flex items-start gap-3 text-sm {{ theme('pricing_popular_plan', '2') == '2' ? 'text-color-base font-medium' : 'text-color-muted' }}">
                            <x-ri-checkbox-circle-fill class="size-5 text-primary shrink-0" />
                            <span>{{ trim($feature) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- PLAN 3 --}}
                <div class="group relative bg-background border {{ theme('pricing_popular_plan', '2') == '3' ? 'border-2 border-primary/50 shadow-2xl shadow-primary/10 transform scale-105 z-10' : 'border-neutral/50' }} rounded-2xl p-8 {{ theme('pricing_popular_plan', '2') == '3' ? '' : 'hover:border-primary/30 transition-all duration-300 hover:shadow-xl' }}">
                    @if(theme('pricing_popular_plan', '2') == '3')
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-xs font-bold uppercase tracking-widest rounded-full shadow-lg">
                            Most Popular
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-color-base mb-2">{{ theme('pricing_3_name', 'Enterprise') }}</h3>
                    <p class="text-color-muted text-sm mb-6 min-h-[40px]">{{ theme('pricing_3_desc', 'Maximum performance for mission-critical applications.') }}</p>

                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-3xl font-bold text-color-base">{{ theme('pricing_3_price', '$99.99') }}</span>
                        <span class="text-sm text-color-muted">/mo</span>
                    </div>

                    @if(theme('pricing_3_discount'))
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 mb-6 rounded-md bg-green-500/10 border border-green-500/20 text-green-600 text-xs font-bold w-fit">
                            <x-ri-price-tag-3-fill class="size-3" />
                            {{ theme('pricing_3_discount') }}
                        </div>
                    @endif

                    <a href="{{ theme('pricing_3_url', '#') }}" class="block w-full py-3 px-6 text-center rounded-xl font-bold {{ theme('pricing_popular_plan', '2') == '3' ? 'bg-primary text-white hover:bg-primary/90 shadow-lg shadow-primary/25' : 'bg-background-secondary text-color-base border border-neutral/50 hover:border-primary/50 hover:bg-background-secondary/20 backdrop-blur-md' }} transition-all mt-auto">
                        {{ theme('pricing_popular_plan', '2') == '3' ? 'Get Started' : 'Choose Plan' }}
                    </a>

                    <ul class="mt-8 space-y-4">
                        @foreach(explode(',', theme('pricing_3_features', 'Dedicated Resources, 32 GB RAM, 1 TB NVMe Storage, 10Gbps Uplink, Dedicated Account Manager')) as $feature)
                        <li class="flex items-start gap-3 text-sm {{ theme('pricing_popular_plan', '2') == '3' ? 'text-color-base font-medium' : 'text-color-muted' }}">
                            <x-ri-checkbox-circle-fill class="size-5 text-primary shrink-0" />
                            <span>{{ trim($feature) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section id="products" class="py-24 bg-background-secondary/10 border-y border-neutral/30">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-color-base mb-4">
                        {{ theme('services_title', 'Explore All Categories') }}
                    </h2>
                    <p class="text-lg text-color-muted">
                        {{ theme('services_subtitle', 'Browse our full catalog of automated cloud solutions.') }}
                    </p>
                </div>
                <a href="{{ route('services') }}" class="hidden md:flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all">
                    See all products <x-ri-arrow-right-line class="size-5" />
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($categories as $category)
                <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="group relative bg-background border border-neutral/50 rounded-2xl overflow-hidden hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 flex flex-col h-full">
                    @if ($category->image)
                    <div class="relative h-56 overflow-hidden">
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-background to-transparent opacity-90"></div>
                        <div class="absolute bottom-4 left-6">
                            <div class="bg-primary/10 text-primary p-2 rounded-lg w-fit mb-2 backdrop-blur-md border border-primary/20">
                                <x-ri-server-line class="h-6 w-6" />
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="h-56 bg-background-secondary flex items-end p-6 border-b border-neutral/30">
                        <div class="bg-primary/10 text-primary p-3 rounded-xl border border-primary/20">
                            <x-ri-server-line class="h-8 w-8" />
                        </div>
                    </div>
                    @endif
                    
                    <div class="p-8 pt-6 flex-grow flex flex-col">
                        <h3 class="text-2xl font-bold text-color-base mb-3 group-hover:text-primary transition-colors">
                            {{ $category->name }}
                        </h3>
                        
                        @if(theme('show_category_description', true))
                        <div class="prose dark:prose-invert prose-sm text-color-muted mb-6 line-clamp-3 flex-grow">
                            {!! $category->description !!}
                        </div>
                        @endif
                        
                        <div class="mt-auto pt-6 border-t border-neutral/30 flex items-center justify-between text-sm font-semibold text-primary">
                            <span>View Plans</span>
                            <x-ri-arrow-right-line class="h-5 w-5 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('services') }}" class="inline-flex items-center gap-2 text-primary font-semibold">
                    See all products <x-ri-arrow-right-line class="size-5" />
                </a>
            </div>
        </div>
    </section>

    <section class="py-24 bg-background relative overflow-hidden px-6">
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none" 
            style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider mb-6">
                        {{ theme('locations_badge', 'Global Network') }}
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-color-base mb-6">
                        {{ theme('locations_title', 'Low Latency, Worldwide.') }}
                    </h2>
                    <p class="text-lg text-color-muted mb-8 leading-relaxed">
                        {{ theme('locations_description', 'Deploy your services closer to your customers. Our premium Tier-4 data centers ensure maximum speed and reliability wherever you are.') }}
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $locations = explode(',', theme('locations_list', 'New York,London,Singapore,Jakarta,Tokyo,Sydney'));
                        @endphp
                        @foreach($locations as $location)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-success"></div>
                                <span class="text-color-base font-medium">{{ trim($location) }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-10">
                        <a href="{{ theme('locations_help_link', theme('help_link', '#')) }}" class="text-primary font-bold hover:underline">
                            {{ theme('locations_cta_text', 'Check network status') }} &rarr;
                        </a>
                    </div>
                </div>

                <div class="hidden lg:block lg:w-1/2 relative">
                    <div class="relative w-full aspect-video rounded-2xl bg-background-secondary border border-neutral/50 dark:border-neutral/40 shadow-2xl overflow-hidden p-4 group">
                        <div class="absolute inset-0 bg-[url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg')] shadow-xl bg-cover bg-center opacity-20 dark:opacity-20 group-hover:opacity-30 dark:group-hover:opacity-25 transition-opacity duration-500"></div>
                        
                        @php
                            $locationsCoords = theme('locations_coordinates', 'New York:25:28,London:48:25,Singapore:75:48,Jakarta:76:51,Tokyo:85:26,Sydney:80:70');
                            $coordArray = [];
                            foreach(explode(',', $locationsCoords) as $coord) {
                                $parts = explode(':', trim($coord));
                                if(count($parts) === 3) {
                                    $coordArray[] = [
                                        'name' => $parts[0],
                                        'left' => $parts[1],
                                        'top' => $parts[2]
                                    ];
                                }
                            }
                        @endphp

                        @foreach($coordArray as $index => $location)
                        <div class="absolute w-2 h-2 bg-primary rounded-full animate-ping" 
                            style="left: {{ $location['left'] }}%; top: {{ $location['top'] }}%; animation-delay: {{ $index * 0.3 }}s;"></div>
                        <div class="absolute w-2 h-2 bg-primary rounded-full" 
                            style="left: {{ $location['left'] }}%; top: {{ $location['top'] }}%;"></div>
                        @endforeach

                        <div class="absolute bottom-6 left-6 right-6 bg-background/20 backdrop-blur-md border border-neutral/50 dark:border-neutral/40 p-4 rounded-xl shadow-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-success/10 text-success">
                                        <x-ri-global-line class="size-5" />
                                    </div>
                                    <div>
                                        <div class="text-xs text-color-muted uppercase font-bold">{{ theme('locations_capacity_label', 'Network Capacity') }}</div>
                                        <div class="text-sm font-bold text-color-base">{{ theme('locations_capacity_value', '10 Tbps+ Backbone') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-color-muted uppercase font-bold">{{ theme('locations_status_label', 'Status') }}</div>
                                    <div class="text-sm font-bold text-success">{{ theme('locations_status_value', 'Operational') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="bento-grid" class="py-20 px-4 sm:px-6">
        <div class="container mx-auto px-4">
                
            <div class="max-w-2xl mx-auto text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-color-base mb-4">
                    {{ theme('features_title', 'The Unfair Advantage') }}
                </h2>
                <p class="text-lg text-color-muted">
                    {{ theme('features_subtitle', 'A modern stack engineered for peak performance and scalability.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5 auto-rows-[minmax(160px,auto)]">

                <div class="group relative md:col-span-2 lg:row-span-2 rounded-xl bg-background-secondary/20 backdrop-blur-lg border border-neutral/20 p-8 flex flex-col overflow-hidden hover:border-primary/30 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                    <div class="relative z-10 flex-grow">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                                <x-ri-cpu-line class="size-6" />
                            </div>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-color-base mb-3">{{ theme('feature_1_title', 'Next-Gen Compute') }}</h3>
                        <p class="text-color-muted text-base leading-relaxed max-w-md">
                            {{ theme('feature_1_desc', 'Powered by the latest high-frequency processors and NVMe storage for blisteringly fast I/O.') }}
                        </p>
                    </div>

                    <div class="absolute -bottom-8 -right-8 text-primary/5 group-hover:text-primary/10 transition-colors duration-500">
                        <x-ri-speed-up-fill class="size-48" />
                    </div>
                </div>

                <div class="group relative lg:row-span-2 rounded-xl bg-background-secondary/20 backdrop-blur-lg border border-neutral/20 p-8 flex flex-col overflow-hidden hover:border-primary/30 transition-all duration-500">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                    <div class="flex flex-col h-full justify-between relative z-10">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-6">
                                <x-ri-shield-keyhole-line class="size-6" />
                            </div>
                            <h3 class="text-xl font-bold text-color-base mb-3">{{ theme('feature_2_title', 'Fortified Security') }}</h3>
                            <p class="text-color-muted text-sm leading-relaxed">
                                {{ theme('feature_2_desc', 'Enterprise-grade protection baked into every layer of the infrastructure.') }}
                            </p>
                        </div>
                            
                        <div class="mt-8 space-y-4">
                            <div class="flex items-center gap-3 text-sm font-medium text-color-base">
                                <x-ri-checkbox-circle-fill class="text-primary size-5" /> DDoS Mitigation
                            </div>
                            <div class="flex items-center gap-3 text-sm font-medium text-color-base">
                                <x-ri-checkbox-circle-fill class="text-primary size-5" /> Web App Firewall
                            </div>
                            <div class="flex items-center gap-3 text-sm font-medium text-color-base">
                                <x-ri-checkbox-circle-fill class="text-primary size-5" /> Auto-Patching
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group relative rounded-xl bg-background-secondary/20 backdrop-blur-lg border border-neutral/20 p-8 flex flex-col justify-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-2 mb-2 text-primary">
                        <x-ri-pulse-line class="size-5" />
                        <span class="text-xs font-bold uppercase tracking-widest">{{ theme('feature_3_title', 'Uptime SLA') }}</span>
                    </div>
                    <h4 class="text-4xl md:text-5xl font-bold text-color-base">99.99%</h4>
                </div>

                <div class="group relative rounded-xl bg-background-secondary/20 backdrop-blur-lg border border-neutral/20 p-8 flex flex-col justify-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-2 mb-2 text-primary">
                        <x-ri-map-pin-time-line class="size-5" />
                        <span class="text-xs font-bold uppercase tracking-widest">{{ theme('feature_4_title', 'Locations') }}</span>
                    </div>
                    <h4 class="text-4xl md:text-5xl font-bold text-color-base">15+</h4>
                </div>

                <div class="group relative md:col-span-2 rounded-xl bg-background-secondary/20 backdrop-blur-lg border border-neutral/20 p-8 overflow-hidden hover:border-primary/30 transition-all duration-500">
                    <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
                        <div class="flex-1">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                                <x-ri-stack-line class="size-6" />
                            </div>
                            <h3 class="text-xl font-bold text-color-base mb-2">{{ theme('feature_5_title', 'Instant Scalability') }}</h3>
                            <p class="text-color-muted text-sm max-w-sm">
                                {{ theme('feature_5_desc', 'Scale resources up or down instantly with zero downtime, powered by our elastic cloud architecture.') }}
                            </p>
                        </div>
                        <div class="flex-1 flex gap-3 opacity-20 group-hover:opacity-50 transition-opacity justify-end">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <div class="w-4 h-4 rounded-full bg-primary"></div>
                            <div class="w-6 h-6 rounded-full bg-primary"></div>
                            <div class="w-8 h-8 rounded-full bg-primary"></div>
                        </div>
                    </div>
                </div>

                <div class="group relative md:col-span-1 lg:col-span-2 rounded-xl bg-background-secondary/20 backdrop-blur-lg border border-neutral/20 p-6 flex items-center justify-between overflow-hidden hover:bg-background-secondary/30 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-ri-customer-service-2-fill class="size-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-color-base mb-1">{{ theme('feature_6_title', 'Expert Support') }}</h3>
                            <p class="text-color-muted text-xs">{{ theme('feature_6_desc', '24/7/365 dedicated engineers available to assist you.') }}</p>
                        </div>
                    </div>
                    <x-ri-arrow-right-line class="size-5 text-color-muted group-hover:text-primary group-hover:translate-x-1 transition-all" />
                </div>

            </div>
        </div>
    </section>

    <section class="py-24 bg-background">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-24">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-color-base mb-6">{{ theme('faq_title', 'Common Questions') }}</h2>
                    <p class="text-lg text-color-muted mb-8">
                        {{ theme('faq_subtitle', 'Find answers to common questions about our services and platform.') }}
                    </p>
                    <a href="{{ theme('help_link', '#') }}" class="text-primary font-semibold hover:underline">Visit Knowledge Base &rarr;</a>
                </div>
                
                <div class="space-y-4" x-data="{ active: null }">
                    @php
                    $faqs = [
                        ['q' => 'How long does deployment take?', 'a' => 'Most services are deployed instantly after payment.'],
                        ['q' => 'Do you offer DDoS protection?', 'a' => 'Yes, all our services come with standard DDoS protection included free of charge.'],
                        ['q' => 'Can I upgrade my plan later?', 'a' => 'Absolutely. You can scale your resources up instantly.'],
                    ];
                    @endphp

                    @foreach($faqs as $idx => $faq)
                    <div class="border border-neutral/50 rounded-xl bg-background overflow-hidden">
                        <button @click="active = active === {{ $idx }} ? null : {{ $idx }}" class="w-full flex items-center justify-between p-6 text-left font-bold text-color-base hover:bg-background-secondary/50 transition-colors">
                            <span>{{ theme('faq_business_'.($idx + 1).'_question', $faq['q']) }}</span>
                            <x-ri-add-line class="size-5 text-color-muted transition-transform duration-300" ::class="active === {{ $idx }} ? 'rotate-45' : ''" />
                        </button>
                        <div x-show="active === {{ $idx }}" x-collapse>
                            <div class="p-6 pt-6 text-color-muted leading-relaxed border-t border-neutral/30 bg-background-secondary/20">
                                {{ theme('faq_business_'.($idx + 1).'_answer', $faq['a']) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-background">
        <div class="container mx-auto px-6">
            <div class="relative overflow-hidden rounded-3xl bg-primary px-6 py-20 text-center shadow-2xl sm:px-16 lg:py-24">
                <div class="absolute inset-0 -z-10">
                    <svg class="absolute left-1/2 top-0 -translate-x-1/2 h-full w-full opacity-20" aria-hidden="true">
                        <defs>
                            <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                                <path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="currentColor" stroke-width="2" fill="none"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid-pattern)"/>
                    </svg>
                    <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>

                <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white md:text-4xl mb-6">
                    {{ theme('cta_title', 'Ready to Get Started?') }}
                </h2>
                <p class="mx-auto max-w-xl text-lg text-white/80 mb-10">
                    {{ theme('cta_subtitle', 'Join thousands of developers and businesses building the future with our infrastructure.') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('services') }}" class="w-full sm:w-auto rounded-xl bg-white px-8 py-4 text-base font-bold text-primary shadow-lg hover:bg-gray-50 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        {{ theme('cta_primary_text', 'Deploy Now') }}
                    </a>
                    <a href="{{ theme('help_link', '#') }}" class="w-full sm:w-auto rounded-xl border border-white/30 bg-white/10 px-8 py-4 text-base font-semibold text-white hover:bg-white/20 backdrop-blur-sm transition-all">
                        {{ theme('cta_secondary_text', 'Talk to an Expert') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    @endif

    <div class="container mx-auto my-12 px-6">
        {!! hook('pages.home') !!}
    </div>
</div>