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
        Schema::create('finances', function (Blueprint $table) {
    $table->id();
    $table->enum('type', ['income', 'expense']); // ingreso o gasto
    $table->decimal('amount', 10, 2);            // valor
    $table->date('date');
    $table->string('description')->nullable();   // en qué fue

    $table->timestamps();
});

//         Schema::create('finances', function (Blueprint $table) {
//     $table->id();
//     $table->decimal('income', 10, 2)->default(0);   // ingresos
//     $table->decimal('expense', 10, 2)->default(0);  // gastos
//     $table->decimal('profit', 10, 2)->default(0);   // utilidad = income - expense
//     $table->date('date');                           // fecha

//    $table->unsignedBigInteger('id_animal_production')->nullable();
// $table->foreign('id_animal_production')
//       ->references('id')
//       ->on('animal_productions')
//       ->onDelete('cascade')
//       ->onUpdate('cascade');
//     $table->timestamps();
// });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};