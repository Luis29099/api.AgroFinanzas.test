<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use User;

class Finance extends Model
{
    protected $fillable=['income','expense','profit','date'];
    protected $allowIncluded=['user_apps','animal_production'];
    public function user_apps()
{
    
    return $this->belongsToMany(User_app::class, 'finance_user_app', 'id_finance', 'id_user_app');
}


public function animal_production(){
    return $this->belongsTo(Animal_production::class,'id_animal_production');
        
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
