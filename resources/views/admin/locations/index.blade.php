@extends('layouts.admin')

@section('title', 'Destinations Management')

@section('subtitle', 'Manage all available tour destinations')

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.locations.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-file-export mr-2"></i>
            Export
        </a>
        <button x-data @click="$dispatch('open-import-modal')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-file-import mr-2"></i>
            Import
        </button>
        <a href="{{ route('admin.locations.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm">
            <i class="fas fa-plus mr-2"></i>
            Add New Destination
        </a>
    </div>

    <!-- Import Modal -->
    <x-admin.partials.import-modal action="{{ route('admin.locations.import') }}" title="Import Locations" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters & Search -->
    <div class="bg-white  rounded-xl border border-gray-200  p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.locations.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="md:col-span-4 lg:col-span-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300  rounded-lg leading-5 bg-white  text-gray-900  placeholder-gray-500  focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Search destinations by name or country...">
                </div>
            </div>
        </form>
    </div>

    <!-- Locations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($locations as $location)
            <div class="bg-white  rounded-xl shadow-sm border border-gray-100  overflow-hidden hover:shadow-md transition-shadow duration-200 group">
                <div class="relative h-48 bg-gray-200 ">
                    <img src="{{ $location->featured_image_url }}"
                         alt="{{ $location->name }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute top-4 right-4 bg-white/90  backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-gray-800  uppercase tracking-wide shadow-sm">
                        {{ $location->country }}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-xl text-gray-900  mb-2">{{ $location->name }}</h3>
                    <p class="text-gray-500  text-sm mb-4 line-clamp-2">{{ $location->description }}</p>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 ">
                        <span class="text-sm font-medium text-emerald-600 ">
                            {{ ucfirst($location->type) }}
                        </span>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.locations.edit', $location) }}" class="text-gray-400 hover:text-emerald-600  transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500  transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white  rounded-xl shadow-sm border border-gray-100  p-12 text-center text-gray-500 ">
                <div class="mb-4">
                    <i class="fas fa-map-marked-alt text-4xl text-gray-300 "></i>
                </div>
                <p class="text-lg font-medium">No destinations found</p>
                <p class="text-sm">Get started by adding a new location.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $locations->links() }}
    </div>
</div>
@endsection
