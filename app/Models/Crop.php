<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crop extends Model
{
   use HasFactory, ApiScopes;

    protected $fillable = ['name', 'area', 'sowing_date', 'harvest_date'];

    protected $allowIncluded = ['user_apps', 'avocado_crop', 'coffe_crop'];// .....................................
    
    protected $allowFilter = ['id','name', 'sowing_date'];  

    protected $allowSort = ['id', 'name', 'sowing_date'];

    public function user_apps()
    {
        return $this->belongsToMany(User_app::class, 'crop_user_app', 'id_crop', 'id_user_app');
    }

    
    public function avocado_crop()
    {
        return $this->hasOne(Avocado_crop::class, 'id_crop');
    }

    
    public function coffe_crop()
    {
        return $this->hasOne(Coffe_crop::class, 'id_crop');
    }

    
}


