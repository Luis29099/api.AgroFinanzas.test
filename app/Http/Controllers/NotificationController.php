<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Obtener notificaciones del usuario autenticado
    public function getMyNotifications()
    {
        $userId = auth()->id();
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

    // Solo el conteo de no leídas (para polling ligero del usuario autenticado)
    public function getMyUnreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    // Marcar TODAS del usuario autenticado como leídas
    public function markAllMyRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Todas marcadas como leídas.']);
    }

    // Obtener notificaciones de un usuario específico (para admin o propósitos internos)
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

    // Marcar una notificación como leída
    public function markRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // Marcar TODAS como leídas para un usuario específico (admin)
    public function markAllRead($userId)
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Todas marcadas como leídas para el usuario.']);
    }

    // Solo el conteo de no leídas para un usuario específico (para admin)
    public function unreadCount($userId)
    {
        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}