<?php
namespace Andrew\ChatPackage\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationInvited implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $chatKey,
        public array $invitedUser,
        public array $invitedBy
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->chatKey}");
    }

    public function broadcastAs(): string
    {
        return 'conversation.invited';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_key'     => $this->chatKey,
            'invited_user' => $this->invitedUser,
            'invited_by'   => $this->invitedBy,
        ];
    }
}
