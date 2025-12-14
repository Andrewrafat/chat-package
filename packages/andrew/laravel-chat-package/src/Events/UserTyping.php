<?php

namespace Andrew\ChatPackage\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $chatKey,
        public array $user
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->chatKey}");
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_key' => $this->chatKey,
            'user'     => $this->user,
        ];
    }
}
