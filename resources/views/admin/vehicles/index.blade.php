@extends('layouts.admin')

@section('title', 'Fleet Management')

@section('subtitle', 'Manage Fleet')

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.vehicles.export') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-file-export mr-2"></i> Export
        </a>
        <button x-data @click="$dispatch('open-import-modal')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-file-import mr-2"></i> Import
        </button>
        <a href="{{ route('admin.vehicles.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
            <i class="fas fa-plus mr-2"></i> Add Vehicle
        </a>
    </div>

    <!-- Import Modal -->
    <x-admin.partials.import-modal action="{{ route('admin.vehicles.import') }}" title="Import Vehicles" />
@endsection

@section('content')
    <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 ">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 ">
                    <thead class="bg-gray-50 ">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Registration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Type / Model</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Year</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Mileage</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500  uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white  divide-y divide-gray-200 ">
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
                                    {{ $vehicle->registration_number }}
                                    @if(!$vehicle->is_active)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">
                                    <div class="font-medium text-gray-900 ">{{ $vehicle->type->name ?? 'Unknown Type' }}</div>
                                    <div class="text-xs text-gray-400">{{ $vehicle->type->manufacturer ?? '' }} {{ $vehicle->type->model ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">{{ $vehicle->year }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'available' => 'bg-green-100 text-green-800',
                                            'in_use' => 'bg-blue-100 text-blue-800',
                                            'maintenance' => 'bg-yellow-100 text-yellow-800',
                                            'retired' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $colorClass = $statusColors[$vehicle->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ ucwords(str_replace('_', ' ', $vehicle->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">{{ number_format($vehicle->mileage) }} km</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-indigo-600  hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600  hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 ">No vehicles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
@endsection
