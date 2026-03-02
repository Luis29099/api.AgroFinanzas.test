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
        Schema::table('finances', function (Blueprint $table) {
            // Aumentar la capacidad de texto para descripciones largas
            $table->text('description')->nullable()->change();
            
            // Aumentar la capacidad de monto para soportar cifras grandes (hasta 13 dígitos antes del decimal)
            // decimal(15, 2) soporta hasta 999,999,999,999.99
            $table->decimal('amount', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->change();
            $table->decimal('amount', 10, 2)->change();
        });
    }
};
