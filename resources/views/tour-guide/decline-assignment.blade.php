@extends('tour-guide.layouts.guide')

@section('content')
    <div class="mb-4">
        <a href="{{ route('guide.assignments.show', $assignment->id) }}" class="text-emerald-600 text-sm font-semibold mb-2 inline-flex items-center">
            <i class="fas fa-chevron-left mr-1"></i> Back to Invitation
        </a>
        <h1 class="text-2xl font-bold text-gray-900 heading-font">Decline Assignment</h1>
    </div>

    <div class="premium-card p-6">
        <p class="text-sm text-gray-600 mb-6">We're sorry you can't make this trip. Please let us know the reason so we can improve our scheduling.</p>
        
        <form action="{{ route('guide.assignments.decline.submit', $assignment->id) }}" method="POST">
            @csrf
            <div class="mb-6">
                <label for="reason" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Reason for Declining</label>
                <textarea 
                    id="reason" 
                    name="reason" 
                    rows="4" 
                    class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                    placeholder="E.g., I have a conflicting tour, personal reasons, etc."
                    required
                ></textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-amber-50 rounded-xl p-4 mb-8">
                <p class="text-xs text-amber-800 flex">
                    <i class="fas fa-info-circle mt-1 mr-2"></i>
                    Declining assignments too frequently may affect your guide rating and future invitations.
                </p>
            </div>

            <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-xl font-bold shadow-lg shadow-red-200">
                Confirm Decline
            </button>
        </form>
    </div>
@endsection
