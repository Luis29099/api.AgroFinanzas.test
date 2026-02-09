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
    Schema::create('recommendations', function (Blueprint $table) {
        $table->id();
        $table->text('text');
        $table->string('category')->default('recomendacion');
        $table->timestamp('date')->default(now());
        $table->unsignedBigInteger('id_user_app')->nullable();
        $table->foreign('id_user_app')
            ->references('id')
            ->on('user_apps')
            ->onDelete('set null')
            ->onUpdate('cascade');
        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};