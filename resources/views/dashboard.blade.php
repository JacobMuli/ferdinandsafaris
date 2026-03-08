<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('My Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg transition-colors">
                <div class="p-6 text-gray-900 ">
                    <h3 class="text-2xl font-bold mb-4">Welcome back, {{ Auth::user()->name }}!</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Upcoming Trips Card -->
                        <div class="bg-emerald-50  border border-emerald-200  rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-emerald-100  text-emerald-600 ">
                                    <i class="fas fa-plane-departure text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 ">Upcoming Trips</p>
                                    <p class="text-2xl font-semibold text-gray-900 ">{{ $upcomingTrips }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Past Trips Card -->
                        <div class="bg-blue-50  border border-blue-200  rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100  text-blue-600 ">
                                    <i class="fas fa-history text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 ">Past Trips</p>
                                    <p class="text-2xl font-semibold text-gray-900 ">{{ $pastTrips }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="text-xl font-bold mb-4 text-gray-800 ">Recent Bookings</h4>
                    @if($bookings->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200 ">
                            <table class="min-w-full divide-y divide-gray-200 ">
                                <thead class="bg-gray-50 ">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Tour</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white  divide-y divide-gray-200 ">
                                    @foreach($bookings as $booking)
                                        <tr class="hover:bg-gray-50  transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 ">{{ $booking->tour->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 ">{{ $booking->tour_date->format('M d, Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $colors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'confirmed' => 'bg-green-100 text-green-800',
                                                        'completed' => 'bg-blue-100 text-blue-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
                                                ${{ number_format($booking->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('bookings.show', $booking->reference ?? $booking->id) }}" class="text-emerald-600 hover:text-emerald-900  ">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500  mb-4">You haven't booked any tours yet.</p>
                            <a href="{{ route('tours.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                                Explore Tours
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
