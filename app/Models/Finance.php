<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Factories\HasFactory;
use User;

class Finance extends Model
{
    // protected $fillable=['income','expense','profit','date'];
           use HasFactory;
        
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
public function scopeIncluded(Builder $query)
    {
        
        if (empty($this->allowIncluded) || empty(request('included'))) { 
            return;
        }


        // return request('included');

        $relations  = explode(',', request('included')); 

         //return $relations;


        $allowIncluded = collect($this->allowIncluded); 

        foreach ($relations as $key => $relationship) { 

            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

       // return $relations;

        $query->with($relations); 
    }

    //.............................................................................................

    public function scopeFilter(Builder $query)
    {

        if (empty($this->allowFilter) || empty(request('filter'))) {
            return;
        }

        $filters = request('filter');

        $allowFilter = collect($this->allowFilter);

        foreach ($filters as $filter => $value) {

            if ($allowFilter->contains($filter)) {

                $query->where($filter, 'LIKE', '%' . $value . '%');//nos retorna todos los registros que conincidad, asi sea en una porcion del texto
            }
        }

    }

    //.................................................................................................

    public function scopeSort(Builder $query)
    {

     if (empty($this->allowSort) || empty(request('sort'))) {
            return;
        }

        $sortFields = explode(',', request('sort'));
        $allowSort = collect($this->allowSort);

      foreach ($sortFields as $sortField) {

            $direction = 'asc';

            if(substr($sortField, 0,1)=='-'){ //cambiamos la consulta a 'desc'si el usuario antecede el menos (-) en el valor de la variable sort
                $direction = 'desc';
                $sortField = substr($sortField,1);//copiamos el valor de sort pero omitiendo, el primer caracter por eso inicia desde el indice 1
            }
            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);//ejecutamos la query con la direccion deseada sea 'asc' o 'desc'
            }
        }
        //http://api.blog.test/v1/categories?sort=name
    }




    //...................................................................................

    public function scopeGetOrPaginate(Builder $query)
    {
      if (request('perPage')) {
            $perPage = intval(request('perPage'));//transformamos la cadena que llega en un numero.

            if($perPage){//como la funcion intval retorna 0 si no puede hacer la conversion 0  es = false
                return $query->paginate($perPage);//retornamos la cuonsulta de acuerdo a la ingresado en la vaiable $perPage
            }


         }
           return $query->get();//sino se pasa el valor de $perPage en la URL se pasan todos los registros.
        //http://api.codersfree1.test/v1/categories?perPage=2
    }
}