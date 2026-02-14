<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Finance extends Model
{
    use HasFactory, ApiScopes;
    
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        // Campos básicos (income/expense)
        'type', 
        'amount', 
        'date', 
        'description',
        'category',
        
        // Campos para INVERSIONES
        'asset_name',
        'depreciation_years',
        
        // Campos para DEUDAS
        'creditor',
        'interest_rate',
        'due_date',
        'installments',
        'paid_installments',
        
        // Campos para INVENTARIO
        'product_name',
        'quantity',
        'unit',
        'unit_cost',
        
        // Campos para COSTOS DE PRODUCCIÓN
        'crop_name',
        'area',
        'production_cycle',
        'cost_per_unit',
    ];

    /**
     * Tipos de datos para casting
     */
    protected $casts = [
        'amount' => 'float',
        'date' => 'date',
        'due_date' => 'date',
        'interest_rate' => 'float',
        'depreciation_years' => 'integer',
        'installments' => 'integer',
        'paid_installments' => 'integer',
        'quantity' => 'float',
        'unit_cost' => 'float',
        'area' => 'float',
        'cost_per_unit' => 'float',
    ];
    
    /**
     * Relaciones permitidas para incluir en la API
     */
    protected $allowIncluded = ['user_apps', 'animal_production'];
    
    /**
     * Campos permitidos para filtrar
     */
    protected $allowFilter = [
        'id',
        'type',
        'amount',
        'date',
        'category',
        'asset_name',
        'creditor',
        'product_name',
        'crop_name'
    ];
    
    /**
     * Campos permitidos para ordenar
     */
    protected $allowSort = [
        'id',
        'type',
        'amount',
        'date',
        'created_at'
    ];

    /**
     * Relación con usuarios
     */
    public function user_apps()
    {
        return $this->belongsToMany(User_app::class, 'finance_user_app', 'id_finance', 'id_user_app');
    }

    /**
     * Relación con producción animal
     */
    public function animal_production()
    {
        return $this->belongsTo(Animal_production::class, 'id_animal_production');
    }

    /**
     * Scope para filtrar por tipo de transacción
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para obtener ingresos
     */
    public function scopeIncomes($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope para obtener gastos
     */
    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope para obtener inversiones
     */
    public function scopeInvestments($query)
    {
        return $query->where('type', 'investment');
    }

    /**
     * Scope para obtener deudas
     */
    public function scopeDebts($query)
    {
        return $query->where('type', 'debt');
    }

    /**
     * Scope para obtener inventario
     */
    public function scopeInventory($query)
    {
        return $query->where('type', 'inventory');
    }

    /**
     * Scope para obtener costos de producción
     */
    public function scopeCosts($query)
    {
        return $query->where('type', 'costs');
    }

    /**
     * Accessor: Calcula el progreso de pago de una deuda (porcentaje)
     */
    public function getDebtProgressAttribute()
    {
        if ($this->type !== 'debt' || !$this->installments) {
            return null;
        }
        
        return round(($this->paid_installments / $this->installments) * 100, 2);
    }

    /**
     * Accessor: Calcula el monto restante de una deuda
     */
    public function getRemainingDebtAttribute()
    {
        if ($this->type !== 'debt' || !$this->installments) {
            return null;
        }
        
        $paidAmount = ($this->amount / $this->installments) * $this->paid_installments;
        return $this->amount - $paidAmount;
    }

    /**
     * Accessor: Calcula el valor total del inventario
     */
    public function getTotalInventoryValueAttribute()
    {
        if ($this->type !== 'inventory' || !$this->quantity || !$this->unit_cost) {
            return $this->amount;
        }
        
        return $this->quantity * $this->unit_cost;
    }

    /**
     * Mutator: Formatea el nombre del activo en mayúsculas
     */
    public function setAssetNameAttribute($value)
    {
        $this->attributes['asset_name'] = $value ? ucwords(strtolower($value)) : null;
    }

    /**
     * Mutator: Formatea el nombre del cultivo
     */
    public function setCropNameAttribute($value)
    {
        $this->attributes['crop_name'] = $value ? ucwords(strtolower($value)) : null;
    }

    /**
     * Mutator: Formatea el nombre del producto
     */
    public function setProductNameAttribute($value)
    {
        $this->attributes['product_name'] = $value ? ucwords(strtolower($value)) : null;
    }
}