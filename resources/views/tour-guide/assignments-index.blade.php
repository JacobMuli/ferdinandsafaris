@extends('tour-guide.layouts.guide')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 heading-font">My Assignments</h1>
        <p class="text-gray-500">History of all your tour assignments.</p>
    </div>

    @if($assignments->count() > 0)
        <div class="space-y-4">
            @foreach($assignments as $assignment)
                <div class="premium-card p-4 transition-transform active:scale-95">
                    <div class="flex justify-between items-start mb-2">
                        <span class="status-badge {{ 'status-'.$assignment->status }}">
                            {{ $assignment->status }}
                        </span>
                        <span class="text-xs font-semibold text-gray-400">#{{ $assignment->booking->booking_reference }}</span>
                    </div>
                    
                    <h3 class="font-bold text-gray-900 mb-1">{{ $assignment->booking->tour->name }}</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        <i class="far fa-calendar-alt mr-1"></i> {{ $assignment->booking->tour_date->format('M d, Y') }}
                    </p>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-emerald-700 font-bold">${{ number_format($assignment->offered_payment, 2) }}</span>
                        <a href="{{ route('guide.assignments.show', $assignment->id) }}" class="text-sm text-emerald-600 font-bold hover:underline">
                            Details <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $assignments->links() }}
        </div>
    @else
        <div class="premium-card p-12 text-center">
            <i class="fas fa-inbox text-4xl text-gray-200 mb-4"></i>
            <p class="text-gray-500">No assignments found yet.</p>
        </div>
    @endif
@endsection
