<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use Illuminate\Http\Request;

class CattleController extends Controller
{
    public function index()
    {
        // $cattle = Cattle::included()->findOrFail(2);
        // $cattle=Cattle::included()->get();
      //  $cattle=Cattle::included()->filter()->sort()->get();
        // $cattle=Cattle::included()->filter()->sort()->getOrPaginate();
        //$cattle=Cattle::included()->filter()->get();

        $cattle = Cattle::all();

        return response()->json($cattle);
    }
    public function show($id)
    {
        $cattle = Cattle::included()->findOrFail($id);
        return response()->json($cattle);
    }
    public function store(Request $request)
{
    $request->validate([
        'breed' => 'required|string|max:255',
        'average_weight' => 'required|string|max:255',
        'use_milk_meat' => 'required|string|max:255',
    ]);


    $cattle = Cattle::create([
        'breed' => $request->breed,
        'average_weight' => $request->average_weight,
        'use_milk_meat' => $request->use_milk_meat,
    ]);
    return response()->json($cattle, 201);
}
}
