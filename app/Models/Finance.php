<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use User;

class Finance extends Model
{
    public function User_apps()
{
    return $this->belongsToMany(User_app::class);
    
}

public function Animal_production(){
    return $this->belongsTo(Animal_production::class);
        
    }


}
