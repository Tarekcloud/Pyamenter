<footer class="w-full px-4 py-4 lg:mt-72 mt-44 bg-background-secondary border-t border-neutral">
    <div class="container my-12 mx-auto px-4 sm:px-6 md:px-8 lg:px-10">
        <div class="flex flex-col md:flex-row justify-between gap-8 md:gap-10">

            <!-- 左侧：Logo 和版权信息 -->
            <div class="flex flex-col gap-6 items-start md:min-w-[220px]">
                <div class="flex flex-row gap-2">
                    <x-logo class="h-10" />
                    @if(theme('logo_display', 'logo-and-name') != 'logo-only')
                    <!-- <span class="text-xl font-bold leading-none flex items-center">{{ config('app.name') }}</span> -->
                    @endif
                </div>
<p class="text-xs font-medium text-base/70 leading-5 max-w-xs">
    Tarekcloud specializes in cloud computing consulting, </br>network infrastructure design, and</br> offers a comprehensive suite of services</br> including dedicated servers, data center</br> colocation, virtual private servers, and IP</br> transit.
</p>
                <div class="text-sm text-base/70">
                    {{ __('Copyright © :year TarekCloud. All rights reserved.', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
                </div>
            </div>

            <!-- 右侧：三列 -->
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">

                <!-- 第一列：Service -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-bold text-base-content">Service</h3>
                    <div class="flex flex-col gap-3">
                        <a href="/products/dedecated-servers" class="text-sm text-base/70 hover:text-primary transition-colors">
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

                <!-- 第二列：Network -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-bold text-base-content">Network</h3>
                    <div class="flex flex-col gap-3">
                        <a href="/status" class="text-sm text-base/70 hover:text-primary transition-colors">
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

                <!-- 第三列：Company -->
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
    </div>
</footer>