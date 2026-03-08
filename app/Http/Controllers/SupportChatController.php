<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function fetch()
    {
        $messages = Message::where('user_id', Auth::id())
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
            'is_read' => false,
            'is_admin_message' => false,
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json($message);
    }
}
