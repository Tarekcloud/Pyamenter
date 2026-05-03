<!DOCTYPE html>
<html class="scroll-smooth" 
      lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      @if(in_array(app()->getLocale(), config('app.rtl_locales'))) dir="rtl" @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="theme-build-id" content="7aa08bc51c6d6a26a4769247ecf9d846"> 
    <meta name="theme-licensee" content="654440">
    
    <script src="https://kit.fontawesome.com/aa65cb4fa4.js" crossorigin="anonymous"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    @php
        $font = theme('change_font_family', 'Figtree');
        $fontUrl = match($font) {
            'Plus Jakarta Sans'    => 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap',
            'Poppins'              => 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100..900;1,100..900&display=swap',
            'Montserrat'           => 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap',
            'Roboto'               => 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap',
            'Inter'                => 'https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100..900;1,100..900&display=swap',
            'Lato'                 => 'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100..900;1,100..900&display=swap',
            'Open Sans'            => 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap',
            'IBM Plex Sans Thai'   => 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&display=swap',
            'Orbitron'             => 'https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap',
            default                => 'https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap',
        };
    @endphp
    <link href="{{ $fontUrl }}" rel="stylesheet">

    @if (config('settings.favicon'))
        <link rel="icon" href="{{ Storage::url(config('settings.favicon')) }}" type="image/png">
    @elseif (config('settings.logo'))
        <link rel="icon" href="{{ Storage::url(config('settings.logo')) }}" type="image/png">
    @endif
    
    <title>
        @if(theme('seo_title'))
            {{ theme('seo_title') }} @isset($title) - {{ $title }} @endisset
        @else
            {{ config('app.name', 'Paymenter') }} @isset($title) - {{ $title }} @endisset
        @endif
    </title>
    
    @if(theme('seo_description'))
        <meta name="description" content="{{ theme('seo_description') }}">
        <meta property="og:description" content="{{ theme('seo_description') }}">
    @endif
    
    @if(theme('seo_keywords'))
        <meta name="keywords" content="{{ theme('seo_keywords') }}">
    @endif
    
    @if(theme('seo_author'))
        <meta name="author" content="{{ theme('seo_author') }}">
    @endif

    @isset($title)
        <meta content="{{ theme('seo_title') ? theme('seo_title') . ' - ' . $title : config('app.name', 'Paymenter') . ' - ' . $title }}" property="og:title">
        <meta content="{{ theme('seo_title') ? theme('seo_title') . ' - ' . $title : config('app.name', 'Paymenter') . ' - ' . $title }}" name="title">
    @else
        <meta content="{{ theme('seo_title') ?: config('app.name', 'Paymenter') }}" property="og:title">
        <meta content="{{ theme('seo_title') ?: config('app.name', 'Paymenter') }}" name="title">
    @endisset
    
    @isset($description)
        <meta content="{{ $description }}" property="og:description">
        <meta content="{{ $description }}" name="description">
    @endisset
    
    @php
        $ogImage = null;
        if(theme('og_image')) {
            $ogImage = theme('og_image');
        } elseif(isset($image) && !empty($image)) {
            $ogImage = $image;
        } elseif(isset($product) && isset($product->image)) {
            $ogImage = $product->image;
        }
    @endphp
    
    @if($ogImage)
        <meta content="{{ $ogImage }}" property="og:image">
        <meta content="{{ $ogImage }}" name="image">
        <meta content="{{ $ogImage }}" property="twitter:image">
    @endif
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Paymenter') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ theme('seo_title') ?: config('app.name', 'Paymenter') }}{{ isset($title) ? ' - ' . $title : '' }}">
    
    @if(theme('seo_description'))
        <meta name="twitter:description" content="{{ theme('seo_description') }}">
    @endif
   
    <meta name="theme-color" content="{{ theme('primary') }}">
    <meta name="locale" content="{{ app()->getLocale() }}">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    @livewireStyles
    @vite(['themes/' . config('settings.theme') . '/js/app.js', 'themes/' . config('settings.theme') . '/css/app.css'], config('settings.theme'))
    @include('layouts.colors')
    
    {!! hook('head') !!}
    
    @if(theme('custom_css'))
        <style>
            {!! theme('custom_css') !!}
        </style>
    @endif
    
    @if(theme('custom_head_html'))
        {!! theme('custom_head_html') !!}
    @endif

