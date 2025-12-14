<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Participant;

class ConversationQueryService
{
    public function listForUser(int $userId, int $limit = 20): array
    {
        $conversationIds = Participant::where('user_id', $userId)
            ->pluck('conversation_id');

        $conversations = Conversation::whereIn('id', $conversationIds)
            ->with(['lastMessage'])
            ->orderByDesc(
                Conversation::select('created_at')
                    ->from('messages')
                    ->whereColumn('messages.conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->limit($limit)
            ->get();

        return $conversations->map(function ($conversation) use ($userId) {
            return [
                'chat_key' => $conversation->chat_key,
                'type'     => $conversation->type ?? 'private',
                'title'    => $conversation->title ?? 'Conversation',
                'last_message' => $conversation->lastMessage ? [
                    'id'         => $conversation->lastMessage->id,
                    'type'       => 'text',
                    'content'    => $conversation->lastMessage->content,
                    'sender_id'  => $conversation->lastMessage->user_id,
                    'created_at' => $conversation->lastMessage->created_at->toISOString(),
                ] : null,
                'unread_count' => $conversation->unreadCountFor($userId),
                'updated_at'   => optional($conversation->lastMessage?->created_at)->toISOString(),
            ];
        })->values()->all();
    }
}
