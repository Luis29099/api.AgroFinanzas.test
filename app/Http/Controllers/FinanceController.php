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
        $finances=Finance::included()->get();
      //  $Crop=Crop::included()->filter()->sort()->get();
        // $Crop=Crop::included()->filter()->sort()->getOrPaginate();
        //$Crop=Crop::included()->filter()->get();

        //$Crop = Crop::all();

        return response()->json($finances);
    }
}
