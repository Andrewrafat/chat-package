<?php

return [

    /*
    |----------------------------------------------------------------------
    | User Model
    |----------------------------------------------------------------------
    */
    'user_model' => App\Models\User::class,

    /*
    |----------------------------------------------------------------------
    | Auth Guard
    |----------------------------------------------------------------------
    */
    'auth_guard' => 'api',

    /*
    |----------------------------------------------------------------------
    | Database Tables
    |----------------------------------------------------------------------
    */
    'tables' => [
        'conversations' => 'chat_conversations',
        'messages'      => 'chat_messages',
        'participants'  => 'chat_participants',
        'message_reads' => 'chat_message_reads',
        'message_stars' => 'chat_message_stars',
        'message_attachments' =>'chat_message_attachments'
    ],

    /*
    |----------------------------------------------------------------------
    | Realtime Broadcasting
    |----------------------------------------------------------------------
    |
    | This package is transport-agnostic.
    | It uses Laravel events & broadcasting.
    |
    | Supported drivers:
    | - pusher
    | - redis
    | - laravel-websockets
    | - log / null
    |
    */
    'broadcasting' => [
        'enabled' => true,
    ],
];
