<?php

namespace Andrew\ChatPackage\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageStarToggled implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(
        public string $chatKey,
        public array $payload
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("chat.{$this->chatKey}");
    }

    public function broadcastAs(): string
    {
        return 'message.star.toggled';
    }
}
