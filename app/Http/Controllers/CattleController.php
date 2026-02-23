<?php

namespace App\Http\Controllers;

use App\Models\Cattle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CattleController extends Controller
{
    // ── Listar animales del usuario ───────────────────────────
    public function index(Request $request)
    {
        $query = Cattle::with(['mother', 'calves', 'user'])
            ->whereNull('mother_id'); // Solo animales principales (no terneros)

        // Filtrar por usuario si se pasa user_id
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrar también terneros si se pide
        if ($request->boolean('include_calves')) {
            $query = Cattle::with(['mother', 'calves', 'user']);
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        $cattle = $query->latest()->get();

        // Conteos útiles para el dashboard
        $summary = [
            'total'   => $cattle->count(),
            'active'  => $cattle->where('status', 'active')->count(),
            'females' => $cattle->where('gender', 'female')->count(),
            'males'   => $cattle->where('gender', 'male')->count(),
            'calves'  => Cattle::where('user_id', $request->user_id)
                               ->whereNotNull('mother_id')->count(),
        ];

        return response()->json([
            'success' => true,
            'cattle'  => $cattle,
            'summary' => $summary,
        ]);
    }

    // ── Ver un animal individual ──────────────────────────────
    public function show($id)
    {
        $cattle = Cattle::with(['mother', 'calves.calves', 'user', 'animal_production'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'cattle' => $cattle]);
    }

    // ── Registrar animal nuevo ────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'nullable|string|max:100',
            'tag_number'         => 'nullable|string|max:50',
            'breed'              => 'required|string|max:255',
            'average_weight'     => 'nullable|string|max:50',
            'use_milk_meat'      => 'required|in:milk,meat,dual',
            'gender'             => 'required|in:female,male',
            'origin'             => 'nullable|in:born_here,purchased',
            'mother_id'          => 'nullable|exists:cattle,id',
            'birth_date'         => 'nullable|date',
            'status'             => 'nullable|in:active,sold,dead',
            'notes'              => 'nullable|string|max:1000',
            'user_id'            => 'required|exists:users,id',
            'id_animal_production' => 'nullable|exists:animal_productions,id',
            'photo'              => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $photoUrl = null;

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

                $result   = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder'         => 'AgroFinanzas/cattle',
                    'transformation' => ['width' => 800, 'height' => 600, 'crop' => 'fill', 'quality' => 'auto'],
                ]);
                $photoUrl = $result['secure_url'];

            } catch (\Exception $e) {
                Log::error('Cloudinary cattle photo error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error al subir la foto: ' . $e->getMessage()], 500);
            }
        }

        $cattle = Cattle::create([
            'name'               => $request->name,
            'tag_number'         => $request->tag_number,
            'breed'              => $request->breed,
            'average_weight'     => $request->average_weight,
            'use_milk_meat'      => $request->use_milk_meat,
            'gender'             => $request->gender,
            'origin'             => $request->origin,
            'mother_id'          => $request->mother_id,
            'birth_date'         => $request->birth_date,
            'status'             => $request->status ?? 'active',
            'notes'              => $request->notes,
            'user_id'            => $request->user_id,
            'id_animal_production' => $request->id_animal_production,
            'photo_url'          => $photoUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Animal registrado correctamente.',
            'cattle'  => $cattle->load(['mother', 'calves']),
        ], 201);
    }

    // ── Registrar nacimiento de ternero ───────────────────────
    // POST /cattles/{id}/birth
    public function registerBirth(Request $request, $motherId)
    {
        $mother = Cattle::findOrFail($motherId);

        $request->validate([
            'name'        => 'nullable|string|max:100',
            'tag_number'  => 'nullable|string|max:50',
            'gender'      => 'required|in:female,male',
            'birth_date'  => 'required|date',
            'notes'       => 'nullable|string|max:1000',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $photoUrl = null;

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
                $result   = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'AgroFinanzas/cattle',
                    'transformation' => ['width' => 800, 'height' => 600, 'crop' => 'fill'],
                ]);
                $photoUrl = $result['secure_url'];
            } catch (\Exception $e) {
                Log::error('Cloudinary calf photo: ' . $e->getMessage());
            }
        }

        $calf = Cattle::create([
            'name'               => $request->name,
            'tag_number'         => $request->tag_number,
            'breed'              => $mother->breed,       // Hereda raza de la madre
            'use_milk_meat'      => $mother->use_milk_meat,
            'gender'             => $request->gender,
            'origin'             => 'born_here',
            'mother_id'          => $mother->id,
            'birth_date'         => $request->birth_date,
            'status'             => 'active',
            'notes'              => $request->notes,
            'user_id'            => $mother->user_id,
            'id_animal_production' => $mother->id_animal_production,
            'photo_url'          => $photoUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => "¡Ternero registrado! Madre: " . ($mother->name ?? 'Sin nombre'),
            'calf'    => $calf->load('mother'),
        ], 201);
    }

    // ── Actualizar animal ─────────────────────────────────────
    public function update(Request $request, $id)
    {
        $cattle = Cattle::findOrFail($id);

        $request->validate([
            'name'           => 'nullable|string|max:100',
            'tag_number'     => 'nullable|string|max:50',
            'breed'          => 'nullable|string|max:255',
            'average_weight' => 'nullable|string|max:50',
            'use_milk_meat'  => 'nullable|in:milk,meat,dual',
            'gender'         => 'nullable|in:female,male',
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
                    'folder' => 'AgroFinanzas/cattle',
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
                $cattle->$campo = $request->$campo;
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
}