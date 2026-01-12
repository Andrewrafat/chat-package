<?php

namespace Andrew\ChatPackage\Http\Controllers\Ui;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Andrew\ChatPackage\Models\Conversation;
use App\Models\User;

class ChatUiController extends Controller
{
    /**
     * Show WhatsApp-like chat layout
     */
    public function index(Request $request)
    {
        $userId = 1;

        $conversations = Conversation::whereHas(
            'participants',
            fn($q) => $q->where('user_id', $userId)
        )
            ->with([
                'participants',
                'messages' => fn($q) => $q->latest()->limit(1),
            ])
            ->latest('updated_at')
            ->get();
                $users=User::all();
        return view('chat::chat.index',  [
            'conversations' => $conversations,
            'activeChat'    => null,
            'users'=>$users,
        ]);
    }

    /**
     * Show specific chat
     */
    public function show(Request $request, string $chatKey)
    {
        // $userId = $request->user()->id;
        $userId = 1;

        $conversations = Conversation::whereHas(
            'participants',
            fn($q) => $q->where('user_id', $userId)
        )
            ->with([
                'participants',
                'messages' => fn($q) => $q->latest()->limit(1),

            ])
            ->latest('updated_at')
            ->get();

        $activeChat = Conversation::where('chat_key', $chatKey)
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->with([
                'participants',
                'messages.sender',
                'messages.reads',
            ])
            ->firstOrFail();
            

        return view('chat::chat.conversation', [
            'conversations' => $conversations,
            'activeChat'    => $activeChat,
        ]);
    }
}
