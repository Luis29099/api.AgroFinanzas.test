<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coffe_crop;
use Illuminate\Http\Request;

class CoffeCropController extends Controller
{
    public function index()
    {
        // $Crop = animal_production::included()->findOrFail(2);
        $coffe_crops=Coffe_crop::included()->get();
      //  $Crop=Crop::included()->filter()->sort()->get();
        // $Crop=Crop::included()->filter()->sort()->getOrPaginate();
        //$Crop=Crop::included()->filter()->get();

        //$Crop = Crop::all();

        return response()->json($coffe_crops);
    }
}
