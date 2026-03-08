@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <h2 class="text-lg font-bold text-gray-800">Support Conversations</h2>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($conversations as $convo)
            <a href="{{ route('admin.messages.show', $convo['user']) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                            {{ substr($convo['user']->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-900">{{ $convo['user']->name }}</span>
                                @if($convo['unread_count'] > 0)
                                    <span class="ml-2 px-2 py-0.5 text-xs font-bold bg-red-100 text-red-600 rounded-full">
                                        {{ $convo['unread_count'] }} new
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 truncate max-w-md">
                                @if($convo['last_message']->is_admin_message)
                                    <span class="text-gray-400">You:</span>
                                @endif
                                {{ $convo['last_message']->message }}
                            </p>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $convo['last_message']->created_at->diffForHumans() }}
                    </div>
                </div>
            </a>
        @empty
            <div class="px-6 py-8 text-center text-gray-500">
                No conversations yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
