@if (config('settings.captcha') !== 'disabled')
    <div class="flex flex-col items-center justify-center bg-background-secondary/50 mt-4 p-4 border border-primary/50 rounded-lg shadow-md">
        <div wire:ignore class="w-full">
            <p class="text-base text-sm text-center">
                Are you a bot?
            </p>
            <p class="text-xs text-center text-base/50 mb-4">
                Please complete the CAPTCHA below to continue.
            </p>
            <div class="flex justify-center">
                @if (config('settings.captcha') == 'recaptcha-v2')
                    <x-captcha.recaptcha-v2 :$form />
                @elseif(config('settings.captcha') == 'recaptcha-v3')
                    <x-captcha.recaptcha-v3 :$form />
                @elseif(config('settings.captcha') == 'turnstile')
                    <x-captcha.turnstile :$form />
                @elseif(config('settings.captcha') == 'hcaptcha')
                    <x-captcha.hcaptcha :$form />
                @endif
            </div>
        </div>
        @error('captcha')
            <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
        @enderror
    </div>
@endif
