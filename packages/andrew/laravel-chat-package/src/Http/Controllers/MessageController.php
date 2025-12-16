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
        // ✅ Validation (text OR attachments)
        $data = $request->validate([
            'chat_key'        => 'required|string',
            'content'         => 'nullable|string',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|max:10240', // 10MB per file
        ]);

        // 📎 Get uploaded files safely
        $attachments = $request->file('attachments', []);
         // 📨 Send message via service
        $message = $this->messageService->send(
            chatKey: $data['chat_key'],
            userId: (int) $request->user()->id,
            content: $data['content'] ?? null,
            attachments: $attachments
        );

        return response()->json([
            'data' => $message,
        ], 201);
    }
}
