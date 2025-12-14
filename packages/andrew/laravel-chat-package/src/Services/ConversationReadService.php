<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\MessageRead;
use Andrew\ChatPackage\Models\Participant;
use Andrew\ChatPackage\Events\ConversationUpdated;

class ConversationReadService
{
    public function markAsRead(string $chatKey, int $userId): array
    {
        $conversation = Conversation::where('chat_key', $chatKey)
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->firstOrFail();

        // Get unread messages (sent by others)
        $unreadMessages = $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $userId))
            ->pluck('id');

        foreach ($unreadMessages as $messageId) {
            MessageRead::firstOrCreate([
                'message_id' => $messageId,
                'user_id'    => $userId,
            ]);
        }

        // 🔔 Fire ConversationUpdated (for sidebar sync)
        event(new ConversationUpdated(
            receiverId: $userId,
            conversation: [
                'chat_key'     => $conversation->chat_key,
                'unread_count' => 0,
                'updated_at'   => now()->toISOString(),
            ]
        ));

        return [
            'chat_key'     => $conversation->chat_key,
            'unread_count' => 0,
        ];
    }
}
