<?php

namespace App\Models;

use App\Traits\ApiScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Factories\HasFactory;
use User;

class Finance extends Model
{
    // protected $fillable=['income','expense','profit','date'];
           use HasFactory, ApiScopes;
        
        // ¡IMPORTANTE! Asegúrate de que estos campos estén aquí
        protected $fillable = [
            'type', 
            'amount', 
            'date', 
            'description',
            // Asegúrate de incluir el ID del usuario si estás manejando sesiones
            // 'user_id' 
        ];

        protected $casts = [
            'amount' => 'float',
            'date' => 'date',
        ];
    protected $allowIncluded=['user_apps','animal_production'];// .....................................

    protected $allowFilter = ['id','income', 'date'];  

    protected $allowSort = ['id', 'income', 'date'];

// public static function boot()
//     {
//         parent::boot();

//         static::saving(function ($finance) {
//             $finance->profit = $finance->income - $finance->expense;
//         });
//     }



    public function user_apps()
{
    
    return $this->belongsToMany(User_app::class, 'finance_user_app', 'id_finance', 'id_user_app');
}


public function animal_production(){
    return $this->belongsTo(Animal_production::class,'id_animal_production');
        
    }

}