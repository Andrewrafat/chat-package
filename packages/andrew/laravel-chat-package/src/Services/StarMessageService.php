<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class StarMessageService
{
    public function star(int $messageId, int $userId): Message
    {
        $message = Message::query()->findOrFail($messageId);

        // idempotent
        $message->starredBy()->syncWithoutDetaching([$userId]);

        return $message;
    }

    public function unstar(int $messageId, int $userId): Message
    {
        $message = Message::query()->findOrFail($messageId);

        $message->starredBy()->detach($userId);

        return $message;
    }

    public function listStarredForUser(int $userId, int $limit = 50, ?string $chatKey = null): Collection
    {
        $query = Message::query()
            ->whereHas('starredBy', fn ($q) => $q->where('user_id', $userId))
            ->with(['conversation'])
            ->latest()
            ->limit($limit);

        if ($chatKey) {
            $query->whereHas('conversation', fn ($q) => $q->where('chat_key', $chatKey));
        }

        return $query->get();
    }
}
