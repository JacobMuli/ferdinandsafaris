@extends('layouts.admin')

@section('title', 'Tour Guides Management')

@section('subtitle', 'Manage and track your tour guides')

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.guides.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-file-export mr-2"></i>
            Export
        </a>
        <button x-data @click="$dispatch('open-import-modal')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-file-import mr-2"></i>
            Import
        </button>
        <a href="{{ route('admin.guides.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm">
            <i class="fas fa-plus mr-2"></i>
            Add New Guide
        </a>
    </div>

    <!-- Import Modal -->
    <x-admin.partials.import-modal action="{{ route('admin.guides.import') }}" title="Import Guides" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters & Search (Simplified for Guides) -->
    <div class="bg-white  rounded-xl border border-gray-200  p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.guides.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="md:col-span-4 lg:col-span-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300  rounded-lg leading-5 bg-white  text-gray-900  placeholder-gray-500  focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Search guides by name, email or phone...">
                </div>
            </div>
        </form>
    </div>

    <!-- Guides Table -->
    <x-admin.table>
        <x-slot name="head">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Guide Profile
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Contact
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    License
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </x-slot>

        <x-slot name="body">
            @forelse($guides as $guide)
                <tr class="row-hover">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold mr-3">
                                {{ substr($guide->first_name, 0, 1) }}{{ substr($guide->last_name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 ">{{ $guide->first_name }} {{ $guide->last_name }}</div>
                                <div class="text-xs text-gray-500 ">{{ $guide->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600 ">
                            <div class="flex items-center"><i class="fas fa-envelope mr-2 text-gray-400 w-4"></i>{{ $guide->email }}</div>
                            <div class="flex items-center mt-1"><i class="fas fa-phone mr-2 text-gray-400 w-4"></i>{{ $guide->phone }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-gray-100  text-gray-700  px-2 py-1 rounded text-xs font-mono">
                            {{ $guide->license_number }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-green-100 text-green-700   px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.guides.edit', $guide) }}" class="text-blue-600  hover:text-blue-900 ">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.guides.destroy', $guide) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600  hover:text-red-900 ">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 ">
                        <div class="flex flex-col items-center justify-center">
                            <div class="mb-4">
                                <i class="fas fa-user-tie text-4xl text-gray-300 "></i>
                            </div>
                            <p class="text-lg font-medium">No tour guides found</p>
                            <p class="text-sm">Get started by adding a new guide.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-slot>

        <x-slot name="footer">
            {{ $guides->links() }}
        </x-slot>
    </x-admin.table>
</div>
@endsection
