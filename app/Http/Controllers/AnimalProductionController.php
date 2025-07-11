<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Animal_production;
use Illuminate\Http\Request;

class AnimalProductionController extends Controller
{
    public function index()
    {
        // $animal_production = animal_production::included()->findOrFail(2);
        $animal_production=Animal_production::included()->get();
      //  $animal_production=Animal_production::included()->filter()->sort()->get();
        // $animal_production=Animal_production::included()->filter()->sort()->getOrPaginate();
        //$animal_production=Animal_production::included()->filter()->get();

        //$animal_production = Animal_production::all();

        return response()->json($animal_production);
    }

}
