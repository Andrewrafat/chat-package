<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    'user_model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Auth Guard
    |--------------------------------------------------------------------------
    */
    'auth_guard' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    */
    'tables' => [
        'conversations' => 'chat_conversations',
        'messages'      => 'chat_messages',
        'participants'  => 'chat_participants',

        'message_reads' => 'chat_message_reads',

        'message_stars' => 'chat_message_stars',


    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    */
    'broadcast' => [

        'enabled' => true,

        'driver' => 'pusher',

        'connections' => [
            'pusher' => [
                'app_id'  => env('PUSHER_APP_ID'),
                'key'     => env('PUSHER_APP_KEY'),
                'secret'  => env('PUSHER_APP_SECRET'),
                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
            ],
        ],
    ],

];
