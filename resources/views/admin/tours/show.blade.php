@extends('layouts.admin')

@section('title', $tour->name)

@section('subtitle', 'Detailed view of tour package')

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.tours.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
        <a href="{{ route('admin.tours.edit', $tour) }}" class="btn-primary bg-indigo-600 hover:bg-indigo-700">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Image & Key Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="h-64 w-full relative group">
                <img src="{{ $tour->display_image }}" alt="{{ $tour->name }}" class="w-full h-full object-cover">
                @if(!$tour->featured_image)
                    <div class="absolute inset-0 flex items-center justify-center bg-gray-100 opacity-50">
                        <span class="text-xs text-gray-500">Fallback Image Active</span>
                    </div>
                @endif
                <div class="absolute top-4 right-4 flex gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 backdrop-blur text-gray-800 shadow-sm">
                        {{ $tour->category }}
                    </span>
                    @if($tour->is_featured)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-400/90 backdrop-blur text-yellow-900 shadow-sm">
                            <i class="fas fa-star mr-1"></i> Featured
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-wrap gap-4 mb-6 text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt w-5 text-emerald-600"></i>
                        {{ $tour->location }}
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock w-5 text-emerald-600"></i>
                        {{ $tour->duration_days }} Days
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-tag w-5 text-emerald-600"></i>
                         ${{ number_format((float) $tour->price_per_person, 2) }} / person
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-users w-5 text-emerald-600"></i>
                        Max {{ $tour->max_participants }} people
                    </div>
                </div>

                <div class="prose max-w-none text-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                    <p>{{ $tour->description }}</p>
                </div>

                @if($tour->highlights)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Highlights</h3>
                        <div class="prose max-w-none text-gray-600 bg-gray-50 p-4 rounded-lg">
                            @if(is_array($tour->highlights))
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($tour->highlights as $highlight)
                                        <li>{{ $highlight }}</li>
                                    @endforeach
                                </ul>
                            @else
                                {{-- Fallback for string data --}}
                                {!! nl2br(e((string) $tour->highlights)) !!}
                            @endif
                        </div>
                    </div>
                @endif

                @if($tour->itinerary)
                    <div class="mt-8 border-t border-gray-100 pt-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="p-2 bg-emerald-100/50 rounded-lg">
                                <i class="fas fa-route text-emerald-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Itinerary</h3>
                        </div>

                        <div class="relative border-l-2 border-emerald-100 ml-4 space-y-10 pb-4">
                            @if(is_array($tour->itinerary))
                                @foreach($tour->itinerary as $item)
                                    @php
                                        $day = $item['day'] ?? $loop->iteration;
                                        $title = $item['title'] ?? (is_string($item) ? '' : 'Day ' . $loop->iteration);
                                        $description = $item['description'] ?? (is_string($item) ? $item : '');

                                        if (is_string($item) && !isset($item['description'])) {
                                             $description = $item;
                                             $title = '';
                                        }
                                    @endphp

                                    <div class="relative pl-10 group">
                                        <!-- Dot -->
                                        <div class="absolute -left-[9px] top-1 h-[18px] w-[18px] rounded-full border-4 border-white bg-emerald-200 group-hover:bg-emerald-500 transition-colors shadow-sm"></div>

                                        <!-- Content Box -->
                                        <div class="bg-gray-50/50 hover:bg-white rounded-xl p-5 border border-gray-100 hover:border-emerald-100 transition-all shadow-sm hover:shadow-md">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3 pb-3 border-b border-gray-100/50">
                                                <div class="flex items-center gap-3">
                                                    <span class="inline-flex items-center justify-center h-7 px-3 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm uppercase tracking-wide">
                                                        Day {{ $day }}
                                                    </span>
                                                    @if($title && $title !== "Day $day" && $title !== "Day " . $day)
                                                        <h4 class="text-base font-bold text-gray-900 leading-tight">
                                                            {{ $title }}
                                                        </h4>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="prose prose-sm prose-emerald max-w-none text-gray-600 leading-relaxed font-light">
                                                <p>{{ $description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="pl-8">
                                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 text-gray-600">
                                        {!! nl2br(e((string) $tour->itinerary)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Gallery -->
        @if($tour->gallery_images && count($tour->gallery_images) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Gallery</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($tour->gallery_images as $index => $image)
                        <div class="relative group aspect-square rounded-lg overflow-hidden">
                            <img src="{{ Str::startsWith($image, 'http') ? $image : Storage::url($image) }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <form action="{{ route('admin.tours.delete-gallery-image', ['tour' => $tour, 'index' => $index]) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 text-white p-1.5 rounded-md hover:bg-red-700 shadow-sm" onclick="return confirm('Delete this image?')">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar Stats -->
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Performance</h3>

            <div class="space-y-4">
                <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                    <span class="text-gray-600">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tour->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $tour->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                    <span class="text-gray-600">Total Bookings</span>
                    <span class="font-bold text-gray-900">{{ $stats['total_bookings'] }}</span>
                </div>

                <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                    <span class="text-gray-600">Total Revenue</span>
                    <span class="font-bold text-emerald-600">${{ number_format($stats['revenue'], 2) }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Rating</span>
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                        <span class="font-bold text-gray-900">{{ number_format($stats['avg_rating'], 1) }}</span>
                        <span class="text-gray-400 text-xs ml-1">({{ $stats['total_reviews'] }})</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Recent Bookings</h3>
            <div class="flow-root">
                <ul class="-my-4 divide-y divide-gray-100">
                    @forelse($tour->bookings as $booking)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $booking->customer->name ?? 'Guest' }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $booking->start_date?->format('M d, Y') ?? 'Date not set' }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center text-xs font-semibold text-emerald-600">
                                    ${{ number_format($booking->total_amount) }}
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-4 text-sm text-gray-500 text-center">No bookings yet.</li>
                    @endforelse
                </ul>
            </div>
            <div class="mt-6">
                <a href="{{ route('admin.bookings.index', ['search' => $tour->name]) }}" class="block w-full text-center text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                    View all bookings
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
