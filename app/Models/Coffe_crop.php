<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coffe_crop extends Model
{
     public function Crop(){
        return $this->belongsTo('App\Models\Crop');

    }
    

}
