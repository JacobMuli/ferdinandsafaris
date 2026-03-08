@extends('layouts.admin')

@section('title', 'Bookings Management')

@section('subtitle', 'Manage and Track all your bookings')

@section('actions')
    <a href="{{ route('admin.bookings.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
        <i class="fas fa-download mr-2 text-gray-400"></i>
        Export CSV
    </a>
    <a href="{{ route('admin.bookings.calendar') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 transition-colors shadow-sm">
        <i class="fas fa-calendar-alt mr-2"></i>
        Calendar View
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters & Search -->
    <div class="bg-white  rounded-xl border border-gray-200  p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">

            <!-- Search -->
            <div class="md:col-span-2 lg:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300  rounded-lg leading-5 bg-white  text-gray-900  placeholder-gray-500  focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Search reference, email, name...">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300  bg-white  text-gray-900  focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg" onchange="this.form.submit()">
                    <option value="all">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Payment Filter -->
            <div>
                <select name="payment_status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300  bg-white  text-gray-900  focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg" onchange="this.form.submit()">
                    <option value="all">Payment Status</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <!-- Date Range (From) -->
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="block w-full pl-3 pr-3 py-2 border border-gray-300  bg-white  text-gray-900  rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm placeholder-gray-400"
                       onchange="this.form.submit()">
            </div>

            <!-- Date Range (To) -->
            <div>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="block w-full pl-3 pr-3 py-2 border border-gray-300  bg-white  text-gray-900  rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm placeholder-gray-400"
                       onchange="this.form.submit()">
            </div>

        </form>
    </div>

    <!-- Data Table -->
    <x-admin.table>
        <x-slot name="head">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Reference / Tour
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Customer
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Amount
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Payment
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </x-slot>

        <x-slot name="body">
            @forelse($bookings as $booking)
                <tr class="row-hover">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-900  font-mono">#{{ $booking->booking_reference }}</span>
                            <span class="text-sm text-gray-500  mt-0.5">{{ $booking->tour->name ?? 'Deleted Tour' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900 ">{{ $booking->customer->first_name ?? 'Guest' }} {{ $booking->customer->last_name ?? '' }}</span>
                            <span class="text-xs text-gray-500 ">{{ $booking->customer->email ?? $booking->email ?? 'No Email' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 ">
                            {{ $booking->tour_date ? $booking->tour_date->format('M d, Y') : 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-900">
                                ${{ number_format($booking->actual_price ?? $booking->total_amount, 2) }}
                            </span>
                            @if(!$booking->actual_price)
                                <span class="text-[10px] uppercase tracking-wider font-bold text-yellow-500">Estimate</span>
                            @else
                                <span class="text-[10px] uppercase tracking-wider font-bold text-emerald-500">Billed</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-emerald-100 text-emerald-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusClass = $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <div class="flex flex-col gap-1">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                            @if($booking->status === 'pending' && !$booking->actual_price)
                                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold bg-red-50 text-red-600 rounded border border-red-100 uppercase tracking-tighter justify-center">
                                    Needs Price
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $paymentClasses = [
                                'unpaid' => 'bg-gray-100 text-gray-500',
                                'partial' => 'bg-orange-100 text-orange-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'refunded' => 'bg-purple-100 text-purple-800',
                            ];
                            $paymentClass = $paymentClasses[$booking->payment_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentClass }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600  hover:text-blue-900  mr-3">View</a>

                        @if($booking->status === 'pending')
                            <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to confirm this booking?')">
                                @csrf
                                <button type="submit" class="text-emerald-600  hover:text-emerald-900 ">Confirm</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 ">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300 "></i>
                            <p class="text-lg font-medium">No bookings found</p>
                            <p class="text-sm">Try adjusting your filters or search terms</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-slot>

        @if($bookings->hasPages())
            <x-slot name="footer">
                {{ $bookings->withQueryString()->links() }}
            </x-slot>
        @endif
    </x-admin.table>
</div>
@endsection
