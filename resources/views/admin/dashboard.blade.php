@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900  tracking-tight">Admin Dashboard</h1>
            <p class="text-sm text-gray-500  mt-1">
                Overview of your platform's performance
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
             <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50  text-blue-700  border border-blue-100 ">
                <i class="fas fa-calendar mr-2"></i> {{ now()->format('M d, Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="relative overflow-hidden bg-white  rounded-xl shadow-lg p-6 group hover:scale-[1.02] transition-transform duration-300 border border-gray-100 ">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-100  rounded-lg text-emerald-600 ">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                    @if($metrics['revenue_growth'] >= 0)
                        <span class="flex items-center text-sm font-semibold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full">
                            <i class="fas fa-arrow-up text-xs mr-1"></i> {{ $metrics['revenue_growth'] }}%
                        </span>
                    @else
                        <span class="flex items-center text-sm font-semibold text-red-600 bg-red-100 px-2 py-1 rounded-full">
                            <i class="fas fa-arrow-down text-xs mr-1"></i> {{ abs($metrics['revenue_growth']) }}%
                        </span>
                    @endif

                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        ${{ number_format($metrics['total_revenue']) }}
                    </p>
                </div>
                <h3 class="text-gray-500  text-sm font-medium uppercase tracking-wider">Total Revenue</h3>
                <p class="text-xs text-gray-500  mt-2">from last month</p>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="relative overflow-hidden bg-white  rounded-xl shadow-lg p-6 group hover:scale-[1.02] transition-transform duration-300 border border-gray-100 ">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100  rounded-lg text-blue-600 ">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <span class="flex items-center text-sm font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                        <i class="fas fa-plus text-xs mr-1"></i>
                        {{ $metrics['new_bookings_count'] }}
                    </span>

                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ $metrics['total_bookings'] }}
                    </p>
                </div>
                <h3 class="text-gray-500  text-sm font-medium uppercase tracking-wider">Total Bookings</h3>
                <p class="text-xs text-gray-500  mt-2">Valid reservations</p>
            </div>
        </div>

        <!-- Active Tours -->
        <div class="relative overflow-hidden bg-white  rounded-xl shadow-lg p-6 group hover:scale-[1.02] transition-transform duration-300 border border-gray-100 ">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl group-hover:bg-purple-500/20 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-100  rounded-lg text-purple-600 ">
                        <i class="fas fa-map-marked-alt text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-500  text-sm font-medium uppercase tracking-wider">Active Tours</h3>
                <p class="text-3xl font-bold text-gray-900  mt-1">{{ $metrics['active_tours'] }}</p>
                <p class="text-xs text-gray-500  mt-2">Currently running</p>
            </div>
        </div>

        <!-- Pending Inquiries -->
        <div class="relative overflow-hidden bg-white  rounded-xl shadow-lg p-6 group hover:scale-[1.02] transition-transform duration-300 border border-gray-100 ">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-orange-500/10 rounded-full blur-xl group-hover:bg-orange-500/20 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-100  rounded-lg text-orange-600 ">
                        <i class="fas fa-envelope-open-text text-xl"></i>
                    </div>
                    <span class="flex items-center text-sm font-semibold text-orange-600  bg-orange-100  px-2 py-1 rounded-full">
                        Action Needed
                    </span>
                </div>
                <h3 class="text-gray-500  text-sm font-medium uppercase tracking-wider">Pending Bookings</h3>
                <p class="text-3xl font-bold text-gray-900  mt-1">{{ $metrics['pending_bookings'] }}</p>
                <p class="text-xs text-gray-500  mt-2">Requires confirmation</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Bookings Carousel -->
        <div class="bg-white  rounded-xl shadow-lg border border-gray-100  flex flex-col h-[400px]">
             <div class="px-6 py-4 border-b border-gray-100  flex justify-between items-center bg-gray-50  z-10 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-800 ">Recent Bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700  ">View All</a>
            </div>

            <div class="flex-1 overflow-y-auto relative custom-scrollbar">
                @forelse($recentBookings as $booking)
                    <div class="px-6 py-4 border-b border-gray-100  hover:bg-gray-50  transition-colors flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-gray-900  text-sm">#{{ $booking->booking_reference }}</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                    @if($booking->status === 'confirmed' || $booking->status === 'paid') bg-green-100 text-green-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800   @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center min-w-0 flex-1 mr-2">
                                    <div class="h-6 w-6 rounded-full bg-emerald-100  flex items-center justify-center text-emerald-700  font-bold text-[10px] mr-2">
                                        {{ strtoupper(substr($booking->customer->first_name, 0, 1)) }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate min-w-0">
                                        {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}
                                    </div>
                                </div>
                                <div class="font-semibold text-gray-900  text-sm">
                                    ${{ number_format($booking->total_amount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 ">
                        No recent bookings.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Top Performing Tours Carousel -->
        <div class="bg-white  rounded-xl shadow-lg border border-gray-100  flex flex-col h-[400px]">
            <div class="px-6 py-4 border-b border-gray-100  bg-gray-50  z-10 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-800 ">Top Performing Tours</h2>
            </div>
            <div class="flex-1 overflow-y-auto relative custom-scrollbar">
                @forelse($topTours as $tour)
                    <div class="px-6 py-4 border-b border-gray-100  hover:bg-gray-50  transition-colors flex items-center">
                        <img src="{{ $tour->display_image }}"
                             class="h-10 w-10 rounded-lg object-cover mr-3 shadow-sm border border-gray-200  flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900  truncate">{{ $tour->name }}</div>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-blue-600  bg-blue-50  px-2 py-0.5 rounded-md mr-2">
                                    {{ $tour->bookings_count }} Bookings
                                </span>
                                <span class="text-xs font-semibold text-emerald-600 ">
                                    ${{ number_format($tour->revenue) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 ">
                        No tour data available.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection