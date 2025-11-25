<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hen extends Model
{
    use HasFactory, ApiScopes;
    protected $fillable = ['breed', 'daily_egg_production', 'monthly_egg_total', 'id_animal_production'];

    protected $allowIncluded = ['animal_production']; // .....................................

    protected $allowFilter = ['id','daily_egg_production', 'breed'];  

    protected $allowSort = ['id', 'date', 'id_user_app'];







    public function animal_production()
    {
        return $this->belongsTo(Animal_production::class, 'id_animal_production');
    }
    
}
