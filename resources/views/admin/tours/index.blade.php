@extends('layouts.admin')

@section('title', 'Tours Management')

@section('subtitle', 'Manage and track all tour bookings')

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.tours.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-file-export mr-2"></i>
            Export CSV
        </a>
        <button x-data @click="$dispatch('open-import-modal')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-file-import mr-2"></i>
            Import CSV
        </button>
        <a href="{{ route('admin.tours.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm">
            <i class="fas fa-plus mr-2"></i>
            Create New Tour
        </a>
    </div>

    <!-- Import Modal -->
    <x-admin.partials.import-modal action="{{ route('admin.tours.import') }}" title="Import Tours" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters & Search -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.tours.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">

            <!-- Search -->
            <div class="md:col-span-2 lg:col-span-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                           placeholder="Search tour name or location...">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="lg:col-span-1">
                <select name="category" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-lg" onchange="this.form.submit()">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-1">
                <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-lg" onchange="this.form.submit()">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Clear Filters -->
             <div class="lg:col-span-1">
                <a href="{{ route('admin.tours.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                     Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Data Table -->
    <x-admin.table>
        <x-slot name="head">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tour Details
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Category / Duration
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Price
                </th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Bookings
                </th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Featured
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </x-slot>

        <x-slot name="body">
            @forelse($tours as $tour)
                <tr class="row-hover">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-16">
                                @if($tour->featured_image)
                                    <img class="h-12 w-16 object-cover rounded-md" src="{{ $tour->display_image }}" alt="{{ $tour->name }}">
                                @else
                                    <div class="h-12 w-16 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 ">{{ $tour->name }}</div>
                                <div class="text-xs text-gray-500 "><i class="fas fa-map-marker-alt mr-1"></i>{{ $tour->location }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 ">{{ $tour->category }}</div>
                        <div class="text-xs text-gray-500 ">{{ $tour->duration_days }} Days</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 ">${{ number_format($tour->price_per_person, 2) }}</div>
                        <div class="text-xs text-gray-500 ">per person</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $tour->bookings_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <form action="{{ route('admin.tours.toggle-status', $tour) }}" method="POST">
                            @csrf
                            <button type="submit" class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 {{ $tour->is_active ? 'bg-emerald-600' : 'bg-gray-200' }}" role="switch" aria-checked="{{ $tour->is_active ? 'true' : 'false' }}">
                                <span class="sr-only">Use setting</span>
                                <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $tour->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                            <form action="{{ route('admin.tours.toggle-featured', $tour) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-{{ $tour->is_featured ? 'yellow-400' : 'gray-300' }} hover:text-yellow-500 transition-colors focus:outline-none">
                                <i class="fas fa-star text-lg"></i>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.tours.show', $tour) }}" class="text-blue-600  hover:text-blue-900  mr-3" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.tours.edit', $tour) }}" class="text-indigo-600  hover:text-indigo-900  mr-3" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this tour? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600  hover:text-red-900 " title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-compass text-4xl mb-3 text-gray-300"></i>
                            <p class="text-lg font-medium">No tours found</p>
                            <p class="text-sm">Get started by creating a new tour package</p>
                            <a href="{{ route('admin.tours.create') }}" class="mt-3 text-emerald-600 hover:text-emerald-500 font-medium text-sm">Create Tour &rarr;</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-slot>

        @if($tours->hasPages())
            <x-slot name="footer">
                {{ $tours->withQueryString()->links() }}
            </x-slot>
        @endif
    </x-admin.table>
</div>
@endsection
