<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Message;

class ConversationListService
{
    public function listForUser(int $userId, int $limit = 20)
    {
        
        return Conversation::query()
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId))

            // ✅ eager loading
            ->with([
                'participants',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])

            // ✅ SAFE ordering using Message model
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn(
                        'chat_messages.conversation_id',
                        'chat_conversations.id'
                    )
                    ->latest()
                    ->limit(1)
            )

            ->paginate($limit);
    }
}
