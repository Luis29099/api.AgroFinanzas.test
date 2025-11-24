<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    // MÉTODO: Retorna todos los registros de finanzas en JSON
    public function index()
    {
        // Se asume que 'Finance' es tu modelo de Eloquent
        // Usamos los scopes incluidos en tu modelo
        $finances = Finance::filter()->sort()->included()->getOrPaginate(); 
        
        // Retorna la colección como un array JSON
        return response()->json($finances);
    }
    
    /**
     * Almacena un nuevo registro de finanza (income/expense).
     * El nombre del método es 'store' para coincidir con la convención de rutas.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) // <-- ¡CORRECCIÓN! El nombre ahora es 'store'
    {
        // Validación unificada con el frontend: min:0.01
        $data = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01', // <-- CORRECCIÓN: Aseguramos min:0.01
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if (empty($data)) {
             return response()->json([
                'success' => false,
                'message' => 'Datos de entrada vacíos o inválidos.'
            ], 400);
        }

        try {
            // Se usa Finance::create() gracias a la configuración $fillable en el modelo
            $finance = Finance::create($data);

            return response()->json([
                'success' => true,
                'finance' => $finance
            ], 201); // 201 Created
            
        } catch (\Exception $e) {
             // Retorna el error de BD para depuración
             return response()->json([
                'success' => false,
                'message' => 'Error al guardar en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Dejo el método storeAjax por si acaso, aunque ahora llama a 'store'
    public function storeAjax(Request $request)
    {
        return $this->store($request);
    }

    // El método 'indexView' se mantiene para compatibilidad con vistas Blade
    public function indexView()
    {
        $finances = Finance::all();
        return view('finances.index', compact('finances'));
    }
    public function update(Request $request, $id)
{
    $finance = Finance::find($id);

    if (!$finance) {
        return response()->json([
            'success' => false,
            'message' => 'Registro no encontrado'
        ], 404);
    }

    $data = $request->validate([
        'type' => 'in:income,expense',
        'amount' => 'numeric|min:0.01',
        'date' => 'date',
        'description' => 'nullable|string',
    ]);

    $finance->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Registro actualizado correctamente',
        'finance' => $finance
    ], 200);
}
public function destroy($id)
{
    $finance = Finance::find($id);

    if (!$finance) {
        return response()->json([
            'success' => false,
            'message' => 'Registro no encontrado'
        ], 404);
    }

    $finance->delete();

    return response()->json([
        'success' => true,
        'message' => 'Registro eliminado correctamente'
    ], 200);
}

}
