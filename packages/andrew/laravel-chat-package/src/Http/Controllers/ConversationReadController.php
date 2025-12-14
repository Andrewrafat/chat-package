<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Andrew\ChatPackage\Services\ConversationReadService;

class ConversationReadController extends Controller
{
    public function __construct(
        protected ConversationReadService $service
    ) {}

    public function store(Request $request, string $chat_key)
    {
        $result = $this->service->markAsRead(
            chatKey: $chat_key,
            userId: $request->user()->getAuthIdentifier()
        );

        return response()->json([
            'data'    => $result,
            'message' => 'Conversation marked as read.',
        ]);
    }
}
