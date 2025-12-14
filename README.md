Laravel Chat Package

Event-Driven, Headless, WhatsApp-Style Chat for Laravel

A modern, API-first chat package for Laravel applications with real-time support, group chats, unread counts, typing indicators, and WhatsApp-style message states.

This package is designed to be headless (UI-agnostic) and event-driven, making it suitable for web apps, mobile apps, dashboards, and integrations.

✨ Features

✅ Private & Group Conversations

✅ API-First (No UI assumptions)

✅ Real-Time Events (Pusher / WebSockets)

✅ WhatsApp-style Read Behavior

✅ Typing Indicators

✅ Unread Message Count

✅ Invite / Leave Conversations

✅ Secure Private Channels

✅ Clean Service Architecture

🚧 Message Delivered (planned)

📦 Installation
composer require andrew/laravel-chat-package


Requires:

Laravel 9+

Auth (Sanctum recommended)

Broadcasting configured (Pusher / Laravel Echo)

⚙️ Configuration

Publish config:

php artisan vendor:publish --tag=chat-config


Config file:
config/chat.php

return [
    'tables' => [
        'conversations' => 'chat_conversations',
        'messages'      => 'chat_messages',
        'participants'  => 'chat_participants',
        'reads'         => 'chat_message_reads',
    ],
];

🧱 Database Structure
Conversations
chat_conversations
- id
- chat_key (public UUID)
- creator_id
- type (private | group)
- title
- last_message_at

Messages
chat_messages
- id
- conversation_id
- sender_id
- content
- type (text | system | image)

Participants
chat_participants
- conversation_id
- user_id
- role (admin | member)

Message Reads
chat_message_reads
- message_id
- user_id

🔐 Authentication

All routes are protected by:

auth:sanctum


The package assumes:

You already have users

You manage authentication outside the package

🔑 Public Identifier (chat_key)

All APIs use chat_key, not numeric IDs.

Why?

Prevents DB ID exposure

Stable across systems

Safer public URLs

Matches real-time channel naming

📡 API Endpoints
Create Conversation
POST /chat/conversations

{
  "type": "group",
  "title": "Backend Team",
  "participants": [2, 3, 4]
}

List Conversations
GET /chat/conversations


Returns conversations with:

last message

unread count

metadata

Send Message
POST /chat/messages

{
  "chat_key": "c_xxx",
  "content": "Hello from chat package"
}


Triggers real-time message.sent.

Fetch Messages
GET /chat/conversations/{chat_key}/messages

Invite User (Admin Only)
POST /chat/conversations/{chat_key}/invite

{
  "user_id": 5
}

Leave Conversation
POST /chat/conversations/{chat_key}/leave

Typing Indicator
POST /chat/conversations/{chat_key}/typing


Triggers real-time user.typing.

🟢 Conversation Read (WhatsApp-Style)
Endpoint
POST /chat/conversations/{chat_key}/read

Behavior

Calling this endpoint means:

“The user opened the conversation.”

What happens:

✅ All unread messages are marked as read

✅ Unread count resets to 0

🔔 conversation.updated event is fired

🔄 UI syncs across devices

Important Notes

Request body is ignored by design

Passing message_id has no effect

Read status is conversation-level, not per message

This matches WhatsApp / Telegram behavior

🔔 Real-Time Events
Channels
User Inbox (metadata & updates)
private-chat.user.{userId}

Conversation Stream (messages, typing)
private-chat.{chat_key}

Events Catalog
Event	Fired When
message.sent	Message created
user.typing	User typing
conversation.updated	Read / metadata change
conversation.created	New conversation
conversation.invited	User invited
conversation.left	User left
Example: Message Sent
{
  "data": {
    "id": 12,
    "content": "Hello",
    "sender": { "id": 1 },
    "created_at": "2025-12-14T22:00:00Z"
  }
}

🧠 Message States

The package follows standard messaging lifecycle:

Sent → Delivered → Read


Sent: message saved

Delivered: message reached user device (planned)

Read: user opened the conversation

Currently implemented:

✅ Sent

✅ Read

🚧 Delivered (coming next)

🧩 Architecture Principles

Services handle business logic

Controllers stay thin

Models store data only

Events represent domain actions

No model-level auto events

No UI assumptions

This makes the package:

Easy to extend

Safe for large teams

Suitable for mobile & web

🚧 Roadmap

Message Delivered (✔✔ gray)

Participants list & kick user

Message pagination (cursor-based)

Feature tests

Webhooks support

🧪 Testing

The package is designed to be testable at the service level.

Example:

Test ConversationReadService

Test unread count logic

Test event dispatching

🤝 Contribution

PRs are welcome.

Guidelines:

Keep APIs backward compatible

Do not expose DB IDs

Keep logic in services

Events must remain UI-agnostic
