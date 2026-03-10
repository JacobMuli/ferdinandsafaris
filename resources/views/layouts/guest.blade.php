<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ferdinand Safaris') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/images/icon-192.png">
        <link rel="shortcut icon" type="image/png" href="/images/icon-192.png">
        
        <!-- PWA Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#059669">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Ferdinand Safaris">
        <link rel="apple-touch-icon" href="/images/icon-192.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            [x-cloak] { display: none !important; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- SEO Meta Tags -->
        @stack('meta')



        <!-- Additional Styles -->
        {{ $header ?? '' }}
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="{{ request()->routeIs('home') ? '' : 'pt-20' }}">
                {{ $slot }}
            </main>

            @include('layouts.footer')

            <!-- AI Chatbot Assistant -->
            <x-chatbot />
        </div>

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => console.log('SW registered:', registration))
                        .catch(error => console.log('SW registration failed:', error));
                });
            }
        </script>
    </body>
</html>