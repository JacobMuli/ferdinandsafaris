<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        // Get all unique users who have sent messages
        $conversations = Message::select('user_id')
            ->distinct()
            ->with(['user'])
            ->get()
            ->map(function ($item) {
                $lastMessage = Message::where('user_id', $item->user_id)
                    ->latest()
                    ->first();

                $unreadCount = Message::where('user_id', $item->user_id)
                    ->where('is_admin_message', false)
                    ->where('is_read', false)
                    ->count();

                return [
                    'user' => $item->user,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc(function ($item) {
                return $item['last_message']->created_at;
            });

        return view('admin.messages.index', compact('conversations'));
    }

    public function show(User $user)
    {
        // Mark all messages from this user as read
        Message::where('user_id', $user->id)
            ->where('is_admin_message', false)
            ->update(['is_read' => true]);

        $messages = Message::where('user_id', $user->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.messages.show', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'user_id' => $user->id,
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
            'is_read' => false,
            'is_admin_message' => true,
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        if ($request->wantsJson()) {
            return response()->json($message);
        }

        return back()->with('success', 'Reply sent successfully.');
    }
}
