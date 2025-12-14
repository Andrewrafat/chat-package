<?php

namespace Andrew\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = [];

    protected $table;

    public function __construct(array $attributes = [])
    {
        $this->table = config('chat.tables.conversations', 'chat_conversations');
        parent::__construct($attributes);
    }

    /**
     * Participants of the conversation
     */
    public function participants()
    {
        return $this->hasMany(Participant::class, 'conversation_id');
    }

    /**
     * Messages of the conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
    public function unreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->count();
    }
}
