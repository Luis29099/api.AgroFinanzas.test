<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Animal_production extends Model
{
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


    // .......................................................................................

    
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
