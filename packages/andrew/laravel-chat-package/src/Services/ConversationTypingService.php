<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Events\UserTyping;

class ConversationTypingService
{
    public function typing(string $chatKey, int $userId): void
    {
        $conversation = Conversation::where('chat_key', $chatKey)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId))
            ->firstOrFail();

        event(new UserTyping(
            chatKey: $conversation->chat_key,
            user: [
                'id' => $userId,
            ]
        ));
    }
}
