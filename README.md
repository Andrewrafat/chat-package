# Laravel Chat Package

A **developer-first, API-based chat package** for Laravel.  
Supports **one-to-one chats**, **group chats**, **user invitations**, and is fully **SaaS & mobile-ready**.

> No UI. No assumptions. Just a clean chat engine.

---

## ✨ Features

-   ✅ One-to-One Conversations
-   ✅ Group Chats
-   ✅ Invite & Remove Users
-   ✅ Leave Conversation
-   ✅ Role-based Participants (admin / member)
-   ✅ API-first (perfect for mobile apps)
-   ✅ Sanctum authentication
-   ✅ Configurable table names
-   ✅ Package-safe (no dependency on User model)

---

## 📦 Installation

### 1️⃣ Require the package

```bash
composer require andrew/laravel-chat-package:dev-main
2️⃣ Publish configuration
bash
Copy code
php artisan vendor:publish --tag=chat-config
This will create:

arduino
Copy code
config/chat.php
3️⃣ Run migrations
bash
Copy code
php artisan migrate
Tables created:

chat_conversations

chat_messages

chat_participants

🔐 Authentication
This package uses Laravel Sanctum.

Make sure Sanctum is installed and configured.

All requests must include:

makefile
Copy code
Authorization: Bearer YOUR_TOKEN
Accept: application/json
🚀 API Routes
All routes are prefixed automatically (no /api prefix required).

🟢 Create Conversation (One-to-One or Group)
POST /chat/conversations

Request (One-to-One)
json
Copy code
{
  "participants": [2]
}
Request (Group)
json
Copy code
{
  "type": "group",
  "title": "Backend Team",
  "participants": [2, 3, 4]
}
Behavior
Creator is added as admin

Invited users are added as member

🟢 Send Message
POST /chat/messages

json
Copy code
{
  "conversation_id": 1,
  "content": "Hello from chat package"
}
🟢 List Messages in a Conversation
GET /chat/conversations/{id}/messages

🟢 Invite User to Conversation
POST /chat/conversations/{id}/invite

json
Copy code
{
  "user_id": 5
}
⚠️ Only admins can invite users.

🟢 Leave Conversation
POST /chat/conversations/{id}/leave

Removes the authenticated user from the conversation

Admins can leave (future logic can reassign admin)

🧠 Concepts
Conversations
Represents a chat room:

private (one-to-one)

group

Participants
Each user in a conversation:

role: admin or member

joined_at timestamp

Messages
Each message belongs to:

a conversation

a sender (auth user)

⚙️ Configuration
config/chat.php

php
Copy code
return [

    'auth_guard' => 'sanctum',

    'tables' => [
        'conversations' => 'chat_conversations',
        'messages'      => 'chat_messages',
        'participants'  => 'chat_participants',
    ],

];
🧩 Design Philosophy
❌ No UI

❌ No forced User model

❌ No assumptions

✅ API-first

✅ Mobile-ready

✅ SaaS-friendly

✅ Extensible

🛣️ Roadmap
🔜 Real-time events (MessageSent)

🔜 Typing indicators

🔜 Read receipts

🔜 Attachments

🔜 Admin reassignment

🔜 Facade API (Chat::send())

```
