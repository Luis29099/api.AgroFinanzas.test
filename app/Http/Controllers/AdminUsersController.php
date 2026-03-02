<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Finance;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    // GET /api/admin/users — Lista todos los usuarios
    public function index(Request $request)
    {
        $query = User::query();

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name',  'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->get()->map(function($user) {
            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'is_active'    => $user->is_active ?? true,
                'created_at'   => $user->created_at?->format('d/m/Y'),
                'profile_photo'=> $user->profile_photo ?? null,
                // Conteos rápidos
                'finances_count'     => \App\Models\Finance::where('user_id', $user->id)->count(),
                'cattle_count'       => \App\Models\Cattle::where('user_id', $user->id)->count(),
                'recommendations_count' => \App\Models\Recommendation::where('user_id', $user->id)->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'users'   => $users,
            'total'   => $users->count(),
        ]);
    }

    // GET /api/admin/users/{id} — Detalle completo de un usuario
    public function show($id)
    {
        $user = User::findOrFail($id);

        $finances = Finance::where('user_id', $id)->latest()->get();
        $summary = [
            'total_income'     => $finances->where('type', 'income')->sum('amount'),
            'total_expense'    => $finances->where('type', 'expense')->sum('amount'),
            'total_investment' => $finances->where('type', 'investment')->sum('amount'),
            'total_debt'       => $finances->where('type', 'debt')->sum('amount'),
            'total_inventory'  => $finances->where('type', 'inventory')->sum('amount'),
            'balance'          => $finances->where('type', 'income')->sum('amount') - $finances->where('type', 'expense')->sum('amount'),
            'records'          => $finances->count(),
        ];

        $comments = \App\Models\Recommendation::where('user_id', $id)->latest()->get()->map(function($r) {
            return [
                'id'      => $r->id,
                'content' => $r->text ?? '',
                'created_at' => $r->created_at?->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'user'    => [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'is_active'     => $user->is_active ?? true,
                'created_at'    => $user->created_at?->format('d/m/Y H:i'),
                'profile_photo' => $user->profile_photo ?? null,
            ],
            'finances' => [
                'finances' => $finances,
                'summary'  => $summary,
            ],
            'comments' => $comments,
        ]);
    }

    // PATCH /api/admin/users/{id}/toggle — Activar o desactivar cuenta
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $newStatus = !($user->is_active ?? true);
        $user->update(['is_active' => $newStatus]);

        return response()->json([
            'success'   => true,
            'message'   => $newStatus ? 'Usuario activado.' : 'Usuario desactivado.',
            'is_active' => $newStatus,
        ]);
    }

    // DELETE /api/admin/users/{id} — Eliminar usuario
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
    }
}