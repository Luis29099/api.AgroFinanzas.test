<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Hen extends Model
{
    protected $fillable = ['breed', 'daily_egg_production', 'monthly_egg_total', 'id_animal_production'];

    protected $allowIncluded = ['animal_production']; 
    public function animal_production()
    {
        return $this->belongsTo(Animal_production::class, 'id_animal_production');
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
