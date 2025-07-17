<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $fillable = ['text', 'date', 'id_user_app'];

    protected $allowIncluded = ['user_app','user_app.crops','user_app.crops.avocado_crop' ];
    //  protected $allowFilter = ['id', 'text'];


    public function user_app()
{
    return $this->belongsTo(User_app::class, 'id_user_app');
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


