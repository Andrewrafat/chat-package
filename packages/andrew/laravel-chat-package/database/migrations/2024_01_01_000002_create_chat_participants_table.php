<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create(
            config('chat.tables.participants', 'chat_participants'),
            function (Blueprint $table) {

                $table->id();

                /**
                 * Conversation reference (package-owned)
                 */
                $table->unsignedBigInteger('conversation_id')->index();

                /**
                 * Participant user identifier
                 * (NO foreign key on users table)
                 */
                $table->unsignedBigInteger('user_id')->index();

                /**
                 * Role inside conversation
                 * admin | member
                 */
                $table->string('role')->default('member');

                /**
                 * When user joined the conversation
                 */
                $table->timestamp('joined_at')->nullable();

                $table->timestamps();

                /**
                 * Prevent duplicate participation
                 */
                $table->unique(['conversation_id', 'user_id']);
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            config('chat.tables.participants', 'chat_participants')
        );
    }
}
