<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Esta migración añade control individual por animal a la tabla cattle existente
    public function up(): void
    {
        Schema::table('cattle', function (Blueprint $table) {
            // Identificación individual
            $table->string('name')->nullable()->after('id');           // Nombre/apodo del animal
            $table->string('tag_number')->nullable()->after('name');   // Número de arete
            $table->string('gender')->default('female')->after('use_milk_meat'); // female | male

            // Origen y genealogía
            $table->string('origin')->nullable();        // 'born_here' | 'purchased'
            $table->unsignedBigInteger('mother_id')->nullable(); // ID de la madre (auto-referencial)
            $table->foreign('mother_id')->references('id')->on('cattle')->onDelete('set null');
            $table->date('birth_date')->nullable();

            // Estado
            $table->string('status')->default('active'); // active | sold | dead
            $table->text('notes')->nullable();

            // Foto en Cloudinary
            $table->string('photo_url')->nullable();

            // Dueño (usuario del sistema)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cattle', function (Blueprint $table) {
            $table->dropForeign(['mother_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'name', 'tag_number', 'gender', 'origin',
                'mother_id', 'birth_date', 'status', 'notes',
                'photo_url', 'user_id',
            ]);
        });
    }
};