<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller

    // public function index()
    // {
    //     // $category = Category::included()->findOrFail(2);
    //     // $recomendation=Recommendation::included()->get();
    //   //  $categories=Category::included()->filter()->sort()->get();
    //     // $recomendation=Recommendation::included()->filter()->sort()->getOrPaginate();
    //     //$categories=Category::included()->filter()->get();

    //     $recommendation = Recommendation::all();
    //     //$categories = Category::with(['posts.user'])->get();

    //     return response()->json($recommendation);
    // }
//      public function show($id)
//     {
//         $recommendation = Recommendation::included()->findOrFail($id);
//         return response()->json($recommendation);
//     }
//     public function store(Request $request)
// {
//     $request->validate([
//         'text' => 'required|string|max:255',
//         'date' => 'required|email|unique:user_apps,email',
        
//     ]);


//     $recomendation = Recommendation::create([
//         'text' => $request->text,
//         'date' => $request->date,
        
//     ]);

//     return response()->json($recomendation, 201);
// }





{
    public function index()
    {
        return response()->json(Recommendation::with('user')->get());
    }

    public function store(Request $request)
    {
        $recommendation = Recommendation::create([
            'text' => $request->text,
            'id_user_app' => $request->id_user_app,
        ]);

        return response()->json([
            'message' => 'Comentario guardado',
            'recommendation' => $recommendation
        ], 201);
    }
}




