<header class="flex items-center justify-between px-6 py-4
    bg-gradient-to-r from-blue-900 to-emerald-600
    shadow-md h-16 z-30">

    <!-- LEFT -->
    <div class="flex items-center gap-4">
        <!-- Desktop Toggle -->
        <button
            @click="desktopSidebarOpen = !desktopSidebarOpen"
            class="hidden md:flex p-2 rounded-lg text-white hover:bg-white/10 focus:outline-none transition-colors"
            title="Toggle Sidebar">
            <i class="fas fa-bars text-xl"></i>
        </button>

        <!-- Mobile Toggle -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="md:hidden p-2 rounded-lg
                   text-white hover:bg-white/10
                   focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>

        <div class="flex items-center gap-3">
            <img src="/images/icon-192.png" alt="Logo" class="h-8 w-8 rounded shadow-sm">
            <h2 class="text-lg font-bold text-white uppercase tracking-tight hidden sm:block">
                Ferdinand <span class="font-light">Safaris</span>
            </h2>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" target="_blank"
           class="p-2 rounded-lg text-white hover:bg-white/10">
            <i class="fas fa-globe"></i>
        </a>

        <!-- Notifications -->
        <x-dropdown align="right" width="64">
            <x-slot name="trigger">
                <button class="relative p-2 rounded-lg text-white hover:bg-white/10">
                    <i class="fas fa-bell"></i>

                    @if(auth()->user()->unreadNotifications->count())
                        <span class="absolute top-1.5 right-1.5
                            h-2.5 w-2.5 bg-red-500 rounded-full
                            ring-2 ring-emerald-600"></span>
                    @endif
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-2 text-xs font-semibold uppercase text-gray-500">
                    Notifications
                </div>

                <div class="max-h-72 overflow-y-auto divide-y">
                    @forelse(auth()->user()->unreadNotifications as $notification)
                        <div class="px-4 py-3 hover:bg-gray-50 transition">
                            <p class="text-sm text-gray-800">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                            No new notifications
                        </div>
                    @endforelse
                </div>
            </x-slot>
        </x-dropdown>

        <!-- User Menu -->
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 text-white hover:text-emerald-100">
                    <div class="h-8 w-8 rounded-full bg-white/20
                        flex items-center justify-center font-bold">
                        {{ substr(auth()->user()->first_name, 0, 1) }}
                    </div>
                    <span class="hidden sm:inline">
                        {{ auth()->user()->first_name }}
                    </span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    Profile
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
