<?php

namespace Andrew\ChatPackage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Andrew\ChatPackage\Models\Conversation;

// Services
use Andrew\ChatPackage\Services\MessageService;
use Andrew\ChatPackage\Services\ConversationCreateService;
use Andrew\ChatPackage\Services\ConversationQueryService;
use Andrew\ChatPackage\Services\ConversationInviteService;
use Andrew\ChatPackage\Services\ConversationLeaveService;
use Andrew\ChatPackage\Services\ConversationReadService;
use Andrew\ChatPackage\Services\ConversationTypingService;
use Andrew\ChatPackage\Services\StarMessageService;
use Illuminate\Support\Facades\Route;
use Andrew\ChatPackage\Models\Message;

class ChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/chat.php',
            'chat'
        );

        // ✅ Explicit service bindings (package best practice)
        $this->app->singleton(MessageService::class);
        $this->app->singleton(ConversationCreateService::class);
        $this->app->singleton(ConversationQueryService::class);
        $this->app->singleton(ConversationInviteService::class);
        $this->app->singleton(ConversationLeaveService::class);
        $this->app->singleton(ConversationReadService::class);
        $this->app->singleton(ConversationTypingService::class);
        $this->app->singleton(StarMessageService::class);
    }

    public function boot()
    {
        // ✅ REGISTER MODEL BINDING FIRST
        \Illuminate\Support\Facades\Route::model(
            'message',
            \Andrew\ChatPackage\Models\Message::class
        );

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/chat.php' => config_path('chat.php'),
        ], 'chat-config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load routes (AFTER binding)
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Broadcast channels
        $this->registerBroadcastChannels();
    }

    protected function registerBroadcastChannels(): void
    {
        // Inbox / metadata events
        Broadcast::channel('chat.user.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        // Conversation stream (messages, typing, membership)
        Broadcast::channel('chat.{chatKey}', function ($user, $chatKey) {

            return Conversation::withoutGlobalScopes()
                ->where('chat_key', $chatKey)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->exists();
        });
    }
}
