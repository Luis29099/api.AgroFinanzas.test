<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finance_user_app', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user_app');
            $table-> foreign('id_user_app')
            ->references('id')
            ->on('user_apps')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            
            $table->unsignedBigInteger('id_finance');
            $table-> foreign('id_finance')
            ->references('id')
            ->on('finances')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_user_app');
    }
};
