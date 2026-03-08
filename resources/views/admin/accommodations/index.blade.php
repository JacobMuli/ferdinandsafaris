@extends('layouts.admin')

@section('title', 'Accommodations')

@section('subtitle', 'Manage lodges, hotels, and camps')

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.accommodations.export') }}" class="btn-secondary">
            <i class="fas fa-file-export mr-2"></i> Export
        </a>
        <button x-data @click="$dispatch('open-import-modal')" class="btn-secondary">
            <i class="fas fa-file-import mr-2"></i> Import
        </button>
        <a href="{{ route('admin.accommodations.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i> Add Accommodation
        </a>
    </div>

    <!-- Import Modal -->
    <x-admin.partials.import-modal action="{{ route('admin.accommodations.import') }}" title="Import Accommodations" />
@endsection

@section('content')
    <x-admin.table>
        <x-slot name="head">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type / Category</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rooms</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (Std)</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($accommodations as $accommodation)
                <tr class="row-hover">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 ">{{ $accommodation->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 ">{{ ucfirst($accommodation->type) }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst($accommodation->category) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 ">{{ $accommodation->city }}, {{ $accommodation->country }}</div>
                        <div class="text-xs text-gray-500">{{ $accommodation->parkLocation->name ?? 'No Park' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
                        {{ $accommodation->total_rooms }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
                        ${{ number_format($accommodation->price_per_night_standard, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.accommodations.edit', $accommodation) }}" class="text-indigo-600  hover:text-indigo-900  mr-3">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No accommodations found.</td>
                </tr>
            @endforelse
        </x-slot>
        <x-slot name="footer">
            {{ $accommodations->links() }}
        </x-slot>
    </x-admin.table>
@endsection
