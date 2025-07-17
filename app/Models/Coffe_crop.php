<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Coffe_crop extends Model
{
    protected $fillable = ['variety', 'estimated_production', 'id_crop'];

    protected $allowIncluded = ['crop'];
     public function crop(){
        return $this->belongsTo(Crop::class,'id_crop');

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
