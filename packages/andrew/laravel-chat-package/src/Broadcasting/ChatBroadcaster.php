<?php

namespace Andrew\ChatPackage\Broadcasting;

use Illuminate\Support\Facades\Broadcast;

class ChatBroadcaster
{
    public static function enabled(): bool
    {
        return config('chat.broadcast.enabled') === true;
    }
}
