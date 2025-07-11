<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    public function user_apps()
{
    return $this->belongsToMany(User_app::class, 'crop_user_app', 'id_crop', 'id_user_app');
}

    public function avocado_crop(){
        return $this->hasOne(Avocado_crop::class);
        
    }
    public function coffe_crop(){
        return $this->hasOne(Coffe_crop::class);
        
    }
 
}
