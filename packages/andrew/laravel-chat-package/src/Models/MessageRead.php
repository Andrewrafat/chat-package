<?php

namespace Andrew\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRead extends Model
{
    protected $table = 'chat_message_reads';

    protected $guarded = [];


    public $timestamps = true;
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
