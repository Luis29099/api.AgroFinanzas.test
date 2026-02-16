<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    /**
     * Retorna todos los registros de finanzas del usuario
     * 🔥 FILTRADO POR USUARIO
     */
    public function index(Request $request)
    {
        // Obtener user_id del query param
        $userId = $request->query('user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere user_id como parámetro'
            ], 400);
        }

        // Filtrar solo las finanzas del usuario
        $finances = Finance::where('user_app_id', $userId)
                          ->filter()
                          ->sort()
                          ->included()
                          ->getOrPaginate();
        
        return response()->json($finances);
    }
    
    /**
     * Almacena un nuevo registro de finanza
     * 🔥 AHORA REQUIERE user_app_id
     */
    public function store(Request $request)
    {
        // Log para debugging
        Log::info('Store Finance Request:', $request->all());

        // Validación base
        $baseRules = [
            'user_app_id' => 'required|exists:user_apps,id', // 🔥 REQUERIDO
            'type' => 'required|in:income,expense,investment,debt,inventory,costs',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ];

        // Validaciones específicas por tipo
        $specificRules = [];
        
        switch ($request->type) {
            case 'investment':
                $specificRules = [
                    'asset_name' => 'required|string|max:255',
                    'depreciation_years' => 'nullable|integer|min:1|max:50',
                ];
                break;
                
            case 'debt':
                $specificRules = [
                    'creditor' => 'required|string|max:255',
                    'interest_rate' => 'nullable|numeric|min:0|max:100',
                    'due_date' => 'nullable|date|after:date',
                    'installments' => 'nullable|integer|min:1',
                    'paid_installments' => 'nullable|integer|min:0',
                ];
                break;
                
            case 'inventory':
                $specificRules = [
                    'product_name' => 'required|string|max:255',
                    'quantity' => 'required|numeric|min:0',
                    'unit' => 'required|string|max:50',
                    'unit_cost' => 'nullable|numeric|min:0',
                ];
                break;
                
            case 'costs':
                $specificRules = [
                    'crop_name' => 'required|string|max:255',
                    'area' => 'nullable|numeric|min:0',
                    'production_cycle' => 'nullable|string|max:100',
                    'cost_per_unit' => 'nullable|numeric|min:0',
                ];
                break;
        }

        $rules = array_merge($baseRules, $specificRules);
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 🔥 CREAR CON user_app_id
            $finance = Finance::create($request->all());

            Log::info('Finance created:', ['id' => $finance->id, 'user_app_id' => $finance->user_app_id]);

            return response()->json([
                'success' => true,
                'message' => 'Registro creado correctamente',
                'finance' => $finance
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating finance:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar en la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualiza un registro existente
     * 🔥 VERIFICA QUE EL REGISTRO PERTENEZCA AL USUARIO
     */
    public function update(Request $request, $id)
    {
        $userId = $request->query('user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere user_id como parámetro'
            ], 400);
        }

        // Buscar solo si pertenece al usuario
        $finance = Finance::where('id', $id)
                         ->where('user_app_id', $userId)
                         ->first();

        if (!$finance) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado o no autorizado'
            ], 404);
        }

        // Validación base
        $baseRules = [
            'type' => 'in:income,expense,investment,debt,inventory,costs',
            'amount' => 'numeric|min:0.01',
            'date' => 'date',
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ];

        $specificRules = [];
        $type = $request->type ?? $finance->type;
        
        switch ($type) {
            case 'investment':
                $specificRules = [
                    'asset_name' => 'string|max:255',
                    'depreciation_years' => 'nullable|integer|min:1|max:50',
                ];
                break;
                
            case 'debt':
                $specificRules = [
                    'creditor' => 'string|max:255',
                    'interest_rate' => 'nullable|numeric|min:0|max:100',
                    'due_date' => 'nullable|date',
                    'installments' => 'nullable|integer|min:1',
                    'paid_installments' => 'nullable|integer|min:0',
                ];
                break;
                
            case 'inventory':
                $specificRules = [
                    'product_name' => 'string|max:255',
                    'quantity' => 'numeric|min:0',
                    'unit' => 'string|max:50',
                    'unit_cost' => 'nullable|numeric|min:0',
                ];
                break;
                
            case 'costs':
                $specificRules = [
                    'crop_name' => 'string|max:255',
                    'area' => 'nullable|numeric|min:0',
                    'production_cycle' => 'nullable|string|max:100',
                    'cost_per_unit' => 'nullable|numeric|min:0',
                ];
                break;
        }

        $rules = array_merge($baseRules, $specificRules);
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $finance->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Registro actualizado correctamente',
                'finance' => $finance
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Elimina un registro
     * 🔥 VERIFICA QUE EL REGISTRO PERTENEZCA AL USUARIO
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->query('user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere user_id como parámetro'
            ], 400);
        }

        $finance = Finance::where('id', $id)
                         ->where('user_app_id', $userId)
                         ->first();

        if (!$finance) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado o no autorizado'
            ], 404);
        }

        try {
            $finance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registro eliminado correctamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene un registro específico
     * 🔥 VERIFICA QUE EL REGISTRO PERTENEZCA AL USUARIO
     */
    public function show(Request $request, $id)
    {
        $userId = $request->query('user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere user_id como parámetro'
            ], 400);
        }

        $finance = Finance::where('id', $id)
                         ->where('user_app_id', $userId)
                         ->first();

        if (!$finance) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado o no autorizado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'finance' => $finance
        ], 200);
    }

    /**
     * Obtiene estadísticas generales del usuario
     * 🔥 FILTRADO POR USUARIO
     */
    public function statistics(Request $request)
    {
        $userId = $request->query('user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere user_id como parámetro'
            ], 400);
        }

        try {
            $stats = [
                'total_income' => Finance::where('user_app_id', $userId)->incomes()->sum('amount'),
                'total_expense' => Finance::where('user_app_id', $userId)->expenses()->sum('amount'),
                'total_investment' => Finance::where('user_app_id', $userId)->investments()->sum('amount'),
                'total_debt' => Finance::where('user_app_id', $userId)->debts()->sum('amount'),
                'total_inventory_value' => Finance::where('user_app_id', $userId)->inventory()->sum('amount'),
                'total_production_costs' => Finance::where('user_app_id', $userId)->costs()->sum('amount'),
            ];

            $stats['balance'] = $stats['total_income'] - $stats['total_expense'];
            $stats['net_worth'] = $stats['total_income'] - $stats['total_expense'] - $stats['total_investment'];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pagar cuota de una deuda
     * 🔥 VERIFICA QUE LA DEUDA PERTENEZCA AL USUARIO
     */
    public function payDebtInstallment(Request $request, $id)
    {
        $userId = $request->query('user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere user_id como parámetro'
            ], 400);
        }

        $finance = Finance::where('id', $id)
                         ->where('user_app_id', $userId)
                         ->where('type', 'debt')
                         ->first();

        if (!$finance) {
            return response()->json([
                'success' => false,
                'message' => 'Deuda no encontrada o no autorizada'
            ], 404);
        }

        if ($finance->paid_installments >= $finance->installments) {
            return response()->json([
                'success' => false,
                'message' => 'La deuda ya está completamente pagada'
            ], 400);
        }

        try {
            $finance->paid_installments += 1;
            $finance->save();

            return response()->json([
                'success' => true,
                'message' => 'Cuota pagada correctamente',
                'finance' => $finance,
                'remaining_installments' => $finance->installments - $finance->paid_installments,
                'progress' => $finance->debt_progress
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método legacy para AJAX
     */
    public function storeAjax(Request $request)
    {
        return $this->store($request);
    }
}