<?php

namespace Andrew\ChatPackage\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $guarded = [];

    protected $table;

    public function __construct(array $attributes = [])
    {
        $this->table = config('chat.tables.participants', 'chat_participants');
        parent::__construct($attributes);
    }
}
