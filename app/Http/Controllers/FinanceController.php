<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
     public function index()
    {
        // $Crop = animal_production::included()->findOrFail(2);
        // $finances=Finance::included()->get();
      //  $Crop=Crop::included()->filter()->sort()->get();
        // $finances=Finance::included()->filter()->sort()->getOrPaginate();
        //$Crop=Crop::included()->filter()->get();

        $finances = Finance::all();

        return response()->json($finances);
    }
     public function show($id)
    {
        $finances = Finance::included()->findOrFail($id);
        return response()->json($finances);
    }
    public function store(Request $request)
{
    $request->validate([
        'income' => 'required|string|max:255',
        'expense' => 'required|string|max:255',
        'profit' => 'required|string|max:255',
        'date' => 'required|string|max:255',
        
    ]);


    $finances = Finance::create([
        'income' => $request->income,
        'expense' => $request->expense,
        'profit' => $request->profit,
        'date' => $request->date,
    ]);

    return response()->json($finances, 201);
}
}
