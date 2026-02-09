<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
{
    return response()->json(
        Recommendation::with(['user', 'replies'])
            ->whereNull('parent_id') // solo comentarios principales
            ->get()
    );
}


    public function store(Request $request)
{
    $request->validate([
        'text' => 'required|string',
        'category' => 'required|string',
        'id_user_app' => 'nullable|integer',
        'parent_id' => 'nullable|exists:recommendations,id'
    ]);

    $recommendation = Recommendation::create([
        'text' => $request->text,
        'category' => $request->category,
        'id_user_app' => $request->id_user_app,
        'parent_id' => $request->parent_id // 👈 clave
    ]);

    return response()->json($recommendation, 201);
}


    public function show($id)
    {
        return response()->json(
            Recommendation::with('user')->findOrFail($id)
        );
    }
}