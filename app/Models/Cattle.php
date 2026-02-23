<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cattle extends Model
{
    use HasFactory, ApiScopes;

    protected $fillable = [
        'name',
        'tag_number',
        'breed',
        'average_weight',
        'use_milk_meat',
        'gender',
        'origin',
        'mother_id',
        'birth_date',
        'status',
        'notes',
        'photo_url',
        'user_id',
        'id_animal_production',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected $allowIncluded = ['animal_production', 'mother', 'calves', 'user'];
    protected $allowFilter   = ['id', 'breed', 'use_milk_meat', 'gender', 'status', 'user_id'];
    protected $allowSort     = ['id', 'breed', 'use_milk_meat', 'birth_date', 'name'];

    // ── Relaciones ────────────────────────────────────────────
    public function animal_production()
    {
        return $this->belongsTo(Animal_production::class, 'id_animal_production');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Madre del animal
    public function mother()
    {
        return $this->belongsTo(Cattle::class, 'mother_id');
    }

    // Crías (terneros) nacidos de esta vaca
    public function calves()
    {
        return $this->hasMany(Cattle::class, 'mother_id');
    }

    // ── Accessors de utilidad ─────────────────────────────────
    public function getAgeAttribute(): ?string
    {
        if (!$this->birth_date) return null;
        $months = $this->birth_date->diffInMonths(now());
        if ($months < 12) return "{$months} meses";
        $years = floor($months / 12);
        $rem   = $months % 12;
        return $rem > 0 ? "{$years} años {$rem} meses" : "{$years} años";
    }

    protected $appends = ['age'];
}