<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crop extends Model
{
    use HasFactory, ApiScopes;

    protected $fillable = ['name', 'area', 'sowing_date', 'harvest_date'];

    protected $allowIncluded = ['users', 'avocado_crop', 'coffe_crop'];

    protected $allowFilter = ['id', 'name', 'sowing_date'];

    protected $allowSort = ['id', 'name', 'sowing_date'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'crop_user_app', 'id_crop', 'user_id');
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