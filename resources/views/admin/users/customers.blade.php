@extends('layouts.admin')

@section('title', 'Customer Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 ">Customers</h1>
            <p class="text-sm text-gray-500 mt-1">Manage registered users and their roles</p>
        </div>

        <!-- Search -->
        <form method="GET" action="{{ route('admin.users.customers') }}" class="w-full sm:w-auto">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Search users...">
            </div>
        </form>
    </div>

    <x-admin.table>
        <x-slot name="head">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Linked Profile</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </x-slot>

        <x-slot name="body">
            @forelse($customers as $customer)
                <tr class="row-hover">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold mr-3 uppercase">
                                {{ substr($customer->name, 0, 2) }}
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $customer->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($customer->customer)
                            <span class="inline-flex items-center text-green-600">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @else
                            <span class="text-gray-400">No Profile</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @can('add-admin')
                            <form action="{{ route('admin.users.promote', $customer) }}" method="POST" onsubmit="return confirm('Promote this user to Admin? They will have full access to the dashboard.');" class="inline-block">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:text-emerald-900 font-medium">
                                    Promote to Admin
                                </button>
                            </form>
                        @else
                            <span class="text-gray-400 cursor-not-allowed">Restricted</span>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No customers found matching your criteria.</td>
                </tr>
            @endforelse
        </x-slot>

        <x-slot name="footer">
            {{ $customers->links() }}
        </x-slot>
    </x-admin.table>
</div>
@endsection
