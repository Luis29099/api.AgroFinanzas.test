<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Avocado_crop;
use Illuminate\Http\Request;

class AvocadoCropController extends Controller
{
     public function index()
    {
        // $avocadocrops = Avocado_crop::included()->findOrFail(2);
        // $avocadocrops=Avocado_crop::included()->get();
      //  $avocadocrops=Avocado_crop::included()->filter()->sort()->get();
        // $avocadocrops=Avocado_crop::included()->filter()->sort()->getOrPaginate();
        //$avocadocrops=Avocado_crop::included()->filter()->get();

        $avocadocrops = Avocado_crop::all();

        return response()->json($avocadocrops);
    }
    public function show($id)
    {
        $avocadocrops = Avocado_crop::included()->findOrFail($id);
        return response()->json($avocadocrops);
    }
    public function store(Request $request)
{
    $request->validate([
        'variety' => 'required|string|max:255',
        'estimated_production' => 'required|string|max:255',
       
       
    ]);


    $avocadocrops = Avocado_crop::create([
        'variety' => $request->Variety,
        'estimated_production' => $request->estimated_production,
    ]);
    return response()->json($avocadocrops, 201);
}
}
