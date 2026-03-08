@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 ">Administrators</h1>
            <p class="text-sm text-gray-500 mt-1">Users with elevated privileges</p>
        </div>

        <!-- Info Alert -->
        <div class="hidden sm:block">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                <i class="fas fa-shield-alt mr-2"></i> Only Super Admin can manage roles
            </span>
        </div>
    </div>

    <x-admin.table>
        <x-slot name="head">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </x-slot>

        <x-slot name="body">
            @forelse($admins as $admin)
                <tr class="row-hover">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold mr-3 uppercase">
                                {{ substr($admin->name, 0, 2) }}
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $admin->email }}
                        @if($admin->email === 'jacobmwalughs@gmail.com')
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                Super Admin
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $admin->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @can('add-admin')
                            @if($admin->id !== auth()->id())
                                <form action="{{ route('admin.users.demote', $admin) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove admin privileges from this user?');" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                        Demote
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 italic">Current User</span>
                            @endif
                        @else
                            <span class="text-gray-400 cursor-not-allowed" title="Permission Denied">No Access</span>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No administrators found.</td>
                </tr>
            @endforelse
        </x-slot>

        <x-slot name="footer">
            {{ $admins->links() }}
        </x-slot>
    </x-admin.table>
</div>
@endsection
