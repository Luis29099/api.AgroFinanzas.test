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
        Schema::create('hens', function (Blueprint $table) {
            $table->id();
             $table->string('breed');
            $table->string('daily_egg_production');
             $table->string('monthly_egg_total');
            $table->unsignedBigInteger('id_animal_production');
            $table-> foreign('id_animal_production')
            ->references('id')
            ->on('animal_productions')
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
        Schema::dropIfExists('hens');
    }
};
