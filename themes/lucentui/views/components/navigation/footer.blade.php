<footer class="w-full rounded-xl bg-background-secondary/20 backdrop-blur-md border border-neutral/50 shadow-xl mt-4">
    <!-- 恢复原本的 my-12 上下边距 -->
    <div class="max-w-7xl my-12 mx-auto px-6 sm:px-8 md:px-10 lg:px-12">
        <!-- 仅调整左右布局：改为 md:grid-cols-5 确保在中大屏幕强制单行显示，且间距等分 -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 md:gap-4 lg:gap-8 items-start w-full">

            <!-- Column 1: Logo and Copyright -->
            <div class="flex flex-col gap-6 items-start">
                <div class="flex flex-row gap-2">
                    <x-logo class="h-10" />
                    @if(theme('logo_display', 'logo-and-name') != 'logo-only')
                    <!-- <span class="text-xl font-bold leading-none flex items-center">{{ config('app.name') }}</span> -->
                    @endif
                </div>

                <div class="text-sm text-base/70 leading-6 max-w-[220px]">
                    {{ __('Copyright © :year TarekCloud. All rights reserved.', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
                </div>
                
            </div>

            <!-- Column 2: Service -->
            <div class="flex flex-col gap-4">
                <h3 class="text-lg font-bold text-base-content">Service</h3>
                <div class="flex flex-col gap-3">
                    <a href="/products/dedicated-servers" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Dedicated Servers
                    </a>
                    <a href="/products/hk-standard-servers" class="text-sm text-base/70 hover:text-primary transition-colors">
                        VPS Hosting
                    </a>
                    <a href="/colocation" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Datacenter Colocations
                    </a>
                    <a href="/company" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Company Services
                    </a>
                </div>
            </div>

            <!-- Column 3: Network -->
            <div class="flex flex-col gap-4">
                <h3 class="text-lg font-bold text-base-content">Network</h3>
                <div class="flex flex-col gap-3">
                    <a href="https://status.altarek.cloud" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Services Status
                    </a>
                    <a href="/products/lir-services" class="text-sm text-base/70 hover:text-primary transition-colors">
                        IPv4, IPv6 and ASNs
                    </a>
                    <a href="https://lg.altarek.cloud" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Looking Glass
                    </a>
                </div>
            </div>

            <!-- Column 4: Company -->
            <div class="flex flex-col gap-4">
                <h3 class="text-lg font-bold text-base-content">Company</h3>
                <div class="flex flex-col gap-3">
                    <a href="/about" class="text-sm text-base/70 hover:text-primary transition-colors">
                        About
                    </a>
                    <a href="/contact" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Contact
                    </a>
                    <a href="/help" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Help Center
                    </a>
                </div>
            </div>

            <!-- Column 5: Policy -->
            <div class="flex flex-col gap-4">
                <h3 class="text-lg font-bold text-base-content">Policy</h3>
                <div class="flex flex-col gap-3">
                    <a href="/privacy-policy" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Privacy Policy
                    </a>
                    <a href="/terms-of-service" class="text-sm text-base/70 hover:text-primary transition-colors">
                        Terms of Service
                    </a>
                </div>
            </div>

        </div>
    </div>
</footer>