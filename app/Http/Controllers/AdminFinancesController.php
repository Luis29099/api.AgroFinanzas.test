<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\User;
use Illuminate\Http\Request;

class AdminFinancesController extends Controller
{
    // GET /api/admin/finances — Todas las finanzas (con filtro por usuario)
    public function index(Request $request)
    {
        $query = Finance::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $finances = $query->latest()->get();

        // Resumen global
        $summary = [
            'total_income'     => $finances->where('type', 'income')->sum('amount'),
            'total_expense'    => $finances->where('type', 'expense')->sum('amount'),
            'total_investment' => $finances->where('type', 'investment')->sum('amount'),
            'total_debt'       => $finances->where('type', 'debt')->sum('amount'),
            'total_inventory'  => $finances->where('type', 'inventory')->sum('amount'),
            'total_costs'      => $finances->where('type', 'costs')->sum('amount'),
            'records'          => $finances->count(),
        ];

        return response()->json([
            'success'  => true,
            'finances' => $finances,
            'summary'  => $summary,
            'users'    => User::select('id', 'name', 'email')->get()
        ]);
    }

    // GET /api/admin/finances/user/{userId} — Finanzas de un usuario específico
    public function byUser($userId)
    {
        $user     = User::findOrFail($userId);
        $finances = Finance::where('user_id', $userId)->latest()->get();

        $summary = [
            'total_income'     => $finances->where('type', 'income')->sum('amount'),
            'total_expense'    => $finances->where('type', 'expense')->sum('amount'),
            'total_investment' => $finances->where('type', 'investment')->sum('amount'),
            'total_debt'       => $finances->where('type', 'debt')->sum('amount'),
            'total_inventory'  => $finances->where('type', 'inventory')->sum('amount'),
            'total_costs'      => $finances->where('type', 'costs')->sum('amount'),
            'balance'          => $finances->where('type', 'income')->sum('amount')
                                - $finances->where('type', 'expense')->sum('amount'),
            'records'          => $finances->count(),
        ];

        return response()->json([
            'success'  => true,
            'user'     => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'finances' => $finances,
            'summary'  => $summary,
        ]);
    }
}