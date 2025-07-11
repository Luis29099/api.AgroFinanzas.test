<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avocado_crop extends Model
{
     public function crop(){
        return $this->belongsTo(Crop::class);

    }
}
