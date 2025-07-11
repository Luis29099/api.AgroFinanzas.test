<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal_production extends Model
{
    protected $fillable = ['type', 'quantity', 'acquisition_date', 'id_user_app'];

    protected $allowIncluded = ['user_app', 'finance', 'cattles', 'hens'];

   
    public function user_app()
    {
        return $this->belongsTo(User_app::class, 'id_user_app');
    }

    public function finance()
    {
        return $this->hasOne(Finance::class, 'id_animal_production');
    }

    public function cattles()
    {
        return $this->hasMany(Cattle::class, 'id_animal_production');
    }

    public function hens()
    {
        return $this->hasMany(Hen::class, 'id_animal_production');
    }
   public function scopeIncluded($query)
    {
        if (empty(request()->included)) return;

        $relations = explode(',', request()->included);

        foreach ($relations as $key => $relation) {
            if (!in_array($relation, $this->allowIncluded)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations);
    }
    

}
