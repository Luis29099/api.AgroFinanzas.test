<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hen;
use Illuminate\Http\Request;

class HenController extends Controller
{
    public function index()
    {
        // $hen = Hen::included()->findOrFail(2);
        // $hen=Hen::included()->get();
        //$hen=Hen::included()->filter()->sort()->get();
        // $hen=Hen::included()->filter()->sort()->getOrPaginate();
        //$hen=Hen::included()->filter()->get();

        $hen = Hen::all();

        return response()->json($hen);
        
    }
    public function show($id)
    {
        $hen = Hen::included()->findOrFail($id);
        return response()->json($hen);
    }
    public function store(Request $request)
{
    $request->validate([
        'breed' => 'required|string|max:255',
        'daily_egg_production' => 'required|string|max:255',
        'monthly_egg_total' => 'required|string|max:255',
        
    ]);


    $hen = Hen::create([
        'breed' => $request->breed,
        'daily_egg_production' => $request->daily_egg_production,
        'monthly_egg_total'=>$request-> monthly_egg_total,

        
    ]);

    return response()->json($hen, 201);
}
}
