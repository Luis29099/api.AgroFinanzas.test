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
        Schema::create('coffe_crops', function (Blueprint $table) {
            $table->id();
            $table->string('variety');
             $table->string('estimated_production');
              $table->unsignedBigInteger('id_crop');
            $table-> foreign('id_crop')
            ->references('id')
            ->on('crops')
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
        Schema::dropIfExists('coffe_crops');
    }
};
