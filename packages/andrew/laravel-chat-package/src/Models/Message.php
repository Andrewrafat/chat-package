<?php

namespace Andrew\ChatPackage\Models;

use Andrew\ChatPackage\Events\MessageSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $table;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = config('chat.tables.messages', 'chat_messages');
        parent::__construct($attributes);
    }

    public function conversation()
    {
        return $this->belongsTo(
            Conversation::class,
            'conversation_id', // FK on chat_messages
            'id'               // PK on chat_conversations
        );
    }

    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class, 'message_id');
    }
    public function stars()
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            'chat_message_stars',
            'message_id',
            'user_id'
        )->withTimestamps();
    }
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }

    public function isStarredBy(int $userId): bool
    {
        return $this->stars()->where('user_id', $userId)->exists();
    }
    public function starredBy()
    {
        return $this->stars();
    }
}
