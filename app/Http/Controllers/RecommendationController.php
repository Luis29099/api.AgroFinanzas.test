<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
    {
        // $category = Category::included()->findOrFail(2);
        // $recomendation=Recommendation::included()->get();
      //  $categories=Category::included()->filter()->sort()->get();
        $recomendation=Recommendation::included()->filter()->sort()->getOrPaginate();
        //$categories=Category::included()->filter()->get();

        //$categories = Category::all();
        //$categories = Category::with(['posts.user'])->get();

        return response()->json($recomendation);
    }
}
