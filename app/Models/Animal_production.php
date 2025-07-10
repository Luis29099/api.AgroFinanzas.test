<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal_production extends Model
{
    public function User_app(){
        return $this->belongsTo(User_app::class);

    }
    public function Finance(){
        return $this->hasOne(Finance::class);

        
    }
    public function Cattles(){
        return $this->hasMany(Cattle::class);
        
        
    }
    public function Hens(){
        return $this->hasMany('App\Models\Hen');
        
        
    }
   
    

}
