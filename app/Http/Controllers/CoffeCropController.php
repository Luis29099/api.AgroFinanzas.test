<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coffe_crop;
use Illuminate\Http\Request;

class CoffeCropController extends Controller
{
    public function index()
    {
        // $coffecrops = Coffe_crop::included()->findOrFail(2);
        // $coffecrops=Coffe_crop::included()->get();
      //  $coffecrops=Coffe_crop::included()->filter()->sort()->get();
        // $coffecrops=Coffe_crop::included()->filter()->sort()->getOrPaginate();
        //$coffecrops=Coffe_crop::included()->filter()->get();

        $coffecrops = Coffe_crop::all();

        return response()->json($coffecrops);
    }
     public function show($id)
    {
        $coffecrops = Coffe_crop::included()->findOrFail($id);
        return response()->json($coffecrops);
    }
     public function store(Request $request)
{
    $request->validate([
        'variety' => 'required|string|max:255',
        'estimated_production' => 'required|string|max:255',
    ]);


    $coffecrops = Coffe_crop::create([
        'variety' => $request->variety,
        'estimated_production' => $request->estimated_production,
    ]);
    return response()->json($coffecrops, 201);
}
}
