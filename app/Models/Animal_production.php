<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal_production extends Model

{
    use HasFactory, ApiScopes;
    protected $fillable = ['type', 'quantity', 'acquisition_date', 'id_user_app'];

    protected $allowIncluded = ['user_app', 'finance', 'cattles', 'hens'];//...........................

    protected $allowFilter = ['id','type', 'acquisition_date','quantity'];  

    protected $allowSort = ['id', 'type', 'acquisition_date','quantity'];

   
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


   



    


    

}
