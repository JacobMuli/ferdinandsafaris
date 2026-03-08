<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Two Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Add additional security to your account using two factor authentication.') }}
        </p>
    </header>

    <div class="mt-6">
        @if(! auth()->user()->two_factor_secret)
            {{-- Enable 2FA --}}
            <h3 class="text-lg font-medium text-gray-900">
                {{ __('You have not enabled two factor authentication.') }}
            </h3>

            <p class="mt-3 max-w-xl text-sm text-gray-600">
                {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
            </p>

            <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-4">
                @csrf
                <x-primary-button type="submit">
                    {{ __('Enable') }}
                </x-primary-button>
            </form>
        @else
            {{-- 2FA is Enabled --}}
            <h3 class="text-lg font-medium text-gray-900">
                {{ __('You have enabled two factor authentication.') }}
            </h3>

            <p class="mt-3 max-w-xl text-sm text-gray-600">
                {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication.') }}
            </p>

            <div class="mt-4">
                @if(session('status') == 'two-factor-authentication-enabled')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.') }}
                    </div>

                    <div class="mb-4">
                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                    </div>

                    <div class="mb-4">
                        <p class="font-semibold text-sm mb-2">Recovery Codes:</p>
                        <ul class="bg-gray-100 p-4 rounded-lg text-xs font-mono">
                            @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                <li>{{ $code }}</li>
                            @endforeach
                        </ul>
                        <p class="text-xs text-gray-500 mt-2">Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.</p>
                    </div>
                @endif

                {{-- Show Recovery Codes (if not just enabled) --}}
                @if(session('status') != 'two-factor-authentication-enabled')
                     <div class="mt-4">
                        <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-900 underline">
                                {{ __('Regenerate Recovery Codes') }}
                            </button>
                        </form>
                    </div>
                @endif

                <div class="mt-4 flex items-center gap-4">
                    {{-- Disable 2FA --}}
                    <form method="POST" action="{{ route('two-factor.disable') }}">
                        @csrf
                        @method('DELETE')
                        <x-danger-button type="submit">
                            {{ __('Disable') }}
                        </x-danger-button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</section>
