<?php

namespace Andrew\ChatPackage\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $receiverId,
        public array $conversation
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.user.{$this->receiverId}");
    }

    public function broadcastAs(): string
    {
        return 'conversation.created';
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->conversation,
        ];
    }
}

