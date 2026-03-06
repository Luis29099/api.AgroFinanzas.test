<?php

namespace App\Http\Controllers;

use App\Models\Cattle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CattleController extends Controller
{
    // ── Helper: obtiene el user_id de forma segura ────────────
    private function resolveUserId(Request $request): ?int
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) return (int) $user->id;

        $uid = $request->input('user_id') ?? $request->query('user_id');
        return $uid ? (int) $uid : null;
    }

    // ── Listar animales del usuario ───────────────────────────
    public function index(Request $request)
{
    $userId = $this->resolveUserId($request);

    $query = Cattle::with(['mother', 'calves', 'user']);

    if ($userId) {
        $query->where('user_id', $userId);
    }

    // ✅ Sin filtro de mother_id — se devuelven adultos Y terneros
    // El frontend (HatoIndex.tsx) los separa con:
    //   const adults = animals.filter(a => !a.mother_id)
    //   const calves  = animals.filter(a =>  a.mother_id)

    $cattle = $query->latest()->get();

    $summary = [
        'total'   => $cattle->count(),
        'machos'  => $cattle->whereIn('gender', ['male', 'macho'])->count(),
        'hembras' => $cattle->whereIn('gender', ['female', 'hembra'])->count(),
        'crias'   => $cattle->whereNotNull('mother_id')->count(),
        'active'  => $cattle->where('status', 'active')->count(),
    ];

    return response()->json([
        'success' => true,
        'cattle'  => $cattle,
        'animals' => $cattle,
        'summary' => $summary,
    ]);
}

    // ── Ver un animal individual ──────────────────────────────
    public function show($id)
    {
        $cattle = Cattle::with(['mother', 'calves.calves', 'user', 'animal_production'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'cattle'  => $cattle,
            'animal'  => $cattle
        ]);
    }

    // ── Registrar animal nuevo ────────────────────────────────
    public function store(Request $request)
    {
        if ($request->has('purpose') && !$request->has('use_milk_meat')) {
            $request->merge(['use_milk_meat' => $request->purpose]);
        }
        if ($request->has('weight') && !$request->has('average_weight')) {
            $request->merge(['average_weight' => $request->weight]);
        }

        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        $request->merge(['user_id' => $userId]);

        $request->validate([
            'name'               => 'nullable|string|max:100',
            'tag_number'         => 'nullable|string|max:50',
            'breed'              => 'required|string|max:255',
            'average_weight'     => 'nullable|string|max:50',
            'use_milk_meat'      => 'required|in:milk,meat,dual',
            'gender'             => 'required|in:female,male,hembra,macho',
            'origin'             => 'nullable|in:born_here,purchased',
            'mother_id'          => 'nullable|exists:cattle,id',
            'birth_date'         => 'nullable|date',
            'status'             => 'nullable|in:active,sold,dead',
            'notes'              => 'nullable|string|max:1000',
            'user_id'            => 'required|exists:users,id',
            'id_animal_production' => 'nullable',
            'photo'              => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $photoUrl = null;

        if ($request->hasFile('photo') && env('CLOUDINARY_CLOUD_NAME')) {
            try {
                $file = $request->file('photo');
                $cloudinary = new Cloudinary(Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true],
                ]));

                $result   = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder'         => 'AgroFinanzas/cattle',
                    'transformation' => ['width' => 800, 'height' => 600, 'crop' => 'fill', 'quality' => 'auto'],
                ]);
                $photoUrl = $result['secure_url'];

            } catch (\Exception $e) {
                Log::error('Cloudinary cattle photo error: ' . $e->getMessage());
            }
        }

        $gender = $request->gender;
        if ($gender === 'hembra') $gender = 'female';
        if ($gender === 'macho')  $gender = 'male';

        $idProduction = $request->id_animal_production;
        if (!$idProduction) {
            $prod = \App\Models\Animal_production::where('user_id', $userId)->first();
            if (!$prod) {
                $prod = \App\Models\Animal_production::create([
                    'type'             => 'hato',
                    'quantity'         => 0,
                    'acquisition_date' => now(),
                    'user_id'          => $userId
                ]);
            }
            $idProduction = $prod->id;
        }

        $cattle = Cattle::create([
            'name'               => $request->name,
            'tag_number'         => $request->tag_number,
            'breed'              => $request->breed,
            'average_weight'     => $request->average_weight,
            'use_milk_meat'      => $request->use_milk_meat,
            'gender'             => $gender,
            'origin'             => $request->origin,
            'mother_id'          => $request->mother_id,
            'birth_date'         => $request->birth_date,
            'status'             => $request->status ?? 'active',
            'notes'              => $request->notes,
            'user_id'            => $userId,
            'id_animal_production' => $idProduction,
            'photo_url'          => $photoUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Animal registrado correctamente.',
            'cattle'  => $cattle->load(['mother', 'calves']),
            'animal'  => $cattle
        ], 201);
    }

    // ── Registrar nacimiento de ternero ───────────────────────
    public function registerBirth(Request $request, $motherId = null)
{
    try {
        $mId = $motherId ?? $request->mother_id;

        if (!$mId) {
            return response()->json(['success' => false, 'message' => 'Se requiere el ID de la madre.'], 400);
        }

        $mother = Cattle::findOrFail($mId);

        $request->validate([
            'name'        => 'nullable|string|max:100',
            'tag_number'  => 'nullable|string|max:50',
            'gender'      => 'required|in:female,male,hembra,macho',
            'birth_date'  => 'required|date',
            'notes'       => 'nullable|string|max:1000',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $photoUrl = null;

        if ($request->hasFile('photo') && env('CLOUDINARY_CLOUD_NAME')) {
            try {
                $file = $request->file('photo');
                $cloudinary = new Cloudinary(Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true],
                ]));
                $result   = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder'         => 'AgroFinanzas/cattle',
                    'transformation' => ['width' => 800, 'height' => 600, 'crop' => 'fill'],
                ]);
                $photoUrl = $result['secure_url'];
            } catch (\Exception $e) {
                Log::error('Cloudinary calf photo: ' . $e->getMessage());
            }
        }

        $gender = $request->gender;
        if ($gender === 'hembra') $gender = 'female';
        if ($gender === 'macho')  $gender = 'male';

        $idProduction = $mother->id_animal_production;
        if (!$idProduction) {
            $prod = \App\Models\Animal_production::where('user_id', $mother->user_id)->first();
            if ($prod) {
                $idProduction = $prod->id;
            }
            // ⚠️ Si no existe, dejamos null — no intentamos crear
        }

        $calf = Cattle::create([
            'name'                 => $request->name,
            'tag_number'           => $request->tag_number,
            'breed'                => $mother->breed,
            'use_milk_meat'        => $mother->use_milk_meat,
            'gender'               => $gender,
            'origin'               => 'born_here',
            'mother_id'            => $mother->id,
            'birth_date'           => $request->birth_date,
            'status'               => 'active',
            'notes'                => $request->notes,
            'user_id'              => $mother->user_id,
            'id_animal_production' => $idProduction,
            'photo_url'            => $photoUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => "¡Ternero registrado! Madre: " . ($mother->name ?? 'Sin nombre'),
            'calf'    => $calf->load('mother'),
        ], 201);

    } catch (\Exception $e) {
        Log::error('registerBirth error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error interno: ' . $e->getMessage()
        ], 500);
    }
}

    // ── Actualizar animal ─────────────────────────────────────
    public function update(Request $request, $id)
    {
        $cattle = Cattle::findOrFail($id);

        if ($request->has('purpose') && !$request->has('use_milk_meat')) {
            $request->merge(['use_milk_meat' => $request->purpose]);
        }
        if ($request->has('weight') && !$request->has('average_weight')) {
            $request->merge(['average_weight' => $request->weight]);
        }

        $request->validate([
            'name'           => 'nullable|string|max:100',
            'tag_number'     => 'nullable|string|max:50',
            'breed'          => 'nullable|string|max:255',
            'average_weight' => 'nullable|string|max:50',
            'use_milk_meat'  => 'nullable|in:milk,meat,dual',
            'gender'         => 'nullable|in:female,male,hembra,macho',
            'status'         => 'nullable|in:active,sold,dead',
            'notes'          => 'nullable|string|max:1000',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $cloudinary = new Cloudinary(Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true],
                ]));
                $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder'         => 'AgroFinanzas/cattle',
                    'transformation' => ['width' => 800, 'height' => 600, 'crop' => 'fill'],
                ]);
                $cattle->photo_url = $result['secure_url'];
            } catch (\Exception $e) {
                Log::error('Cloudinary cattle update: ' . $e->getMessage());
            }
        }

        $campos = ['name', 'tag_number', 'breed', 'average_weight', 'use_milk_meat', 'gender', 'status', 'notes', 'birth_date'];
        foreach ($campos as $campo) {
            if ($request->has($campo)) {
                $val = $request->$campo;
                if ($campo === 'gender') {
                    if ($val === 'hembra') $val = 'female';
                    if ($val === 'macho')  $val = 'male';
                }
                $cattle->$campo = $val;
            }
        }

        $cattle->save();

        return response()->json([
            'success' => true,
            'message' => 'Animal actualizado correctamente.',
            'cattle'  => $cattle->load(['mother', 'calves']),
        ]);
    }

    // ── Eliminar animal ───────────────────────────────────────
    public function destroy($id)
    {
        $cattle = Cattle::findOrFail($id);
        $cattle->delete();

        return response()->json(['success' => true, 'message' => 'Animal eliminado.']);
    }

    // ── Madres posibles ───────────────────────────────────────
    public function mothers(Request $request)
    {
        $userId = $this->resolveUserId($request);

        $mothers = Cattle::where('gender', 'female')
            ->where('status', 'active')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->get();

        return response()->json([
            'success' => true,
            'mothers' => $mothers,
        ]);
    }
}