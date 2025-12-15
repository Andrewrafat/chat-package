<?php

namespace Andrew\ChatPackage\Support;

class Broadcast
{
    public static function dispatch(object $event): void
    {
        if (! config('chat.broadcasting.enabled')) {
            return;
        }

        event($event);
    }
}
