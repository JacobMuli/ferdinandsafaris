@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Subscribed At</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subscribers as $subscriber)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $subscriber->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscriber->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $subscriber->is_active ? 'Active' : 'Unsubscribed' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $subscriber->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <form action="{{ route('admin.newsletter.toggle', $subscriber) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-semibold {{ $subscriber->is_active ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $subscriber->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.newsletter.destroy', $subscriber) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-gray-400 hover:text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">No subscribers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $subscribers->links() }}
    </div>
</div>
@endsection
