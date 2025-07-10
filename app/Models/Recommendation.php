<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    public function User_app(){
        return $this->belongsTo(User_app::class);
        
    }
}
