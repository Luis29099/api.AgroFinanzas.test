<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ── Helper: obtiene el user_id autenticado de forma segura ──
    private function resolveUserId(): ?int
    {
        $user = Auth::guard('sanctum')->user();
        return $user ? (int) $user->id : null;
    }

    // ── Notificaciones del usuario autenticado ────────────────
    public function getMyNotifications()
    {
        $userId = $this->resolveUserId();

        if (!$userId) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $notifications = Notification::with(['fromUser', 'recommendation'])
            ->where('user_id', $userId)
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // ── Conteo no leídas del usuario autenticado ─────────────
    public function getMyUnreadCount()
    {
        $userId = $this->resolveUserId();

        if (!$userId) {
            return response()->json(['unread_count' => 0]);
        }

        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    // ── Marcar TODAS como leídas (usuario autenticado) ────────
    public function markAllMyRead()
    {
        $userId = $this->resolveUserId();

        if (!$userId) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Todas marcadas como leídas.']);
    }

    // ── Notificaciones de un usuario específico (admin) ───────
    public function index($userId)
    {
        $notifications = Notification::with(['fromUser', 'recommendation'])
            ->where('user_id', $userId)
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // ── Marcar una notificación como leída ────────────────────
    public function markRead($id)
    {
        $userId = $this->resolveUserId();

        if (!$userId) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $notification = Notification::where('user_id', $userId)->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // ── Marcar TODAS como leídas para un usuario (admin) ─────
    public function markAllRead($userId)
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Todas marcadas como leídas.']);
    }

    // ── Conteo no leídas para usuario específico (admin) ─────
    public function unreadCount($userId)
    {
        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}