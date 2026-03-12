<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Recommendation;
use App\Models\RecommendationLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class RecommendationController extends Controller
{
    private function resolveUserId(Request $request): ?int
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) return (int) $user->id;

        $uid = $request->input('user_id');
        return $uid ? (int) $uid : null;
    }

    // ══════════════════════════════════════════════════════
    //  FILTRO DE LENGUAJE INAPROPIADO
    //  Normaliza el texto eliminando separadores comunes
    //  para detectar variantes evasivas (p.ej. "m-i-e-r-d-a")
    // ══════════════════════════════════════════════════════
    private function containsBadWords(string $text): bool
    {
        // Normalizar: minúsculas + quitar separadores y acentos comunes
        $normalized = strtolower($text);
        $normalized = strtr($normalized, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n',
            'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
        ]);
        $normalized = preg_replace('/[.\-_,!? *@#$0]+/', '', $normalized);

        // ── Variantes numéricas comunes ─────────────────────
        // a=4, e=3, i=1, o=0, s=5 (leet speak)
        $leet = strtr($normalized, [
            '4'=>'a', '3'=>'e', '1'=>'i', '0'=>'o', '5'=>'s', '@'=>'a',
        ]);

        $badWords = [
            // ── Groserías colombianas y latinas ──────────────
            'mierda', 'mierd',
            'puta', 'puto', 'putas', 'putos', 'hijueputa', 'hijuepucha',
            'hpta', 'hp', 'h.p', 'jueputa', 'jueputas',
            'malparido', 'malparida', 'malparidos',
            'gonorrea', 'gonorre',
            'pirobo', 'piroba', 'pirobos',
            'marica', 'maricas',               // usado como insulto
            'hdp', 'hd p',
            'soploculo', 'soploculero',
            'cagada', 'cagado',
            'maldito', 'maldita',
            'culo', 'culos',
            'ojete', 'ojetes',
            'chinga', 'chingada', 'chingado',  // México
            'pendejo', 'pendeja', 'pendejos',
            'cabron', 'cabrona', 'cabrones',
            'verga', 'vergas',
            'coño', 'cono',
            'polla', 'pollas',
            'joder', 'jodido', 'jodida',
            'gilipollas',
            'maricon', 'maricona',             // como insulto
            'idiota', 'idiotas',
            'estupido', 'estupida', 'estupidos',
            'imbecil', 'imbeciles',
            'bobo', 'boba',
            'zorra', 'zorras',
            'perra', 'perras',                 // como insulto
            'bastardo', 'bastarda',
            'mamon', 'mamona',
            'culero', 'culera',
            'huevon', 'huevona',               // insulto (no el uso coloquial amistoso)
            'menso', 'mensa',
            'tarado', 'tarada',
            'retrasado', 'retrasada',          // insulto
            'mogolico', 'mogolica',
            'subnormal',
            'inutil', 'inutiles',

            // ── Amenazas / frases agresivas ──────────────────
            'tevoyamatar', 'teboyamatar',
            'teodio', 'tehago', 'tehago',
            'matate', 'muerete',
            'tequieromuerto', 'tequierohacerdano',
            'eresunabashurahumana', 'eresunabashura',
            'terompo', 'tereviento',
            'techasco',

            // ── Discriminación / odio ─────────────────────────
            'negrata', 'negratas',
            'indio', 'india',                  // como insulto despectivo
            'sudaca',
            'mariconsito',

            // ── Variantes textuales evasivas ──────────────────
            'mlp', 'ptm', 'ptmr',
            'wtf',                             // en contexto agresivo
            'fck', 'fuk', 'fuq',
            'bitch', 'bitches',
            'asshole', 'ass',
            'shit', 'shitt',
            'damn',                            // leve, pero incluido
        ];

        foreach ($badWords as $word) {
            $cleanWord = preg_replace('/[.\-_,!? *@#$0]+/', '', strtolower($word));
            if (str_contains($normalized, $cleanWord)) return true;
            if (str_contains($leet,       $cleanWord)) return true;
        }

        return false;
    }

    // ══════════════════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $query = Recommendation::with(['user', 'replies.user'])
            ->whereNull('parent_id');

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $userId   = optional(Auth::guard('sanctum')->user())->id;
        $comments = $query->latest()->get()->map(function ($c) use ($userId) {
            return $this->formatComment($c, $userId);
        });

        return response()->json(['comments' => $comments]);
    }

    // ══════════════════════════════════════════════════════
    //  STORE
    // ══════════════════════════════════════════════════════
    public function store(Request $request)
    {
        // ✅ Acepta tanto "text" (nuevo) como "content" (legado)
        $text = $request->text ?? $request->content;

        $request->validate([
            'category'   => 'required|string',
            'parent_id'  => 'nullable|exists:recommendations,id',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480',
        ]);

        if (!$text) {
            return response()->json(['message' => 'El campo texto es obligatorio.'], 422);
        }

        // ── Filtro de palabras inapropiadas ─────────────────
        if ($this->containsBadWords($text)) {
            return response()->json([
                'message' => 'Tu comentario contiene lenguaje inapropiado. Por favor, cuida el respeto en la comunidad.'
            ], 422);
        }

        $userId = $this->resolveUserId($request);
        if (!$userId) {
            return response()->json([
                'message' => 'No autenticado. Inicia sesión nuevamente.'
            ], 401);
        }

        // ── Subida de media a Cloudinary ────────────────────
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

        // ── Notificación al autor del comentario padre ───────
        if ($request->parent_id) {
            $parent = Recommendation::find($request->parent_id);
            if ($parent && $parent->user_id && $parent->user_id !== $userId) {
                $fromName = $recommendation->load('user')->user?->name ?? 'Alguien';
                $preview  = strlen($text) > 60 ? substr($text, 0, 60) . '...' : $text;

                Notification::create([
                    'user_id'           => $parent->user_id,
                    'from_user_id'      => $userId,
                    'recommendation_id' => $parent->id,
                    'type'              => 'reply',
                    'message'           => "{$fromName} respondió tu publicación: \"{$preview}\"",
                    'is_read'           => false,
                ]);
            }
        }

        $userId = Auth::guard('sanctum')->user()?->id;
        return response()->json([
            'comment' => $this->formatComment($recommendation->load(['user', 'replies.user']), $userId)
        ], 201);
    }

    public function show($id)
    {
        $userId  = optional(Auth::guard('sanctum')->user())->id;
        $comment = Recommendation::with(['user', 'replies.user'])->findOrFail($id);
        return response()->json(['comment' => $this->formatComment($comment, $userId)]);
    }

    public function reply(Request $request, $id)
    {
        $request->merge(['parent_id' => $id, 'category' => $request->category ?? 'Opinión']);
        return $this->store($request);
    }

    public function destroy($id)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['message' => 'No autenticado.'], 401);

        $rec = Recommendation::where('user_id', $user->id)->findOrFail($id);
        $rec->delete();
        return response()->json(['success' => true]);
    }

    public function toggleLike($id)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['message' => 'No autenticado.'], 401);

        $rec  = Recommendation::findOrFail($id);
        $like = $rec->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $rec->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $rec->likes()->count(),
        ]);
    }

    public function liked()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['message' => 'No autenticado.'], 401);

        $ids = RecommendationLike::where('user_id', $user->id)->pluck('recommendation_id');
        $userId = $user->id;

        $comments = Recommendation::with(['user', 'replies.user'])
            ->whereIn('id', $ids)
            ->whereNull('parent_id')
            ->latest()
            ->get()
            ->map(fn($c) => $this->formatComment($c, $userId));

        return response()->json(['comments' => $comments]);
    }

    // ══════════════════════════════════════════════════════
    //  HELPER — Formatea un comentario para el frontend
    //  Agrega: liked_by_user, likes_count, replies_count
    //  y expone "text" como campo principal.
    // ══════════════════════════════════════════════════════
    private function formatComment(Recommendation $c, ?int $userId): array
    {
        $likesCount   = $c->likes()->count();
        $likedByUser  = $userId ? $c->likes()->where('user_id', $userId)->exists() : false;

        $replies = ($c->replies ?? collect())->map(function ($r) {
            return [
                'id'         => $r->id,
                'text'       => $r->text,
                'user'       => $r->user,
                'media_url'  => $r->media_url,
                'media_type' => $r->media_type,
                'created_at' => $r->created_at,
            ];
        })->values();

        return [
            'id'            => $c->id,
            'text'          => $c->text,        // ✅ campo principal
            'category'      => $c->category,
            'user'          => $c->user,
            'media_url'     => $c->media_url,
            'media_type'    => $c->media_type,
            'created_at'    => $c->created_at,
            'replies'       => $replies,
            'replies_count' => $replies->count(),
            'likes_count'   => $likesCount,
            'liked_by_user' => $likedByUser,    // ✅ ahora sí viene del backend
        ];
    }
}