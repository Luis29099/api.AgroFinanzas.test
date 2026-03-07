<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class RecommendationController extends Controller
{
    // ── Helper: obtiene el user_id de forma segura ────────────
    private function resolveUserId(Request $request): ?int
    {
        // 1) Usuario autenticado via Sanctum
        $user = Auth::guard('sanctum')->user();
        if ($user) return (int) $user->id;

        // 2) Fallback: campo user_id en el body (para testing / casos legacy)
        $uid = $request->input('user_id');
        return $uid ? (int) $uid : null;
    }

    public function index(Request $request)
    {
        $query = Recommendation::with(['user', 'replies.user'])
            ->whereNull('parent_id');

        if ($request->category) {
            $query->where('category', $request->category);
        }

        return response()->json([
            'comments' => $query->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        // Soporte para que el frontend envíe 'content' en lugar de 'text'
        $text = $request->text ?? $request->content;

        $request->validate([
            'category'   => 'required|string',
            'parent_id'  => 'nullable|exists:recommendations,id',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480',
        ]);

        if (!$text) {
            return response()->json(['message' => 'El campo texto es obligatorio.'], 422);
        }

        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['message' => 'No autenticado. Inicia sesión nuevamente.'], 401);
        }

        $mediaUrl  = null;
        $mediaType = null;

        if ($request->hasFile('media_file')) {
            try {
                $file      = $request->file('media_file');
                $isVideo   = str_starts_with($file->getMimeType(), 'video/');
                $mediaType = $isVideo ? 'video' : 'image';

                $cloudinary = new Cloudinary(
                    Configuration::instance([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key'    => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                        'url' => ['secure' => true],
                    ])
                );

                $options = [
                    'folder'        => 'AgroFinanzas/recommendations',
                    'resource_type' => $isVideo ? 'video' : 'image',
                ];

                if (!$isVideo) {
                    $options['transformation'] = [
                        'width' => 1200, 'height' => 800,
                        'crop'  => 'limit', 'quality' => 'auto',
                    ];
                }

                $result   = $cloudinary->uploadApi()->upload($file->getRealPath(), $options);
                $mediaUrl = $result['secure_url'];

            } catch (\Exception $e) {
                Log::error('Cloudinary recommendation media error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el archivo: ' . $e->getMessage()
                ], 500);
            }
        }

        $recommendation = Recommendation::create([
            'text'       => $text,
            'category'   => $request->category,
            'user_id'    => $userId,
            'parent_id'  => $request->parent_id,
            'media_url'  => $mediaUrl,
            'media_type' => $mediaType,
        ]);

        // ✅ Notificación si es respuesta a otro comentario
        if ($request->parent_id) {
            $parentComment = Recommendation::find($request->parent_id);
            if ($parentComment && $parentComment->user_id && $parentComment->user_id !== $userId) {
                $fromUserName = $recommendation->load('user')->user?->name ?? 'Alguien';
                $preview = strlen($text) > 60 ? substr($text, 0, 60) . '...' : $text;

                Notification::create([
                    'user_id'           => $parentComment->user_id,
                    'from_user_id'      => $userId,
                    'recommendation_id' => $parentComment->id,
                    'type'              => 'reply',
                    'message'           => "{$fromUserName} respondió tu publicación: \"{$preview}\"",
                    'is_read'           => false,
                ]);
            }
        }

        return response()->json(['comment' => $recommendation->load('user')], 201);
    }

    public function show($id)
    {
        return response()->json([
            'comment' => Recommendation::with(['user', 'replies.user'])->findOrFail($id)
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->merge(['parent_id' => $id, 'category' => $request->category ?? 'Opinión']);
        return $this->store($request);
    }

    public function destroy($id)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $rec = Recommendation::where('user_id', $user->id)->findOrFail($id);
        $rec->delete();
        return response()->json(['success' => true]);
    }

    public function toggleLike($id)
{
    $user = Auth::guard('sanctum')->user();
    if (!$user) {
        return response()->json(['message' => 'No autenticado.'], 401);
    }

    $recommendation = Recommendation::findOrFail($id);

    $existing = \App\Models\RecommendationLike::where([
        'user_id'           => $user->id,
        'recommendation_id' => $id,
    ])->first();

    if ($existing) {
        $existing->delete();
        $liked = false;
    } else {
        \App\Models\RecommendationLike::create([
            'user_id'           => $user->id,
            'recommendation_id' => $id,
        ]);
        $liked = true;
    }

    return response()->json([
        'liked'       => $liked,
        'likes_count' => $recommendation->likes()->count(),
    ]);
}

public function liked(Request $request)
{
    $user = Auth::guard('sanctum')->user();
    if (!$user) {
        return response()->json(['message' => 'No autenticado.'], 401);
    }

    $likedIds = \App\Models\RecommendationLike::where('user_id', $user->id)
        ->pluck('recommendation_id');

    $recommendations = Recommendation::with(['user'])
        ->whereIn('id', $likedIds)
        ->whereNull('parent_id')
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'liked_recommendations' => $recommendations,
    ]);
}
}