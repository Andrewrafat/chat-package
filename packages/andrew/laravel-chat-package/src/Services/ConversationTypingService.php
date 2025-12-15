<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Events\UserTyping;
use Andrew\ChatPackage\Support\Broadcast;

class ConversationTypingService
{
    public function typing(string $chatKey, int $userId): void
    {
        $conversation = Conversation::where('chat_key', $chatKey)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId))
            ->firstOrFail();

        // ✅ Optional realtime (driver-agnostic)
        Broadcast::dispatch(
            new UserTyping(
                chatKey: $conversation->chat_key,
                user: [
                    'id' => $userId,
                ]
            )
        );
    }
}
