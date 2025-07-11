<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use User;

class Finance extends Model
{
    public function user_apps()
{
    return $this->belongsToMany(User_app::class, 'finance_user_app', 'id_finance', 'id_user_app');
}


public function animal_production(){
    return $this->belongsTo(Animal_production::class);
        
    }


}
