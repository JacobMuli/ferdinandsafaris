@extends('layouts.admin')

@section('title', 'Security Settings')

@section('subtitle', 'System access control and logs')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Admin Accounts -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Administrator Accounts</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">Manage access levels for dashboard administrators.</p>
            <a href="{{ route('admin.users.admins') }}" class="btn-secondary w-full justify-center">
                Manage Admins
            </a>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Security Logs</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start gap-3 text-sm">
                    <div class="mt-0.5 w-2 h-2 rounded-full bg-green-500"></div>
                    <div>
                        <p class="font-medium text-gray-900">System Backup Completed</p>
                        <p class="text-gray-500 text-xs">Today at 2:00 AM</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-sm">
                    <div class="mt-0.5 w-2 h-2 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="font-medium text-gray-900">Login Attempt (Admin)</p>
                        <p class="text-gray-500 text-xs">Today at 9:15 AM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Policy -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 lg:col-span-2">
        <div class="px-6 py-4 border-b border-gray-100">
             <h3 class="text-lg font-semibold text-gray-900">Password Policy</h3>
        </div>
        <div class="p-6">
            <form action="#" class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="font-medium text-gray-900">Require Two-Factor Authentication</label>
                        <p class="text-sm text-gray-500">Force all admin accounts to use 2FA.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
