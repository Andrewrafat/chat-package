Laravel Chat Package is a lightweight, API-first chat engine for Laravel.
Build private or group chats, send messages, track unread counts, and integrate realtime — without any UI or frontend assumptions.⚡ Quick Start (5 Minutes)


1️⃣ Install composer

require andrew/laravel-chat-package:dev-main

2️⃣ Publish config & migrate
php artisan vendor:publish --tag=chat-config
php artisan migrate

3️⃣ Authenticate (Sanctum)

All endpoints require:

Authorization: Bearer YOUR_TOKEN

🚀 Basic Usage
Create a conversation
POST /chat/conversations

{
  "participants": [2, 3]
}

Send a message
POST /chat/messages

{
  "chat_key": "c_xxxxx",
  "content": "Hello 👋"
}

List conversations
GET /chat/conversations


Returns:

last message

unread count

participants count

Star a message
POST /chat/messages/{message_id}/star

🎯 When to Use This Package

✅ Mobile apps (Flutter / React Native)
✅ SPA frontends (React / Vue / Next.js)
✅ SaaS dashboards
✅ Internal tools
❌ Not opinionated UI chats

🧩 Why Developers Love It

No UI coupling

No forced User model

Clean service architecture

Works with or without realtime

Easy to extend (mute, pin, archive)