<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User_app extends Model
{

    protected $fillable = ['name', 'email', 'password', 'birth_date'];

    protected $allowIncluded = [
        'Crops', 'Crops.Avocado_crop', 'Crops.Coffe_crop',
        'Recommendations', 'Finances',
        'Animal_productions', 'Animal_productions.Finance',
        'Animal_productions.Cattles', 'Animal_productions.Hens'
    ];

    public function Crops()
    {
        return $this->belongsToMany(Crop::class);
    }

    public function Recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function Finances()
    {
        return $this->belongsToMany(Finance::class);
    }

    public function Animal_productions()
    {
        return $this->hasMany(Animal_production::class);
    }

    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) { // validamos que la lista blanca y la variable included enviada a travez de HTTP no este en vacia.
            return;
        }


        // return request('included');

        $relations  = explode(',', request('included')); //['posts','relation2']//recuperamos el valor de la variable included y separa sus valores por una coma

         //return $relations;


        $allowIncluded = collect($this->allowIncluded); //colocamos en una colecion lo que tiene $allowIncluded en este caso = ['posts','posts.user']

        foreach ($relations as $key => $relationship) { //recorremos el array de relaciones

            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

       // return $relations;

        $query->with($relations); //se ejecuta el query con lo que tiene $relations en ultimas es el valor en la url de included

    }

}

