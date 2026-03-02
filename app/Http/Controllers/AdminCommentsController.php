<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCommentsController extends Controller
{
    // GET /api/admin/comments — Todos los comentarios
    public function index(Request $request)
    {
        $query = Recommendation::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        $comments = $query->latest()->get()->map(function($r) {
            return [
                'id'         => $r->id,
                'content' => $r->text ?? '',
                'created_at' => $r->created_at?->format('d/m/Y H:i'),
                'user'       => [
                    'id'    => $r->user?->id,
                    'name'  => $r->user?->name,
                    'email' => $r->user?->email,
                    'photo' => $r->user?->profile_photo,
                ],
            ];
        });

        return response()->json([
            'success'  => true,
            'comments' => $comments,
            'total'    => $comments->count(),
            'users'    => User::select('id', 'name')->get()
        ]);
    }

    // GET /api/admin/comments/user/{userId} — Comentarios de un usuario
    public function byUser($userId)
{
    $user     = User::findOrFail($userId);
    $comments = Recommendation::where('user_id', $userId)->latest()->get();

    $transformed = $comments->map(function($r) {
        return [
            'id'         => $r->id,
            'content'    => $r->text ?? '',
            'created_at' => $r->created_at?->format('d/m/Y H:i'),
            'user'       => null,
        ];
    });

    return response()->json([
        'success'  => true,
        'user'     => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        'comments' => $transformed,
        'total'    => $transformed->count(),
    ]);
}

    // DELETE /api/admin/comments/{id} — Eliminar comentario
    public function destroy($id)
    {
        $comment = Recommendation::findOrFail($id);
        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comentario eliminado.']);
    }
}