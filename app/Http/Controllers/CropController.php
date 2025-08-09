<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use Illuminate\Http\Request;

class CropController extends Controller
{
     public function index()
    {
        // $Crop = animal_production::included()->findOrFail(2);
        // $crops=Crop::included()->get();
      //  $Crop=Crop::included()->filter()->sort()->get();
        // $crops=Crop::included()->filter()->sort()->getOrPaginate();
        //$Crop=Crop::included()->filter()->get();

        $crops = Crop::all();

        return response()->json($crops);
    }
    public function show($id)
    {
        $crops = Crop::included()->findOrFail($id);
        return response()->json($crops);
    }
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'area' => 'required|string|max:255',
        'sowing_date' => 'required|string|max:255',
        'harvest_date' => 'required|string|max:255',
       
    ]);


    $crops = Crop::create([
        'name' => $request->name,
        'area' => $request->area,
        'sowing_date' => $request->sowing_date,
        'harvest_date' => $request->harvest_date,
    ]);

    return response()->json($crops, 201);
}
}
