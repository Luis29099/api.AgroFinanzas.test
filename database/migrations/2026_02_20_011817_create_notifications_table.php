<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Usuario que RECIBE la notificación
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Usuario que GENERÓ la notificación (quien respondió)
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('set null');

            // Recomendación relacionada
            $table->unsignedBigInteger('recommendation_id')->nullable();
            $table->foreign('recommendation_id')->references('id')->on('recommendations')->onDelete('cascade');

            $table->string('type')->default('reply'); // 'reply' por ahora
            $table->text('message');
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};