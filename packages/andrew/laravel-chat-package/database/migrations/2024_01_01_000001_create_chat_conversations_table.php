<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatConversationsTable extends Migration
{
    public function up()
    {
        Schema::create(
            config('chat.tables.conversations', 'chat_conversations'),
            function (Blueprint $table) {

                $table->id();
                $table->string('chat_key')->unique()->index();

                /**
                 * Creator of the conversation
                 * (user who started the chat)
                 */
                $table->unsignedBigInteger('creator_id')->index();

                /**
                 * Conversation type
                 * private | group | support (future-proof)
                 */
                $table->string('type')->default('private');

                /**
                 * Optional title (for group chats)
                 */
                $table->string('title')->nullable();

                /**
                 * Last message timestamp
                 * (useful for ordering conversations)
                 */
                $table->timestamp('last_message_at')->nullable();

                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            config('chat.tables.conversations', 'chat_conversations')
        );
    }
}
