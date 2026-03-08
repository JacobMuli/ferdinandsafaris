<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/icon-192.png">
    <link rel="shortcut icon" type="image/png" href="/images/icon-192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- App -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/80 z-30 md:hidden" style="display: none;"></div>

        <!-- Sidebar -->
        <aside
            class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-blue-900 to-emerald-900 text-white z-40 hidden md:flex flex-col">
            @include('layouts.admin-sidebar')
        </aside>

        <!-- Main wrapper -->
        <div class="flex-1 md:ml-64 flex flex-col">

            <!-- Topbar -->
            <header
                class="fixed top-0 left-0 right-0 md:left-64 h-16 z-30 bg-gradient-to-r from-blue-900 to-emerald-600 shadow">
                @include('layouts.admin-topbar')
            </header>

            <!-- Content -->
            <main class="pt-20 px-6 pb-10 max-w-[1600px] mx-auto w-full">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-100 text-emerald-800 px-4 py-3 rounded-lg shadow">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 text-red-800 px-4 py-3 rounded-lg shadow">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Page Header --}}
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500">
                                @yield('subtitle')
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            @yield('actions')
                        </div>
                    </div>
                </div>

                @yield('content')
            </main>

        </div>
    </div>



</body>

</html>