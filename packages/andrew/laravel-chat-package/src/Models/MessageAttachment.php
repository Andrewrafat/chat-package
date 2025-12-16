<?php

namespace Andrew\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $table;

    protected $guarded = [];


    public function __construct(array $attributes = [])
    {
        $this->table = config('chat.tables.message_attachments', 'chat_message_attachments');
        parent::__construct($attributes);
    }
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}
