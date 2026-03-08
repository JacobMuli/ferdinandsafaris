@extends('layouts.admin')

@section('title', 'Vehicle Types')

@section('subtitle', 'Manage Vehicle Types')

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.vehicle-types.export') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-file-export mr-2"></i> Export CSV
        </a>
        <button x-data @click="$dispatch('open-import-modal')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-file-import mr-2"></i> Import CSV
        </button>
        <a href="{{ route('admin.vehicle-types.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
            <i class="fas fa-plus mr-2"></i> Add Vehicle Type
        </a>
    </div>

    <!-- Import Modal -->
    <x-admin.partials.import-modal action="{{ route('admin.vehicle-types.import') }}" title="Import Vehicle Types" />
@endsection

@section('content')
    <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 ">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 ">
                    <thead class="bg-gray-50 ">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Make/Model</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Rate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500  uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white  divide-y divide-gray-200 ">
                        @forelse($vehicleTypes as $type)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">{{ $type->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">{{ $type->manufacturer }} {{ $type->model }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500  capitalize">{{ str_replace('_', ' ', $type->category) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">{{ $type->default_capacity }} pax</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ">${{ number_format($type->base_daily_rate, 2) }}/day</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.vehicle-types.edit', $type) }}" class="text-indigo-600  hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.vehicle-types.destroy', $type) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600  hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 ">No vehicle types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $vehicleTypes->links() }}
            </div>
        </div>
    </div>
@endsection
