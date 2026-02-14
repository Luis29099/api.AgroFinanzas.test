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
            // 1. Expandir el ENUM para incluir las nuevas categorías
            $table->enum('type', ['income', 'expense', 'investment', 'debt', 'inventory', 'costs'])
                  ->change();
            
            // 2. Agregar campo de categoría específica
            $table->string('category', 100)->nullable()->after('description');
            
            // 3. Campos para INVERSIONES
            $table->string('asset_name', 255)->nullable()->after('category');
            $table->integer('depreciation_years')->nullable()->after('asset_name');
            
            // 4. Campos para DEUDAS
            $table->string('creditor', 255)->nullable()->after('depreciation_years');
            $table->decimal('interest_rate', 5, 2)->nullable()->after('creditor');
            $table->date('due_date')->nullable()->after('interest_rate');
            $table->integer('installments')->nullable()->after('due_date');
            $table->integer('paid_installments')->default(0)->after('installments');
            
            // 5. Campos para INVENTARIO
            $table->string('product_name', 255)->nullable()->after('paid_installments');
            $table->decimal('quantity', 10, 2)->nullable()->after('product_name');
            $table->string('unit', 50)->nullable()->after('quantity');
            $table->decimal('unit_cost', 15, 2)->nullable()->after('unit');
            
            // 6. Campos para COSTOS DE PRODUCCIÓN
            $table->string('crop_name', 255)->nullable()->after('unit_cost');
            $table->decimal('area', 10, 2)->nullable()->after('crop_name');
            $table->string('production_cycle', 100)->nullable()->after('area');
            $table->decimal('cost_per_unit', 15, 2)->nullable()->after('production_cycle');
            
            // 7. Índices para mejorar el rendimiento
            $table->index('type');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            // Eliminar columnas agregadas
            $table->dropColumn([
                'category',
                'asset_name',
                'depreciation_years',
                'creditor',
                'interest_rate',
                'due_date',
                'installments',
                'paid_installments',
                'product_name',
                'quantity',
                'unit',
                'unit_cost',
                'crop_name',
                'area',
                'production_cycle',
                'cost_per_unit'
            ]);
            
            // Restaurar ENUM original
            $table->enum('type', ['income', 'expense'])->change();
            
            // Eliminar índices
            $table->dropIndex(['type']);
            $table->dropIndex(['date']);
        });
    }
};