<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    public function up()
    {
        Schema::create(
            config('chat.tables.messages', 'chat_messages'),
            function (Blueprint $table) {

                $table->id();

                /**
                 * Conversation reference
                 */
                $table->unsignedBigInteger('conversation_id')->index()->nullable(false);

                /**
                 * Sender (do NOT foreign-key users table)
                 */
                $table->unsignedBigInteger('sender_id')->index();

                /**
                 * Message content
                 */
                $table->text('content');

                /**
                 * Message type (future-proof)
                 * text | image | file | system
                 */
                $table->string('type')->default('text');

                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            config('chat.tables.messages', 'chat_messages')
        );
    }
}
