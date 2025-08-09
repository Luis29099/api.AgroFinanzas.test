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
        // $animal_production=Animal_production::included()->get();
      //  $animal_production=Animal_production::included()->filter()->sort()->get();
        // $animalproduction=Animal_production::included()->filter()->sort()->getOrPaginate();
        //$animal_production=Animal_production::included()->filter()->get();

        $animal_production = Animal_production::all();

        return response()->json($animal_production);
    }
     public function show($id)
    {
        $animalproduction = Animal_production::included()->findOrFail($id);
        return response()->json($animalproduction);
    }
    public function store(Request $request)
{
    $request->validate([
        'type' => 'required|string|max:255',
        'quantity' => 'required|string|max:255',
        'acquisition_date' => 'required|string|max:255',
    ]);


    $animalproduction = Animal_production::create([
        'type' => $request->type,
        'quantity' => $request->quantity,
        'acquisition_date' => $request->acquisition_date,
    ]);
    return response()->json($animalproduction, 201);
}

}
