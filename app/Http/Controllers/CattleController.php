<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use Illuminate\Http\Request;

class CattleController extends Controller
{
    public function index()
    {
        // $hen = Hen::included()->findOrFail(2);
        $cattle=Cattle::included()->get();
      //  $hen=Hen::included()->filter()->sort()->get();
        // $hen=Hen::included()->filter()->sort()->getOrPaginate();
        //$hen=Hen::included()->filter()->get();

        //$hen = Hen::all();

        return response()->json($cattle);
    }
}
