@extends('layouts.admin')

@section('title', 'Content Management')
@section('subtitle', 'Manage website pages and content')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">All Pages</h2>
        <a href="{{ route('admin.cms-pages.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i> New Page
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-900 font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Title</th>
                    <th class="px-6 py-4">Slug</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pages as $page)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $page->title }}</td>
                    <td class="px-6 py-4 font-mono text-xs">{{ $page->slug }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $page->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $page->is_active ? 'Active' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.cms-pages.edit', $page) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form action="{{ route('admin.cms-pages.destroy', $page) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                        No pages found. Create one to get started.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pages->hasPages())
        <div class="p-6 border-t border-gray-100">
            {{ $pages->links() }}
        </div>
    @endif
</div>
@endsection
