Laravel Chat Package

A developer-first, headless real-time chat engine for Laravel.
Built for APIs, mobile apps, SaaS platforms, and modern frontends.

No UI. No opinions. No lock-in.
Just a clean, extensible chat core.

✨ Why This Package?

Most chat packages:

force UI

assume frontend stack

lock you to Pusher

are hard to customize

This package is different.

✔ API-first
✔ Transport-agnostic (Pusher, WebSocket, Redis, or none)
✔ Mobile-ready
✔ SaaS-friendly
✔ Zero assumptions

🚀 Features
Core

✅ One-to-One Conversations

✅ Group Conversations

✅ Role-based Participants (admin / member)

✅ Invite users to conversations

✅ Leave conversations safely

✅ Secure authorization (participants only)

Messaging

✅ Send messages

✅ Read receipts

✅ Star / unstar messages

✅ List starred messages

✅ Last message relation (for inbox)

Realtime (Optional)

✅ MessageSent event

✅ Typing indicator

✅ Conversation updates

✅ Pluggable realtime driver

📦 Installation
1️⃣ Install via Composer
composer require andrew/laravel-chat-package

2️⃣ Publish config
php artisan vendor:publish --tag=chat-config


Creates:

config/chat.php

3️⃣ Run migrations
php artisan migrate


Tables created:

chat_conversations

chat_messages

chat_participants

chat_message_reads

chat_message_stars

🔐 Authentication

This package is auth-guard aware.

By default it uses Sanctum:

Authorization: Bearer YOUR_TOKEN
Accept: application/json


You can change the guard in config/chat.php.

⚙️ Configuration
return [

    'user_model' => App\Models\User::class,

    'auth_guard' => 'api',

    'tables' => [
        'conversations' => 'chat_conversations',
        'messages'      => 'chat_messages',
        'participants'  => 'chat_participants',
        'message_reads' => 'chat_message_reads',
        'message_stars' => 'chat_message_stars',
    ],

    /*
    |--------------------------------------------------------------------------
    | Realtime Broadcasting
    |--------------------------------------------------------------------------
    |
    | This package is transport-agnostic.
    | Uses Laravel events & broadcasting.
    |
    */
    'broadcasting' => [
        'enabled' => true,
    ],
];

🔌 API Usage
Create Conversation

One-to-One

POST /chat/conversations

{
  "participants": [2]
}


Group

{
  "type": "group",
  "title": "Backend Team",
  "participants": [2,3,4]
}

List Conversations (Inbox)
GET /chat/conversations


Returns:

unread count

last message

participants count

Send Message
POST /chat/messages

{
  "chat_key": "c_xxx",
  "content": "Hello 👋"
}

Typing Indicator
POST /chat/conversations/{chat_key}/typing

Mark Conversation as Read
POST /chat/conversations/{chat_key}/read

Star / Unstar Message
POST   /chat/messages/{id}/star
DELETE /chat/messages/{id}/star

List Starred Messages
GET /chat/messages/starred

Invite User (Admin Only)
POST /chat/conversations/{chat_key}/invite

{
  "user_id": 5
}

Leave Conversation
POST /chat/conversations/{chat_key}/leave

📡 Realtime Events

All realtime features are optional.

Events emitted:

message.sent

message.star.toggled

conversation.created

conversation.updated

conversation.invited

conversation.left

user.typing

You choose:

Pusher

Laravel WebSockets

Redis

Or disable realtime completely

🧠 Design Philosophy

❌ No UI
❌ No frontend assumptions
❌ No forced websocket provider

✅ API-first
✅ Event-driven
✅ Extendable
✅ Production-ready
---------------------------------------------------------------------------------------------------------------------------------------------------
                                    🆚 Comparison

Feature	This Package	                             Typical Chat Packages
Realtime optional	    ✅	                             ❌ forced
Mobile-first	        ✅	                             ❌
No UI	                ✅	                             ❌
Extensible	            ✅	                             ⚠️
Laravel-native	        ✅	                             ⚠️


---------------------------------------------------------------------------------------------------------------------------------------------
🛣️ Roadmap

✅ Attachments

⏳ Message reactions

⏳ Conversation archiving

⏳ Facade API (Chat::send())

⏳ Rate limiting

⏳ Moderation hooks

👤 Author

Andrew Rafat
📧 andrewrafat91@gmail.com
