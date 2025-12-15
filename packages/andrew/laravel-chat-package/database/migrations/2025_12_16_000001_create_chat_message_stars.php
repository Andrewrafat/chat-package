<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_message_stars', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('message_id')->index();
            $table->unsignedBigInteger('user_id')->index();

            $table->timestamps();

            $table->unique(['message_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_message_stars');
    }
};
