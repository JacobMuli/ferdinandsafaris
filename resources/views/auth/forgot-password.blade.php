<x-auth-layout>
    <div class="mb-6 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">
            Forgot Password?
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-500">
                <i class="fas fa-arrow-left mr-1"></i> Back to Login
            </a>
            <x-primary-button>
                {{ __('Email Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-auth-layout>
