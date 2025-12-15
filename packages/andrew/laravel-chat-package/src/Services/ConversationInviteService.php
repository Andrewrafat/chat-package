<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Participant;
use Andrew\ChatPackage\Events\ConversationInvited;
use Andrew\ChatPackage\Support\Broadcast;

class ConversationInviteService
{
    public function invite(
        string $chatKey,
        int $inviterId,
        int $invitedUserId
    ): array {
        $conversation = Conversation::where('chat_key', $chatKey)->firstOrFail();

        // ✅ Check admin
        $isAdmin = Participant::where('conversation_id', $conversation->id)
            ->where('user_id', $inviterId)
            ->where('role', 'admin')
            ->exists();

        abort_if(!$isAdmin, 403, 'Only admins can invite users.');

        // ❌ Prevent duplicate
        $exists = Participant::where('conversation_id', $conversation->id)
            ->where('user_id', $invitedUserId)
            ->exists();

        abort_if($exists, 409, 'User already in conversation.');

        Participant::create([
            'conversation_id' => $conversation->id,
            'user_id'         => $invitedUserId,
            'role'            => 'member',
            'joined_at'       => now(),
        ]);

        $invitedUserPayload = [
            'id' => $invitedUserId,
        ];

        $invitedByPayload = [
            'id' => $inviterId,
        ];

        // ✅ Optional realtime event
        Broadcast::dispatch(
            new ConversationInvited(
                chatKey: $conversation->chat_key,
                invitedUser: $invitedUserPayload,
                invitedBy: $invitedByPayload
            )
        );

        return [
            'chat_key'     => $conversation->chat_key,
            'invited_user' => $invitedUserPayload,
            'invited_by'   => $invitedByPayload,
        ];
    }
}
