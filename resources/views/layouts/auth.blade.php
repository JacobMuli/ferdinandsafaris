<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ferdinand Safaris') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])


    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 ">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo -->
            <div class="mb-6">
                <a href="/" class="flex items-center space-x-2">
                    <i class="fas fa-mountain text-5xl text-emerald-600"></i>
                    <span class="text-2xl font-bold text-gray-900 ">Ferdinand Safaris</span>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white  shadow-lg overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <!-- Footer Link -->
            <div class="mt-6 text-center">
                <a href="/" class="text-sm text-emerald-600 hover:text-emerald-500">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to home
                </a>
            </div>
        </div>
    </body>
</html>