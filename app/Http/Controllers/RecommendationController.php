<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class RecommendationController extends Controller
{
    public function index()
    {
        return response()->json(
            Recommendation::with(['user', 'replies.user'])
                ->whereNull('parent_id')
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'text'       => 'required|string',
            'category'   => 'required|string',
            'user_id'    => 'nullable|integer|exists:users,id',
            'parent_id'  => 'nullable|exists:recommendations,id',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480',
        ]);

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
                return response()->json(['success' => false, 'message' => 'Error al subir el archivo: ' . $e->getMessage()], 500);
            }
        }

        $recommendation = Recommendation::create([
            'text'       => $request->text,
            'category'   => $request->category,
            'user_id'    => $request->user_id,
            'parent_id'  => $request->parent_id,
            'media_url'  => $mediaUrl,
            'media_type' => $mediaType,
        ]);

        // ✅ CREAR NOTIFICACIÓN si es una respuesta a un comentario
        if ($request->parent_id) {
            $parentComment = Recommendation::find($request->parent_id);

            // Solo notificar si el comentario padre tiene dueño
            // y el que responde NO es el mismo dueño
            if ($parentComment &&
                $parentComment->user_id &&
                $parentComment->user_id !== (int) $request->user_id)
            {
                $fromUserName = $recommendation->load('user')->user?->name ?? 'Alguien';
                $preview = strlen($request->text) > 60
                    ? substr($request->text, 0, 60) . '...'
                    : $request->text;

                Notification::create([
                    'user_id'           => $parentComment->user_id,
                    'from_user_id'      => $request->user_id,
                    'recommendation_id' => $parentComment->id,
                    'type'              => 'reply',
                    'message'           => "{$fromUserName} respondió tu publicación: \"{$preview}\"",
                    'is_read'           => false,
                ]);
            }
        }

        return response()->json($recommendation->load('user'), 201);
    }

    public function show($id)
    {
        return response()->json(
            Recommendation::with(['user', 'replies.user'])->findOrFail($id)
        );
    }
}