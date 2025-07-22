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
        $crops=Crop::included()->filter()->sort()->getOrPaginate();
        //$Crop=Crop::included()->filter()->get();

        //$Crop = Crop::all();

        return response()->json($crops);
    }
}
