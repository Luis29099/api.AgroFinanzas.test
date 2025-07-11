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
        $hen=Hen::included()->get();
      //  $hen=Hen::included()->filter()->sort()->get();
        // $hen=Hen::included()->filter()->sort()->getOrPaginate();
        //$hen=Hen::included()->filter()->get();

        //$hen = Hen::all();

        return response()->json($hen);
    }
}
