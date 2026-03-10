@extends('layouts.admin')

@section('title', 'Community Stories Moderation')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">User / Story</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Featured</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stories as $story)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $story->title }}</div>
                            <div class="text-xs text-gray-500">By {{ $story->name }} ({{ $story->user->email ?? $story->email }})</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $story->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $story->is_approved ? 'Approved' : 'Pending Review' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.community-stories.toggle-featured', $story) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit">
                                    <i class="fas {{ $story->is_featured ? 'fa-star text-yellow-400' : 'fa-star text-gray-200' }}"></i>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            @if(!$story->is_approved)
                                <form action="{{ route('admin.community-stories.approve', $story) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline">Approve</button>
                                </form>
                            @else
                                <form action="{{ route('admin.community-stories.reject', $story) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs font-bold text-orange-600 hover:underline">Unapprove</button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.community-stories.destroy', $story) }}" method="POST" class="inline" onsubmit="return confirm('Delete this story?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-gray-400 hover:text-red-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">No community stories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $stories->links() }}
    </div>
</div>
@endsection
