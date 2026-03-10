@extends('layouts.admin')

@section('title', 'Content Management')
@section('subtitle', 'Build and organize your website pages')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Pages</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pages->total() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Active</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pages->where('is_active', true)->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center text-xl">
                <i class="fas fa-pencil-alt"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Drafts</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pages->where('is_active', false)->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <div class="relative w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" placeholder="Filter pages..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
            </div>
            <a href="{{ route('admin.cms-pages.create') }}" class="inline-flex items-center bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-emerald-700 transition transform hover:-translate-y-0.5 active:scale-95 shadow-md shadow-emerald-500/20">
                <i class="fas fa-plus mr-2"></i> Create Page
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Page Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">URL Slug</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Sections</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pages as $page)
                    <tr class="group hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                                    {{ substr($page->title, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $page->title }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase font-semibold">Updated {{ $page->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <code class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ $page->slug }}</code>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-1">
                               @foreach($page->sections->take(3) as $section)
                                   <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded border border-blue-100">{{ $section->section_key }}</span>
                               @endforeach
                               @if($page->sections->count() > 3)
                                   <span class="text-[10px] text-gray-400">+{{ $page->sections->count() - 3 }} more</span>
                               @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($page->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.cms-pages.edit', $page) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Edit Content">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/page/{{ $page->slug }}" target="_blank" class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-lg transition" title="View Public Page">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <form action="{{ route('admin.cms-pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Archive this page?');">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="max-w-xs mx-auto">
                                <div class="h-16 w-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                                    <i class="fas fa-ghost"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">No Pages Found</h3>
                                <p class="text-gray-500 text-sm mb-6">Start building your site by creating your first content page.</p>
                                <a href="{{ route('admin.cms-pages.create') }}" class="btn-primary">Create First Page</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
        <div class="p-6 border-t border-gray-50">
            {{ $pages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
