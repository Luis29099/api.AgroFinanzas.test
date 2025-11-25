<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coffe_crop extends Model
{
    use HasFactory, ApiScopes;
    protected $fillable = ['variety', 'estimated_production', 'id_crop'];

    protected $allowIncluded = ['crop']; //.....................................

    protected $allowFilter = ['id','variety', 'estimated_production'];  

    protected $allowSort = ['id', 'variety', 'estimated_production'];




     public function crop(){
        return $this->belongsTo(Crop::class,'id_crop');

    }

 
}
