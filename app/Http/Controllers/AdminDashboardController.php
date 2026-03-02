<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Finance;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    // GET /api/admin/dashboard
    public function index(Request $request)
    {
        // Calculate stats
        $stats = [
            'total_users'    => User::count(),
            'active_users'   => User::where('is_verified', true)->count(), // Assuming verified users are active
            'total_finances' => Finance::count(),
            'total_comments' => Recommendation::count(),
            'total_income'   => Finance::where('type', 'income')->sum('amount') ?? 0,
            'total_expense'  => Finance::where('type', 'expense')->sum('amount') ?? 0,
        ];

        // Get latest 5 users
        $users = User::latest()->take(5)->get()->map(function($user) {
            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'is_active'    => $user->is_verified ?? true,
                'created_at'   => $user->created_at?->format('d/m/Y'),
                'profile_photo'=> $user->profile_photo ?? null,
                'finances_count'     => Finance::where('user_id', $user->id)->count(),
                'cattle_count'       => \App\Models\Cattle::where('user_id', $user->id)->count(),
                'recommendations_count' => Recommendation::where('user_id', $user->id)->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'stats'   => $stats,
            'users'   => $users,
        ]);
    }
}
