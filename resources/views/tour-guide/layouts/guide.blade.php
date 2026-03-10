<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Guide Portal - {{ config('app.name', 'Ferdinand Safaris') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .guide-nav {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .mobile-tab-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            padding-bottom: calc(10px + env(safe-area-inset-bottom));
            z-index: 50;
        }
        .tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 11px;
            font-weight: 500;
        }
        .tab-item.active {
            color: #059669;
        }
        .tab-item i {
            font-size: 20px;
            margin-bottom: 4px;
        }
        .content-area {
            padding-bottom: 100px;
        }
        .premium-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #f3f4f6;
            overflow: hidden;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-accepted { background: #dcfce7; color: #166534; }
        .status-declined { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="antialiased">
    <nav class="guide-nav px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <span class="text-emerald-600 text-2xl font-bold heading-font">FS</span>
            <span class="font-semibold text-gray-800">Guide Portal</span>
        </div>
        <div class="flex items-center space-x-3">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-medium text-gray-500">Welcome,</p>
                <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
            </div>
            <img class="h-8 w-8 rounded-full object-cover border-2 border-emerald-500" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" alt="Avatar">
        </div>
    </nav>

    <main class="content-area container mx-auto px-4 py-6 max-w-lg">
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <div class="mobile-tab-bar">
        <a href="{{ route('guide.dashboard') }}" class="tab-item {{ request()->routeIs('guide.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>Summary</span>
        </a>
        <a href="{{ route('guide.assignments.index') }}" class="tab-item {{ request()->routeIs('guide.assignments.*') ? 'active' : '' }}">
            <i class="fas fa-briefcase"></i>
            <span>Assignments</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="tab-item">
            <i class="fas fa-user-circle"></i>
            <span>Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="tab-item">
            @csrf
            <button type="submit" class="flex flex-col items-center">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</body>
</html>
