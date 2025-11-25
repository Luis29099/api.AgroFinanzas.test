<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory, ApiScopes;
    protected $fillable = ['text', 'id_user_app'];


    protected $allowIncluded = ['user_app','user_app.crops','user_app.crops.avocado_crop' ];// .....................................


    
    protected $allowFilter = ['id', 'text'];  

    protected $allowSort = ['id', 'date', 'id_user_app'];

    public function user()
{
    return $this->belongsTo(User_app::class, 'id_user_app');
}



}


