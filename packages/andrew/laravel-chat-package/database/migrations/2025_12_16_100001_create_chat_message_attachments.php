<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_message_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('message_id')->index();

            $table->string('disk')->default('public'); // local, s3, etc
            $table->string('path'); // storage path
            $table->string('original_name')->nullable();

            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_message_attachments');
    }
};
