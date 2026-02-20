<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'birth_date' => 'required|string',
            'profile_photo' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'experience_years' => 'nullable|integer|min:0|max:50',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'profile_photo' => $request->profile_photo,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'experience_years' => $request->experience_years,
        ]);

        return response()->json($user, 201);
    }
}
