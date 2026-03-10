@extends('layouts.admin')

@section('title', 'Page Builder')
@section('subtitle', 'Editing: ' . $cmsPage->title)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.cms-pages.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $cmsPage->title }}</h2>
                <div class="flex items-center gap-2">
                    <code class="text-[10px] text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded">/{{ $cmsPage->slug }}</code>
                    <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                    <span class="text-xs font-medium {{ $cmsPage->is_active ? 'text-emerald-600' : 'text-orange-600' }}">
                        {{ $cmsPage->is_active ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
             <a href="/page/{{ $cmsPage->slug }}" target="_blank" class="px-5 py-2 rounded-xl text-emerald-600 font-bold hover:bg-emerald-50 transition flex items-center gap-2">
                <i class="fas fa-eye"></i> Preview
             </a>
             <button form="pageSettingsForm" type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition transform active:scale-95 flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                <i class="fas fa-save"></i> Save Changes
             </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar: Page Settings -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-cog text-emerald-500"></i> Page Configurations
                </h3>
                <form id="pageSettingsForm" action="{{ route('admin.cms-pages.update', $cmsPage) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Display Title</label>
                        <input type="text" name="title" value="{{ old('title', $cmsPage->title) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">URL Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $cmsPage->slug) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all font-mono text-sm shadow-inner bg-gray-50">
                    </div>
                    <div class="pt-4 flex items-center justify-between bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                        <div>
                            <p class="text-sm font-bold text-emerald-900">Direct visibility</p>
                            <p class="text-[10px] text-emerald-600">Set page to live or draft mode</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $cmsPage->is_active ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </form>
            </div>

            <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                <h3 class="font-bold text-red-900 mb-2">Management</h3>
                <p class="text-xs text-red-600 mb-6">Archive this page from the system. Redirects will need to be manual.</p>
                <form action="{{ route('admin.cms-pages.destroy', $cmsPage) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-3 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-600 hover:text-white transition shadow-sm">
                        Archive Page
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Workspace: Content Blocks -->
        <div class="lg:col-span-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-500"></i> Content Blocks
                    </h3>
                    <button onclick="alert('Section definition and schema tools expanding...')" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-200 transition">
                        <i class="fas fa-plus mr-1"></i> Add New Block
                    </button>
                </div>

                @if($cmsPage->sections->count() > 0)
                    <div class="space-y-6">
                        @foreach($cmsPage->sections as $section)
                            <div class="group border border-gray-100 rounded-2xl bg-gray-50/30 overflow-hidden hover:border-blue-200 hover:bg-white transition-all duration-300 shadow-sm hover:shadow-md">
                                <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-white">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-sm">
                                            <i class="fas fa-square"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ $section->title ?? $section->section_key }}</h4>
                                            <code class="text-[9px] text-gray-400 font-mono">{{ strtoupper($section->section_key) }} BLOCK</code>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button class="text-gray-400 hover:text-blue-500 transition p-1.5"><i class="fas fa-chevron-up"></i></button>
                                        <button class="text-gray-400 hover:text-blue-500 transition p-1.5"><i class="fas fa-chevron-down"></i></button>
                                        <button class="ml-2 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition">Edit Content</button>
                                    </div>
                                </div>
                                <div class="p-4 bg-gray-50/50">
                                    <div class="bg-white rounded-xl border border-gray-100 p-4 font-mono text-[11px] leading-relaxed text-blue-900/70 overflow-x-auto shadow-inner">
                                        <pre>{{ json_encode($section->content, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100">
                         <div class="h-16 w-16 bg-white rounded-full flex items-center justify-center text-gray-200 text-3xl mx-auto mb-4">
                            <i class="fas fa-cubes"></i>
                         </div>
                         <h4 class="font-bold text-gray-400 mb-2">Empty Page Canvas</h4>
                         <p class="text-xs text-gray-400 mb-8 max-w-xs mx-auto text-balance">Add your first content block to start building this page's layout and messaging.</p>
                         <button class="btn-primary">Initialize Hero Block</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
