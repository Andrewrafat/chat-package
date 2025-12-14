<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Andrew\ChatPackage\Services\ConversationTypingService;

class ConversationTypingController extends Controller
{
    public function __construct(
        protected ConversationTypingService $service
    ) {}

    public function store(Request $request, string $chat_key)
    {
        $this->service->typing(
            chatKey: $chat_key,
            userId: $request->user()->getAuthIdentifier()
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
