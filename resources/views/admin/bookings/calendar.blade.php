@extends('layouts.admin')

@section('title', 'Bookings Calendar')
@section('subtitle', 'Visual overview of bookings and guide assignments')

@section('content')
    @php
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfWeek = $currentDate->dayOfWeek; // 0 (Sun) to 6 (Sat)

        // Adjust for Monday start if needed, but standard is Sunday (0) usually in these grids to match standard calendars.
        // Let's stick to Sunday start for simplicity, or we can check locale.
        // Let's assume Week starts on Sunday.

        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        $monthName = $currentDate->format('F Y');
    @endphp

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <!-- Calendar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">{{ $monthName }}</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.bookings.calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                   class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('admin.bookings.calendar', ['month' => now()->month, 'year' => now()->year]) }}"
                   class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Today
                </a>
                <a href="{{ route('admin.bookings.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                   class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <!-- Calendar Grid Container -->
        <div class="w-full overflow-x-auto">
            <div class="min-w-[1100px] bg-gray-200 rounded-lg overflow-hidden">
                <!-- Days Header -->
                <div class="grid grid-cols-7 gap-px bg-gray-200 border-b border-gray-200">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="bg-gray-50 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                <!-- Days Grid -->
                <div class="grid grid-cols-7 gap-px bg-gray-200 auto-rows-fr">
                    {{-- Leading empty cells --}}
                    @for ($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="min-h-[140px] bg-gray-50"></div>
                    @endfor

                    {{-- Days --}}
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $date = $currentDate->copy()->day($day);
                            $dateString = $date->format('Y-m-d');
                            $daysBookings = $bookings[$dateString] ?? collect();
                            $isToday = $dateString === now()->format('Y-m-d');
                        @endphp

                        <div class="relative min-h-[140px] bg-white hover:bg-gray-50 transition group flex flex-col">
                            {{-- Day header --}}
                            <div class="flex items-center justify-between px-2 py-1.5 border-b border-gray-100 {{ $isToday ? 'bg-blue-50' : '' }}">
                                <span class="text-xs font-semibold {{ $isToday ? 'text-blue-600' : 'text-gray-700' }}">
                                    {{ $day }}
                                </span>

                                @if($daysBookings->count())
                                    <span class="text-[10px] bg-gray-100 text-gray-600 px-1.5 rounded-full font-medium">
                                        {{ $daysBookings->count() }}
                                    </span>
                                @endif
                            </div>

                            {{-- Bookings --}}
                            <div class="flex-1 p-1 space-y-1 overflow-y-auto max-h-[96px] scrollbar-thin scrollbar-thumb-gray-200">
                                @foreach($daysBookings as $booking)
                                    <a
                                        href="{{ route('admin.bookings.show', $booking->id) }}"
                                        class="block text-[10px] leading-tight py-1 transition-all hover:opacity-90 hover:ring-2 hover:ring-opacity-50 hover:z-20 relative
                                            {{ $booking->is_start ? 'rounded-l-md ml-1 pl-2 border-l-2' : '-ml-1 pl-1' }}
                                            {{ $booking->is_end ? 'rounded-r-md mr-1 pr-2' : '-mr-1 pr-1' }}
                                            mb-1 truncate font-medium text-gray-700 shadow-sm"
                                        @class([
                                            'bg-yellow-100 border-yellow-500 hover:ring-yellow-400' => $booking->status === 'pending',
                                            'bg-emerald-100 border-emerald-500 hover:ring-emerald-400' => $booking->status === 'confirmed',
                                            'bg-purple-100 border-purple-500 hover:ring-purple-400' => $booking->status === 'paid',
                                            'bg-blue-100 border-blue-500 hover:ring-blue-400' => $booking->status === 'completed',
                                            'bg-red-100 border-red-500 hover:ring-red-400' => $booking->status === 'cancelled',
                                        ])
                                        title="{{ $booking->tour->name }} ({{ $booking->customer->first_name }})"
                                    >
                                        {{-- Only show text on appropriate days or if generic --}}
                                        @if($booking->is_start || $day == 1 || $date->dayOfWeek == 0)
                                            <span class="truncate block w-full">
                                                {{ $booking->tour->name }}
                                            </span>
                                        @else
                                            <span class="opacity-0 hover:opacity-100 transition-opacity">
                                                 {{ $booking->tour->name }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endfor

                    {{-- Trailing empty cells --}}
                    @php
                        $remaining = (7 - (($firstDayOfWeek + $daysInMonth) % 7)) % 7;
                    @endphp
                    @for ($i = 0; $i < $remaining; $i++)
                        <div class="min-h-[140px] bg-gray-50"></div>
                    @endfor
                </div>
            </div>
        </div>
</div>
@endsection
