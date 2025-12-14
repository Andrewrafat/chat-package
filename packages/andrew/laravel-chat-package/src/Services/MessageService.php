<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Message;
use Andrew\ChatPackage\Events\MessageSent;

class MessageService
{
    public function send(string $chatKey, int $userId, string $content): array
    {
        $conversation = Conversation::where('chat_key', $chatKey)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId))
            ->firstOrFail();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'         => $userId,
            'content'         => $content,
        ]);

        $payload = [
            'id'         => $message->id,
            'type'       => 'text',
            'content'    => $message->content,
            'sender'     => [
                'id' => $userId,
            ],
            'created_at' => $message->created_at->toISOString(),
        ];

        // 🔥 EVENT IS THE PRODUCT
        event(new MessageSent(
            chatKey: $conversation->chat_key,
            message: $payload
        ));

        return $payload;
    }
}
