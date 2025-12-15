<?php

namespace Andrew\ChatPackage\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Participant;
use Andrew\ChatPackage\Events\ConversationCreated;
use Andrew\ChatPackage\Support\Broadcast;

class ConversationCreateService
{
    public function create(
        int $creatorId,
        array $participants,
        ?string $type = 'private',
        ?string $title = null
    ): array {
        return DB::transaction(function () use ($creatorId, $participants, $type, $title) {

            $conversation = Conversation::create([
                'chat_key'   => 'c_' . Str::uuid(),
                'creator_id' => $creatorId,
                'type'       => $type,
                'title'      => $title,
            ]);

            $allParticipants = collect([$creatorId])
                ->merge($participants)
                ->unique()
                ->values();

            foreach ($allParticipants as $userId) {
                Participant::firstOrCreate(
                    [
                        'conversation_id' => $conversation->id,
                        'user_id'         => $userId,
                    ],
                    [
                        'role'      => $userId === $creatorId ? 'admin' : 'member',
                        'joined_at' => now(),
                    ]
                );
            }

            $conversationDto = [
                'chat_key'           => $conversation->chat_key,
                'type'               => $conversation->type,
                'is_group'           => $conversation->type === 'group',
                'title'              => $conversation->title,
                'participants_count' => $allParticipants->count(),
                'created_at'         => $conversation->created_at->toISOString(),
            ];

            // ✅ Optional realtime notification
            foreach ($allParticipants as $userId) {
                Broadcast::dispatch(
                    new ConversationCreated(
                        receiverId: $userId,
                        conversation: $conversationDto
                    )
                );
            }

            return $conversationDto;
        });
    }
}
