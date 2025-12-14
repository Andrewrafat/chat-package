<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessageReadsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_message_reads', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('message_id')->index();
            $table->unsignedBigInteger('user_id')->index();

            $table->timestamps();

            // Prevent duplicate read records
            $table->unique(['message_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_message_reads');
    }
}
