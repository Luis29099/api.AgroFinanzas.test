<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Finance extends Model
{
    use HasFactory, ApiScopes;

    protected $fillable = [
        'user_id',

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

    protected $allowIncluded = ['user', 'animal_production'];

    protected $allowFilter = [
        'id',
        'user_id',
        'type',
        'amount',
        'date',
        'category',
        'asset_name',
        'creditor',
        'product_name',
        'crop_name'
    ];

    protected $allowSort = [
        'id',
        'type',
        'amount',
        'date',
        'created_at'
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function animal_production()
    {
        return $this->belongsTo(Animal_production::class, 'id_animal_production');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeIncomes($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeInvestments($query)
    {
        return $query->where('type', 'investment');
    }

    public function scopeDebts($query)
    {
        return $query->where('type', 'debt');
    }

    public function scopeInventory($query)
    {
        return $query->where('type', 'inventory');
    }

    public function scopeCosts($query)
    {
        return $query->where('type', 'costs');
    }

    // Accessors
    public function getDebtProgressAttribute()
    {
        if ($this->type !== 'debt' || !$this->installments) {
            return null;
        }
        return round(($this->paid_installments / $this->installments) * 100, 2);
    }

    public function getRemainingDebtAttribute()
    {
        if ($this->type !== 'debt' || !$this->installments) {
            return null;
        }
        $paidAmount = ($this->amount / $this->installments) * $this->paid_installments;
        return $this->amount - $paidAmount;
    }

    public function getTotalInventoryValueAttribute()
    {
        if ($this->type !== 'inventory' || !$this->quantity || !$this->unit_cost) {
            return $this->amount;
        }
        return $this->quantity * $this->unit_cost;
    }

    // Mutators
    public function setAssetNameAttribute($value)
    {
        $this->attributes['asset_name'] = $value ? ucwords(strtolower($value)) : null;
    }

    public function setCropNameAttribute($value)
    {
        $this->attributes['crop_name'] = $value ? ucwords(strtolower($value)) : null;
    }

    public function setProductNameAttribute($value)
    {
        $this->attributes['product_name'] = $value ? ucwords(strtolower($value)) : null;
    }
}