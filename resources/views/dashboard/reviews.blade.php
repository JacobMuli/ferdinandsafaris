<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Reviews') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold">Review History</h3>
                        <p class="text-sm text-gray-500">Track and manage the feedback you've shared.</p>
                    </div>

                    @if($reviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border rounded-xl p-6 hover:border-emerald-200 transition-colors bg-gray-50/50">
                                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="flex text-yellow-400 text-sm">
                                                    @for ($i = 0; $i < 5; $i++)
                                                        <i class="fas fa-star {{ $i < $review->rating ? '' : 'text-gray-200' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $review->is_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $review->is_approved ? 'Approved' : 'Pending Approval' }}
                                                </span>
                                            </div>
                                            <h4 class="font-bold text-gray-900 mb-1">
                                                <a href="{{ route('tours.show', $review->tour) }}" class="hover:text-emerald-600 transition">
                                                    {{ $review->tour->name }}
                                                </a>
                                            </h4>
                                            <p class="text-gray-600 italic">"{{ $review->comment }}"</p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-xs text-gray-400">Submitted on</p>
                                            <p class="text-sm font-medium">{{ $review->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $reviews->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-star text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No reviews yet</h3>
                            <p class="text-gray-500 mb-6">Complete a safari and share your experience with the community.</p>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                                Go to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
