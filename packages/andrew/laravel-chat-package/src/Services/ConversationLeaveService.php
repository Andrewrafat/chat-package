<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Participant;
use Andrew\ChatPackage\Events\ConversationLeft;

class ConversationLeaveService
{
    public function leave(string $chatKey, int $userId): array
    {
        $conversation = Conversation::where('chat_key', $chatKey)->firstOrFail();

        $participant = Participant::where('conversation_id', $conversation->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // ❌ Admin cannot leave if only admin
        if ($participant->role === 'admin') {
            $adminsCount = Participant::where('conversation_id', $conversation->id)
                ->where('role', 'admin')
                ->count();

            abort_if($adminsCount <= 1, 403, 'Admin cannot leave without assigning another admin.');
        }

        $participant->delete();

        $userPayload = [
            'id'   => $userId,
            'name' => optional($participant->user)->name,
        ];

        // 🔥 EVENT IS THE GOAL
        event(new ConversationLeft(
            chatKey: $conversation->chat_key,
            user: $userPayload
        ));

        return [
            'chat_key' => $conversation->chat_key,
            'user'     => $userPayload,
        ];
    }
}
