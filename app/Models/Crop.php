<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'area', 'sowing_date', 'harvest_date'];

    protected $allowIncluded = ['user_apps', 'avocado_crop', 'coffe_crop'];

    
    public function user_apps()
    {
        return $this->belongsToMany(User_app::class, 'crop_user_app', 'id_crop', 'id_user_app');
    }

    
    public function avocado_crop()
    {
        return $this->hasOne(Avocado_crop::class, 'id_crop');
    }

    
    public function coffe_crop()
    {
        return $this->hasOne(Coffe_crop::class, 'id_crop');
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


