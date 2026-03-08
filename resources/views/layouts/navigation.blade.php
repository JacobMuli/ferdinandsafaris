<nav
    x-data="{ open: false, scrolled: false }"
    @scroll.window="scrolled = window.pageYOffset > 20"
    :class="scrolled || {{ request()->routeIs('home') ? 'false' : 'true' }} ? 'bg-white shadow-lg' : 'bg-transparent'"
    class="fixed w-full z-50 transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">

            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <img src="/images/icon-192.png" 
                         alt="Ferdinand Safaris" 
                         class="h-12 w-12 rounded-lg"
                         :class="scrolled || '{{ !request()->routeIs('home') }}' ? '' : 'ring-2 ring-white/30'">
                    <span
                        class="text-2xl font-bold"
                        :class="scrolled || '{{ !request()->routeIs('home') }}' ? 'text-gray-900' : 'text-white'"
                    >
                        Ferdinand Safaris
                    </span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">

                <x-nav-link
                    href="{{ route('home') }}"
                    :active="request()->routeIs('home')"
                    x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                        ? 'text-gray-700  hover:text-emerald-600'
                        : 'text-white hover:text-emerald-200'"
                >
                    {{ __('Home') }}
                </x-nav-link>

                <x-nav-link
                    href="{{ route('tours.index') }}"
                    :active="request()->routeIs('tours.*')"
                    x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                        ? 'text-gray-700  hover:text-emerald-600'
                        : 'text-white hover:text-emerald-200'"
                >
                    {{ __('Tours') }}
                </x-nav-link>

                <x-nav-link
                    href="{{ route('about') }}"
                    :active="request()->routeIs('about')"
                    x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                        ? 'text-gray-700  hover:text-emerald-600'
                        : 'text-white hover:text-emerald-200'"
                >
                    {{ __('About') }}
                </x-nav-link>

                <x-nav-link
                    href="{{ route('contact') }}"
                    :active="request()->routeIs('contact')"
                    x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                        ? 'text-gray-700  hover:text-emerald-600'
                        : 'text-white hover:text-emerald-200'"
                >
                    {{ __('Contact') }}
                </x-nav-link>

                <x-nav-link
                    href="{{ route('community') }}"
                    :active="request()->routeIs('community')"
                    x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                        ? 'text-gray-700  hover:text-emerald-600'
                        : 'text-white hover:text-emerald-200'"
                >
                    {{ __('Community') }}
                </x-nav-link>

                @auth
                    @if(auth()->user()->isAdmin())
                        <x-nav-link
                            href="{{ route('admin.dashboard') }}"
                            x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                                ? 'text-red-600  font-bold hover:text-red-800'
                                : 'text-white font-bold hover:text-red-200'"
                        >
                            <i class="fas fa-shield-alt mr-1"></i> {{ __('Admin Dashboard') }}
                        </x-nav-link>
                    @else
                        <!-- Customer Links removed (now in dropdown) -->
                    @endif

                    <!-- User Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition"
                                x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                                    ? 'text-gray-700  hover:text-emerald-600'
                                    : 'text-white hover:text-emerald-200'"
                            >
                                <div>{{ Auth::user()->name }}</div>
                                <svg class="ml-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(!auth()->user()->isAdmin())
                                <x-dropdown-link href="{{ route('my-bookings') }}">
                                    {{ __('My Bookings') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('chat.index') }}">
                                    {{ __('Support Chat') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('profile.edit') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                            @else
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Administrator') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link
                                    :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                >
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <x-nav-link
                        href="{{ route('login') }}"
                        x-bind:class="scrolled || '{{ !request()->routeIs('home') }}'
                            ? 'text-gray-700  hover:text-emerald-600'
                            : 'text-white hover:text-emerald-200'"
                    >
                        {{ __('Login') }}
                    </x-nav-link>

                    <a
                        href="{{ route('tours.index') }}"
                        class="bg-emerald-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-emerald-700 transition"
                    >
                        Book Now
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button @click="open = !open" :class="scrolled || '{{ !request()->routeIs('home') }}' ? 'text-gray-700' : 'text-white'">
                    <i class="fas text-2xl" :class="open ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="md:hidden bg-white shadow-lg border-t border-gray-200"
    >
        <div class="px-4 pt-4 pb-4 space-y-1">
            <!-- Main Navigation Links -->
            <div class="space-y-1 mb-4">
                <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                    <i class="fas fa-home mr-2"></i>{{ __('Home') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('tours.index') }}" :active="request()->routeIs('tours.*')">
                    <i class="fas fa-map-marked-alt mr-2"></i>{{ __('Tours') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                    <i class="fas fa-info-circle mr-2"></i>{{ __('About') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')">
                    <i class="fas fa-envelope mr-2"></i>{{ __('Contact') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('community') }}" :active="request()->routeIs('community')">
                    <i class="fas fa-users mr-2"></i>{{ __('Community') }}
                </x-responsive-nav-link>
            </div>

            @auth
                <!-- Customer Links -->
                @if(!auth()->user()->isAdmin())
                    <div class="border-t border-gray-200 pt-4 mb-4 space-y-1">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            My Account
                        </div>
                        <x-responsive-nav-link href="{{ route('my-bookings') }}" :active="request()->routeIs('my-bookings')">
                            <i class="fas fa-calendar-check mr-2"></i>{{ __('My Bookings') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('chat.index') }}" :active="request()->routeIs('chat.*')">
                            <i class="fas fa-comments mr-2"></i>{{ __('Support Chat') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')">
                            <i class="fas fa-user-cog mr-2"></i>{{ __('Profile') }}
                        </x-responsive-nav-link>
                    </div>
                @endif

                <!-- Admin Link -->
                @if(auth()->user()->isAdmin())
                    <div class="border-t border-gray-200 pt-4 mb-4">
                        <x-responsive-nav-link href="{{ route('admin.dashboard') }}" class="text-red-600 font-bold">
                            <i class="fas fa-shield-alt mr-2"></i>{{ __('Admin Dashboard') }}
                        </x-responsive-nav-link>
                    </div>
                @endif

                <!-- Logout -->
                <div class="border-t border-gray-200 pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link
                            :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <!-- Guest Links -->
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <x-responsive-nav-link href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt mr-2"></i>{{ __('Login') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link href="{{ route('register') }}">
                        <i class="fas fa-user-plus mr-2"></i>{{ __('Register') }}
                    </x-responsive-nav-link>

                    <a
                        href="{{ route('tours.index') }}"
                        class="block w-full text-center bg-emerald-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition"
                    >
                        <i class="fas fa-compass mr-2"></i>Book Now
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
</style>
