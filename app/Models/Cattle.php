<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cattle extends Model
{
     public function Animal_production(){
        return $this->belongsTo('App\Models\Animal_production');
        
    }
    

}
