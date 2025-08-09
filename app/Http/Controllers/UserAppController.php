<?php

namespace App\Http\Controllers;

use App\Models\User_app;
use Illuminate\Http\Request;

class UserAppController extends Controller
{
    public function index()
    {
        // $category = Category::included()->findOrFail(2);
        // $userapps=User_app::included()->get();
       
    //    $userapps=User_app::included()->filter();
       
        // $userapps=User_app::included()->filter()->sort()->getOrPaginate();
        //$categories=Category::included()->filter()->get();

        $userapps = User_app::all();
        //$categories = Category::with(['posts.user'])->get();

        return response()->json($userapps);
    }
    public function show($id)
    {
        $user = User_app::included()->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:user_apps,email',
        'password' => 'required|string|min:6',
        'birth_date' => 'required|date',
    ]);


    $user = User_app::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'birth_date' => $request->birth_date,
    ]);

    return response()->json($user, 201);
}

 }

