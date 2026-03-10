@extends('tour-guide.layouts.guide')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 heading-font">Hello, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
        <p class="text-gray-500">Here is your guide summary for today.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="premium-card p-4 bg-emerald-50 border-emerald-100">
            <div class="text-emerald-600 text-sm font-semibold mb-1">Upcoming</div>
            <div class="text-2xl font-bold text-emerald-800">{{ $upcomingAssignments->count() }}</div>
            <div class="text-xs text-emerald-500">Confirmed Tours</div>
        </div>
        <div class="premium-card p-4 bg-amber-50 border-amber-100">
            <div class="text-amber-600 text-sm font-semibold mb-1">Pending</div>
            <div class="text-2xl font-bold text-amber-800">{{ $pendingAssignments->count() }}</div>
            <div class="text-xs text-amber-500">New Requests</div>
        </div>
    </div>

    <!-- Active/Upcoming Assignment -->
    @if($upcomingAssignments->count() > 0)
        <h2 class="text-lg font-bold text-gray-800 mb-4 heading-font">Next Assignment</h2>
        @php $next = $upcomingAssignments->first(); @endphp
        <div class="premium-card mb-8">
            <div class="relative h-32">
                <img src="{{ $next->booking->tour->featured_image ?? 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e' }}" alt="Tour" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-3 left-4 text-white">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-300">Starts {{ $next->booking->tour_date->format('M d') }}</p>
                    <h3 class="font-bold text-lg leading-tight">{{ $next->booking->tour->name }}</h3>
                </div>
            </div>
            <div class="p-4">
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <i class="fas fa-users w-5 text-emerald-500"></i>
                    <span>{{ $next->booking->total_participants }} Participants</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 mb-4">
                    <i class="fas fa-map-marker-alt w-5 text-emerald-500"></i>
                    <span>{{ $next->booking->tour->location }}</span>
                </div>
                <a href="{{ route('guide.assignments.show', $next->id) }}" class="block w-full text-center py-3 bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-200">
                    View Manifest
                </a>
            </div>
        </div>
    @endif

    <!-- Pending Requests -->
    @if($pendingAssignments->count() > 0)
        <h2 class="text-lg font-bold text-gray-800 mb-4 heading-font">Pending Invitations</h2>
        <div class="space-y-4">
            @foreach($pendingAssignments as $assignment)
                <div class="premium-card p-4 flex items-center justify-between border-l-4 border-amber-400">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $assignment->booking->tour->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $assignment->booking->tour_date->format('M d, Y') }} • ${{ number_format($assignment->offered_payment, 0) }}</p>
                    </div>
                    <a href="{{ route('guide.assignments.show', $assignment->id) }}" class="px-4 py-2 bg-amber-100 text-amber-700 rounded-lg text-sm font-bold">
                        Review
                    </a>
                </div>
            @endforeach
        </div>
    @else
        @if($upcomingAssignments->count() == 0)
            <div class="premium-card p-8 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-check text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-600 font-medium">No active assignments</p>
                <p class="text-gray-400 text-sm mt-1">Assignments will appear here when you are matched with a tour.</p>
            </div>
        @endif
    @endif
@endsection
