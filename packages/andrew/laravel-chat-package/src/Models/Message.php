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
        $this->table = config('chat.tables.messages');
        parent::__construct($attributes);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

     public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class, 'message_id');
    }
}
