<x-auth-layout>
    <div class="mb-6 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 ">
            Create your account
        </h2>
        <p class="mt-2 text-sm text-gray-600 ">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-emerald-600  hover:text-emerald-500">
                Sign in
            </a>
        </p>
    </div>

    <div class="mb-10">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-600 font-bold uppercase tracking-wider">Register with Social Account</span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <a href="{{ route('social.redirect', 'google') }}" class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-200 hover:shadow-md active:scale-95">
                <i class="fab fa-google text-red-600 mr-2 text-lg"></i>
                Google
            </a>

            <a href="{{ route('social.redirect', 'facebook') }}" class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-200 hover:shadow-md active:scale-95">
                <i class="fab fa-facebook text-blue-600 mr-2 text-lg"></i>
                Facebook
            </a>
        </div>

        <div class="relative mt-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500 uppercase text-xs tracking-widest font-medium">Or create an email account</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600  hover:text-gray-900  rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 " href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

</x-auth-layout>