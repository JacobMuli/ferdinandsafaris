@extends('layouts.admin')

@section('title', 'Guide Profile')

@section('subtitle', 'View details for ' . $guide->first_name)

@section('actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.guides.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
        <a href="{{ route('admin.guides.edit', $guide) }}" class="btn-primary bg-indigo-600 hover:bg-indigo-700">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <div class="mb-4">
                @if($guide->profile_photo)
                    <img src="{{ $guide->profile_photo_url }}" alt="{{ $guide->first_name }}" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-emerald-50">
                @else
                    <div class="w-32 h-32 rounded-full bg-gray-100 flex items-center justify-center mx-auto text-gray-400 text-4xl">
                        <i class="fas fa-user-tie"></i>
                    </div>
                @endif
            </div>

            <h2 class="text-xl font-bold text-gray-900">{{ $guide->first_name }} {{ $guide->last_name }}</h2>
            <p class="text-sm text-gray-500 mb-4">Professional Tour Guide</p>

            <div class="flex justify-center gap-2 mb-6">
                <!-- Status Badge (assuming active column exists or just static for now) -->
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                    Active
                </span>
            </div>

            <div class="border-t border-gray-100 pt-4 text-left space-y-3">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-envelope w-5 text-gray-400"></i>
                    {{ $guide->email }}
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-phone w-5 text-gray-400"></i>
                    {{ $guide->phone ?? 'N/A' }}
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-id-card w-5 text-gray-400"></i>
                    License: <span class="font-medium ml-1">{{ $guide->license_number }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Details / Assignments -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="text-xs font-medium text-gray-500 uppercase">Experience</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">
                    {{ $guide->created_at->diffInYears(now()) }} <span class="text-sm font-normal text-gray-500">Years</span>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="text-xs font-medium text-gray-500 uppercase">Tours Assigned</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">0</div> <!-- Placeholder until relation is added -->
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="text-xs font-medium text-gray-500 uppercase">Rating</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">5.0</div> <!-- Placeholder -->
            </div>
        </div>

        <!-- License Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">License Information</h3>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-500">License Number</p>
                    <p class="font-medium text-gray-900">{{ $guide->license_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Expires On</p>
                    <p class="font-medium {{ $guide->license_expiry_date->isPast() ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ $guide->license_expiry_date->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
