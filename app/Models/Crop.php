<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    public function User_apps(){
        return $this->belongsToMany(User_app::class);

    }
    public function Avocado_crop(){
        return $this->hasOne('App\Models\Avocado_crop');
        
    }
    public function Coffe_crop(){
        return $this->hasOne('App\Models\Coffe_crop');
        
    }
 
}
