<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cattle extends Model
{
use HasFactory, ApiScopes;
    protected $fillable = ['breed', 'average_weight', 'use_milk_meat', 'id_animal_production'];

    protected $allowIncluded = ['animal_production']; // solo si usas el scopeIncluded()   //.....................................

    protected $allowFilter = ['id','breed', 'use_milk_meat'];  

    protected $allowSort = ['id', 'breed', '','use_milk_meat'];






    public function animal_production()
    {
        return $this->belongsTo(animal_production::class, 'id_animal_production');
    }


}
