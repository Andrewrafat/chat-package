🚀 Laravel Chat Package

A headless, API-first chat engine for Laravel.
Built for SaaS platforms, mobile apps, and modern frontends.

❌ No UI
❌ No assumptions
✅ Just conversations, messages, and realtime events.

✨ Why this package?

Most Laravel chat packages:

force a UI

force Livewire / Blade

force a User model

lock you into one realtime solution

This package does none of that.

Laravel Chat Package gives you:

a clean chat core

event-driven architecture

full control over frontend & realtime layer

🧠 Who is this for?

This package is perfect if you are building:

📱 Mobile apps (Flutter / React Native)

🧩 SaaS dashboards

🛒 Marketplaces (buyer ↔ seller chat)

🎧 Customer support systems

🧑‍💻 Internal team chat tools

🤖 AI / bot-powered chat workflows

If you need full control, this package is for you.

🔥 Core Features

✅ One-to-One Conversations

✅ Group Conversations

✅ Role-based Participants (admin / member)

✅ Invite & Remove Users

✅ Leave Conversation

✅ Message Read Receipts

✅ Message Star / Bookmark

✅ Typing Indicators

✅ Realtime Events (driver-agnostic)

✅ API-first (mobile friendly)

✅ Configurable table names

✅ Zero dependency on User model

🏗 Design Philosophy
Principle	Description
Headless	No frontend, no Blade, no Livewire
API-first	Built for REST & mobile
Event-driven	Events are the product
Zero opinion	You choose auth, UI, realtime
Extensible	Easy to add AI, bots, analytics
📦 Installation
1️⃣ Install via Composer
composer require andrew/laravel-chat-package

2️⃣ Publish configuration
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

The package is auth-agnostic, but works perfectly with Laravel Sanctum.

All requests must include:

Authorization: Bearer YOUR_TOKEN
Accept: application/json

🚀 API Overview
Create Conversation

POST /chat/conversations

{
  "participants": [2, 3]
}


Group conversation:

{
  "type": "group",
  "title": "Backend Team",
  "participants": [2, 3, 4]
}

List User Conversations

GET /chat/conversations

Returns:

unread count

last message

participants count

Send Message

POST /chat/messages

{
  "conversation_id": 1,
  "content": "Hello from chat package"
}

Mark Conversation as Read

POST /chat/conversations/{chatKey}/read

Invite User (Admins only)

POST /chat/conversations/{chatKey}/invite

{
  "user_id": 5
}

Leave Conversation

POST /chat/conversations/{chatKey}/leave

Star / Unstar Message

POST /chat/messages/{messageId}/star
DELETE /chat/messages/{messageId}/star

List Starred Messages

GET /chat/messages/starred

⚡ Realtime & Events

This package is event-driven.

Every important action fires an event:

ConversationCreated

ConversationInvited

ConversationLeft

ConversationUpdated

MessageSent

MessageStarToggled

UserTyping

🔁 Realtime is optional

You can use:

Pusher

Laravel WebSockets

Redis

Or disable broadcasting entirely

You decide. The package never forces a driver.

⚙️ Configuration
return [

    'auth_guard' => 'api',

    'tables' => [
        'conversations'  => 'chat_conversations',
        'messages'       => 'chat_messages',
        'participants'   => 'chat_participants',
        'message_reads'  => 'chat_message_reads',
        'message_stars'  => 'chat_message_stars',
    ],

    'broadcast' => [
        'enabled' => true,
        'driver'  => 'pusher', // or websocket / none
    ],
];

🧪 Stability Promise

Semantic versioning

No breaking changes without major release

Clean upgrade path

Open roadmap

🛣 Roadmap

🔜 WebSocket driver toggle

🔜 Attachments

🔜 Message reactions

🔜 Admin reassignment

🔜 Chat facade API

🔜 AI & bot hooks

❤️ Contributing

Contributions are welcome.

Issues

Feature requests

Pull requests

Documentation improvements

📄 License

MIT License

⭐ Final Note

This package is not a chat UI.
It is a chat engine.

Build whatever you want on top of it.
