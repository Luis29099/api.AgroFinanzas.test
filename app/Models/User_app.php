<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User_app extends Model
{

    protected $fillable = ['name', 'email', 'password', 'birth_date'];

    protected $allowIncluded = [
         'crops', 'crops.avocado_crop', 'crops.coffe_crop',
         'recommendations', 'finances',
         'animal_productions', 'animal_productions.finance',
         'animal_productions.cattles', 'animal_productions.hens'
    ];

    public function crops()
{
    return $this->belongsToMany(Crop::class, 'crop_user_app', 'id_user_app', 'id_crop');
}


    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function finances()
{
    return $this->belongsToMany(Finance::class, 'finance_user_app', 'id_user_app', 'id_finance');
}


    public function animal_productions()
    {
        return $this->hasMany(Animal_production::class);
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

