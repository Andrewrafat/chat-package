<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Andrew\ChatPackage\Services\MessageService;

class MessageController extends Controller
{
    public function __construct(
        protected MessageService $messageService
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'chat_key' => 'required|string',
            'content'  => 'required|string',
        ]);

        $message = $this->messageService->send(
            chatKey: $data['chat_key'],
            userId: auth()->id(),
            content: $data['content']
        );

        return response()->json([
            'data' => $message
        ]);
    }
}