</head>

<body class="w-full bg-background text-base min-h-screen flex flex-col antialiased" 
      x-cloak 
      data-license-owner="654440"
      data-license-check="7aa08bc51c6d6a26a4769247ecf9d846"
      x-data="{darkMode: $persist(window.matchMedia('(prefers-color-scheme: dark)').matches)}" 
      :class="{'dark': darkMode}">

    <div x-data="{ loading: true }" 
        x-init="setTimeout(() => loading = false, 150)" 
        x-show="loading"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-background/90 backdrop-blur-xs">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
    
    {!! hook('body') !!}

    @if(theme('custom_body_top_html'))
        {!! theme('custom_body_top_html') !!}
    @endif

    @if(!isset($sidebar) || !$sidebar)
        <x-navigation />
    @endif

    <div class="relative z-10 w-full flex flex-grow">
        
        @if(theme('background_image_url'))
            <div class="background-image-layer"
                 style="position: absolute;
                        top: 0; left: 0; width: 100%; min-height: 30%;
                        background-image: url('{{ theme('background_image_url') }}');
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-attachment: scroll;
                        opacity: {{ theme('background_image_opacity', 30) / 100 }};
                        filter: blur({{ theme('background_image_blur', 5) }}px);
                        z-index: -2;
                        pointer-events: none;
                        transition: opacity 0.5s ease-in-out, filter 0.5s ease-in-out;
                        -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 75%);
                        mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 75%);">
            </div>
        @endif

        @if (isset($sidebar) && $sidebar)
            <x-navigation.sidebar title="$title" />
        @endif

        <svg class="absolute inset-0 -z-10 h-[32rem] w-full stroke-primary/15 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]" aria-hidden="true">
            <defs>
                <pattern id="grid-pattern" width="80" height="80" x="50%" y="-1" patternUnits="userSpaceOnUse">
                    <path d="M.5 80V.5H80" fill="none" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" strokeWidth="0" fill="url(#grid-pattern)" />
        </svg>
        
        <div class="{{ (isset($sidebar) && $sidebar) ? 'md:ml-72 rtl:ml-0 rtl:md:mr-64' : '' }} flex flex-col flex-grow overflow-auto mt-18">
            
            <main class="grow">
                {{ $slot }}
            </main>
            
            <x-notification />
            <x-confirmation />
            
            <div class="mb-3 px-4 lg:px-8 py-8 relative z-0">
                <x-navigation.footer />
                </div>
        </div>
        
        <x-impersonating />
    </div> 
    
    @if(theme('enable_cookies'))
        <div x-data="{ accepted: $persist(false).as('cookie_consent_accepted') }"
             x-show="!accepted"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 z-50 max-w-md p-4 bg-background border border-neutral/50 rounded-xl shadow-2xl"
             style="display: none;">
            
            <div class="flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-background-secondary rounded-full text-primary">
                        <x-ri-cake-3-fill class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-base">
                            {{ theme('cookie_title') ?: 'Cookies?? huh?' }}
                        </h3>
                        <p class="mt-1 text-sm text-base">
                            {{ theme('cookie_text') ?: 'We use cookies to make your experience sweeter and analyze our traffic. No weird stuff, promise.' }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-2">
                    <button @click="accepted = true" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all hover:scale-105 active:scale-95">
                        {{ theme('cookie_button_text') ?: 'Got it!' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    @livewireScriptConfig 
    {!! hook('footer') !!}
    
    @if(theme('custom_js'))
        <script>
            {!! theme('custom_js') !!}
        </script>
    @endif
    
    @if(theme('custom_body_bottom_html'))
        {!! theme('custom_body_bottom_html') !!}
    @endif

</body>
</html>