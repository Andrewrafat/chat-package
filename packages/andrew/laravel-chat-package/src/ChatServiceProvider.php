<?php

namespace Andrew\ChatPackage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
*/
use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Message;

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/
use Andrew\ChatPackage\Services\MessageService;
use Andrew\ChatPackage\Services\ConversationCreateService;
use Andrew\ChatPackage\Services\ConversationQueryService;
use Andrew\ChatPackage\Services\ConversationInviteService;
use Andrew\ChatPackage\Services\ConversationLeaveService;
use Andrew\ChatPackage\Services\ConversationReadService;
use Andrew\ChatPackage\Services\ConversationTypingService;
use Andrew\ChatPackage\Services\StarMessageService;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register bindings & merge config
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Merge Package Configuration
        |--------------------------------------------------------------------------
        */
        $this->mergeConfigFrom(
            __DIR__ . '/../config/chat.php',
            'chat'
        );

        /*
        |--------------------------------------------------------------------------
        | Bind Services (API / Domain Layer)
        |--------------------------------------------------------------------------
        */
        $this->app->singleton(MessageService::class);
        $this->app->singleton(ConversationCreateService::class);
        $this->app->singleton(ConversationQueryService::class);
        $this->app->singleton(ConversationInviteService::class);
        $this->app->singleton(ConversationLeaveService::class);
        $this->app->singleton(ConversationReadService::class);
        $this->app->singleton(ConversationTypingService::class);
        $this->app->singleton(StarMessageService::class);
    }

    /**
     * Boot package features
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Route Model Binding (MUST be before routes)
        |--------------------------------------------------------------------------
        */
        Route::model('message', Message::class);

        /*
        |--------------------------------------------------------------------------
        | Publish Configuration
        |--------------------------------------------------------------------------
        */
        $this->publishes([
            __DIR__ . '/../config/chat.php' => config_path('chat.php'),
        ], 'chat-config');

        /*
        |--------------------------------------------------------------------------
        | Publish Migrations (Optional)
        |--------------------------------------------------------------------------
        */
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'chat-migrations');

        /*
        |--------------------------------------------------------------------------
        | Load Migrations Automatically
        |--------------------------------------------------------------------------
        */
        $this->loadMigrationsFrom(
            __DIR__ . '/../database/migrations'
        );

        /*
        |--------------------------------------------------------------------------
        | Load API Routes (Always Enabled)
        |--------------------------------------------------------------------------
        */
        $this->loadRoutesFrom(
            __DIR__ . '/Routes/api.php'
        );

        /*
        |--------------------------------------------------------------------------
        | Optional UI Layer (Blade + Assets)
        |--------------------------------------------------------------------------
        | Enabled only if developer wants UI
        */
        if (config('chat.ui.enabled', false)) {

            // Load UI routes
            $this->loadRoutesFrom(
                __DIR__ . '/Routes/ui.php'
            );

            // Load Blade views namespace
            $this->loadViewsFrom(
                __DIR__ . '/../resources/views',
                'chat'
            );

            // Publish Blade views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/chat'),
            ], 'chat-ui');

            // Publish JS assets
            $this->publishes([
                __DIR__ . '/../resources/js' => resource_path('js/chat'),
            ], 'chat-ui-assets');

            // Publish CSS assets
            $this->publishes([
                __DIR__ . '/../resources/css' => resource_path('css/chat'),
            ], 'chat-ui-assets');
        }

        /*
        |--------------------------------------------------------------------------
        | Register Realtime Broadcasting Channels
        |--------------------------------------------------------------------------
        */
        $this->registerBroadcastChannels();
    }

    /**
     * Register private broadcast channels
     */
    protected function registerBroadcastChannels(): void
    {
        /*
        |--------------------------------------------------------------------------
        | User Inbox Channel (Sidebar updates)
        |--------------------------------------------------------------------------
        */
        Broadcast::channel('chat.user.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        /*
        |--------------------------------------------------------------------------
        | Conversation Stream Channel
        |--------------------------------------------------------------------------
        | Used for messages, typing, stars, invites, etc.
        */
        Broadcast::channel('chat.{chatKey}', function ($user, $chatKey) {
            return Conversation::withoutGlobalScopes()
                ->where('chat_key', $chatKey)
                ->whereHas(
                    'participants',
                    fn($q) =>
                    $q->where('user_id', $user->id)
                )
                ->exists();
        });
    }
}
