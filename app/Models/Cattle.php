<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cattle extends Model
{

    protected $fillable = ['breed', 'average_weight', 'use_milk_meat', 'id_animal_production'];

    protected $allowIncluded = ['animal_production']; // solo si usas el scopeIncluded()

    public function animal_production()
    {
        return $this->belongsTo(animal_production::class, 'id_animal_production');
    }

    public function scopeIncluded(Builder $query)
    {
        
        if (empty($this->allowIncluded) || empty(request('included'))) { 
            return;
        }


        // return request('included');

        $relations  = explode(',', request('included')); 

         //return $relations;


        $allowIncluded = collect($this->allowIncluded); 

        foreach ($relations as $key => $relationship) { 

            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

       // return $relations;

        $query->with($relations); 
    }
}
