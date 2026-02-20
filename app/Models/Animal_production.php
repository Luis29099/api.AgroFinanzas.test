<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal_production extends Model
{
    use HasFactory, ApiScopes;

    protected $fillable = ['type', 'quantity', 'acquisition_date', 'user_id'];

    protected $allowIncluded = ['user', 'finance', 'cattles', 'hens'];

    protected $allowFilter = ['id', 'type', 'acquisition_date', 'quantity'];

    protected $allowSort = ['id', 'type', 'acquisition_date', 'quantity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function finance()
    {
        return $this->hasOne(Finance::class, 'id_animal_production');
    }

    public function cattles()
    {
        return $this->hasMany(Cattle::class, 'id_animal_production');
    }

    public function hens()
    {
        return $this->hasMany(Hen::class, 'id_animal_production');
    }
}