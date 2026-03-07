<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FinanceController extends Controller
{
    // ── Helper: obtiene el user_id de forma segura ────────────────
    private function resolveUserId(Request $request): ?int
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) return (int) $user->id;

        $uid = $request->input('user_id') ?? $request->query('user_id');
        return $uid ? (int) $uid : null;
    }

    // ═════════════════════════════════════════════════════════════
    //  INDEX
    // ═════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requiere autenticación o user_id'
            ], 401);
        }

        $query     = Finance::where('user_id', $userId);
        $rawFilter = $request->query('filter');

        if (is_string($rawFilter) && $rawFilter !== 'all') {
            $query->where('type', $rawFilter);
        } elseif (is_array($rawFilter)) {
            $query->filter();
        }

        $finances = $query->latest()->get();

        $totalIncome     = Finance::where('user_id', $userId)->incomes()->sum('amount');
        $totalExpense    = Finance::where('user_id', $userId)->expenses()->sum('amount');
        $totalInvestment = Finance::where('user_id', $userId)->investments()->sum('amount');
        $totalDebt       = Finance::where('user_id', $userId)->debts()->sum('amount');
        $totalInventory  = Finance::where('user_id', $userId)->inventory()->sum('amount');
        $totalCosts      = Finance::where('user_id', $userId)->costs()->sum('amount');

        return response()->json([
            'finances'        => $finances,
            'filter'          => is_string($rawFilter) ? $rawFilter : 'all',
            'totalIncome'     => (float) $totalIncome,
            'totalExpense'    => (float) $totalExpense,
            'totalInvestment' => (float) $totalInvestment,
            'totalDebt'       => (float) $totalDebt,
            'totalInventory'  => (float) $totalInventory,
            'totalCosts'      => (float) $totalCosts,
            'balance'         => (float) ($totalIncome - $totalExpense)
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    //  STORE
    // ═════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        Log::info('Store Finance Request:', $request->all());

        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.'
            ], 401);
        }

        $baseRules = [
            'type'        => 'required|in:income,expense,investment,debt,inventory,costs',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:5000',
            'category'    => 'nullable|string|max:100',
        ];

        $specificRules = [];

        switch ($request->type) {
            case 'investment':
                $specificRules = [
                    'asset_name'         => 'required|string|max:255',
                    'depreciation_years' => 'nullable|integer|min:1|max:50',
                ];
                break;
            case 'debt':
                $specificRules = [
                    'creditor'          => 'required|string|max:255',
                    'interest_rate'     => 'nullable|numeric|min:0|max:100',
                    'due_date'          => 'nullable|date|after:date',
                    'installments'      => 'nullable|integer|min:1',
                    'paid_installments' => 'nullable|integer|min:0',
                ];
                break;
            case 'inventory':
                $specificRules = [
                    'product_name' => 'required|string|max:255',
                    'quantity'     => 'required|numeric|min:0',
                    'unit'         => 'required|string|max:50',
                    'unit_cost'    => 'nullable|numeric|min:0',
                ];
                break;
            case 'costs':
                $specificRules = [
                    'crop_name'        => 'required|string|max:255',
                    'area'             => 'nullable|numeric|min:0',
                    'production_cycle' => 'nullable|string|max:100',
                    'cost_per_unit'    => 'nullable|numeric|min:0',
                ];
                break;
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $specificRules));

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $request->merge(['user_id' => $userId]);
            $finance = Finance::create($request->all());

            Log::info('Finance created:', ['id' => $finance->id, 'user_id' => $finance->user_id]);

            return response()->json([
                'success' => true,
                'message' => 'Registro creado correctamente',
                'finance' => $finance
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating finance:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    // ═════════════════════════════════════════════════════════════
    //  UPDATE
    // ═════════════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        $finance = Finance::where('id', $id)->where('user_id', $userId)->first();

        if (!$finance) {
            return response()->json(['success' => false, 'message' => 'Registro no encontrado o no autorizado'], 404);
        }

        $baseRules = [
            'type'        => 'in:income,expense,investment,debt,inventory,costs',
            'amount'      => 'numeric|min:0.01',
            'date'        => 'date',
            'description' => 'nullable|string|max:500',
            'category'    => 'nullable|string|max:100',
        ];

        $specificRules = [];
        $type = $request->type ?? $finance->type;

        switch ($type) {
            case 'investment':
                $specificRules = [
                    'asset_name'         => 'string|max:255',
                    'depreciation_years' => 'nullable|integer|min:1|max:50'
                ];
                break;
            case 'debt':
                $specificRules = [
                    'creditor'          => 'string|max:255',
                    'interest_rate'     => 'nullable|numeric|min:0|max:100',
                    'due_date'          => 'nullable|date',
                    'installments'      => 'nullable|integer|min:1',
                    'paid_installments' => 'nullable|integer|min:0'
                ];
                break;
            case 'inventory':
                $specificRules = [
                    'product_name' => 'string|max:255',
                    'quantity'     => 'numeric|min:0',
                    'unit'         => 'string|max:50',
                    'unit_cost'    => 'nullable|numeric|min:0'
                ];
                break;
            case 'costs':
                $specificRules = [
                    'crop_name'        => 'string|max:255',
                    'area'             => 'nullable|numeric|min:0',
                    'production_cycle' => 'nullable|string|max:100',
                    'cost_per_unit'    => 'nullable|numeric|min:0'
                ];
                break;
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $specificRules));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $validator->errors()
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

    // ═════════════════════════════════════════════════════════════
    //  DESTROY
    // ═════════════════════════════════════════════════════════════
    public function destroy(Request $request, $id)
    {
        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        $finance = Finance::where('id', $id)->where('user_id', $userId)->first();

        if (!$finance) {
            return response()->json(['success' => false, 'message' => 'Registro no encontrado o no autorizado'], 404);
        }

        try {
            $finance->delete();
            return response()->json(['success' => true, 'message' => 'Registro eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    // ═════════════════════════════════════════════════════════════
    //  SHOW
    // ═════════════════════════════════════════════════════════════
    public function show(Request $request, $id)
    {
        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        $finance = Finance::where('id', $id)->where('user_id', $userId)->first();

        if (!$finance) {
            return response()->json(['success' => false, 'message' => 'Registro no encontrado o no autorizado'], 404);
        }

        return response()->json(['success' => true, 'finance' => $finance], 200);
    }

    // ═════════════════════════════════════════════════════════════
    //  STATISTICS
    // ═════════════════════════════════════════════════════════════
    public function statistics(Request $request)
    {
        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        try {
            $stats = [
                'total_income'           => Finance::where('user_id', $userId)->incomes()->sum('amount'),
                'total_expense'          => Finance::where('user_id', $userId)->expenses()->sum('amount'),
                'total_investment'       => Finance::where('user_id', $userId)->investments()->sum('amount'),
                'total_debt'             => Finance::where('user_id', $userId)->debts()->sum('amount'),
                'total_inventory_value'  => Finance::where('user_id', $userId)->inventory()->sum('amount'),
                'total_production_costs' => Finance::where('user_id', $userId)->costs()->sum('amount'),
            ];

            $stats['balance']   = $stats['total_income'] - $stats['total_expense'];
            $stats['net_worth'] = $stats['total_income'] - $stats['total_expense'] - $stats['total_investment'];

            return response()->json(['success' => true, 'statistics' => $stats], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    // ═════════════════════════════════════════════════════════════
    //  PAY DEBT INSTALLMENT
    // ═════════════════════════════════════════════════════════════
    public function payDebtInstallment(Request $request, $id)
    {
        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        $finance = Finance::where('id', $id)
            ->where('user_id', $userId)
            ->where('type', 'debt')
            ->first();

        if (!$finance) {
            return response()->json(['success' => false, 'message' => 'Deuda no encontrada o no autorizada'], 404);
        }

        if ($finance->paid_installments >= $finance->installments) {
            return response()->json(['success' => false, 'message' => 'La deuda ya está completamente pagada'], 400);
        }

        try {
            $finance->paid_installments += 1;
            $finance->save();

            return response()->json([
                'success'                => true,
                'message'                => 'Cuota pagada correctamente',
                'finance'                => $finance,
                'remaining_installments' => $finance->installments - $finance->paid_installments,
                'progress'               => $finance->debt_progress
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    // ═════════════════════════════════════════════════════════════
    //  STORE AJAX
    // ═════════════════════════════════════════════════════════════
    public function storeAjax(Request $request)
    {
        return $this->store($request);
    }

    // ═════════════════════════════════════════════════════════════
    //  ANALYZE — Score de salud financiera + recomendaciones IA
    //  POST /api/finances/analyze
    //  Body: { filter?, date_from?, date_to? }
    //
    //  Requiere en .env:
    //    GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxx
    //  Obtén tu key gratis en: console.groq.com (sin tarjeta)
    // ═════════════════════════════════════════════════════════════
    public function analyze(Request $request)
    {
        $userId = $this->resolveUserId($request);

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        try {
            // ── 1. Obtener datos con los mismos filtros del historial ──
            $filter = $request->input('filter', 'all');
            $from   = $request->input('date_from');
            $to     = $request->input('date_to');

            $query = Finance::where('user_id', $userId)->orderBy('date', 'desc');

            if ($filter !== 'all') {
                $query->where('type', $filter);
            }
            if ($from) { $query->whereDate('date', '>=', $from); }
            if ($to)   { $query->whereDate('date', '<=', $to); }

            $finances = $query->get();

            if ($finances->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay datos financieros para analizar en el período seleccionado.'
                ], 404);
            }

            // ── 2. Construir resumen estructurado ──────────────────────
            $totalIncome     = $finances->where('type', 'income')->sum('amount');
            $totalExpense    = $finances->where('type', 'expense')->sum('amount');
            $totalInvestment = $finances->where('type', 'investment')->sum('amount');
            $totalDebt       = $finances->where('type', 'debt')->sum('amount');
            $totalInventory  = $finances->where('type', 'inventory')->sum('amount');
            $totalCosts      = $finances->where('type', 'costs')->sum('amount');
            $balance         = $totalIncome - $totalExpense;

            // Deudas vencidas sin terminar de pagar
            $overdueDebts = $finances->filter(function ($f) {
                return $f->type === 'debt'
                    && $f->due_date
                    && $f->due_date->isPast()
                    && ($f->paid_installments < $f->installments);
            })->count();

            // Deudas completamente pagadas
            $paidDebts = $finances->filter(function ($f) {
                return $f->type === 'debt'
                    && $f->installments > 0
                    && $f->paid_installments >= $f->installments;
            })->count();

            // Progreso promedio de deudas activas
            $activeDebts     = $finances->where('type', 'debt')->filter(fn($f) => $f->installments > 0);
            $avgDebtProgress = $activeDebts->count() > 0
                ? round($activeDebts->avg(fn($f) => ($f->paid_installments / $f->installments) * 100), 1)
                : 100;

            // Top 5 categorías de gastos
            $expenseByCategory = $finances
                ->where('type', 'expense')
                ->groupBy('category')
                ->map(fn($g) => round($g->sum('amount'), 2))
                ->sortByDesc(fn($v) => $v)
                ->take(5)
                ->toArray();

            // Top 5 categorías de ingresos
            $incomeByCategory = $finances
                ->where('type', 'income')
                ->groupBy('category')
                ->map(fn($g) => round($g->sum('amount'), 2))
                ->sortByDesc(fn($v) => $v)
                ->take(5)
                ->toArray();

            // Costos por cultivo
            $cropCosts = $finances
                ->where('type', 'costs')
                ->groupBy('crop_name')
                ->map(fn($g) => round($g->sum('amount'), 2))
                ->toArray();

            // Tendencia mensual
            $monthlyTrend = $finances
                ->groupBy(fn($f) => $f->date->format('Y-m'))
                ->map(function ($group) {
                    return [
                        'income'  => round($group->where('type', 'income')->sum('amount'), 2),
                        'expense' => round($group->where('type', 'expense')->sum('amount'), 2),
                        'balance' => round(
                            $group->where('type', 'income')->sum('amount') -
                            $group->where('type', 'expense')->sum('amount'), 2
                        ),
                    ];
                })
                ->take(6)
                ->toArray();

            // Meses consecutivos con pérdidas
            $consecutiveLosses = 0;
            $maxConsecutive    = 0;
            foreach ($monthlyTrend as $month) {
                if ($month['balance'] < 0) {
                    $consecutiveLosses++;
                    $maxConsecutive = max($maxConsecutive, $consecutiveLosses);
                } else {
                    $consecutiveLosses = 0;
                }
            }

            // ── 3. Calcular score (0–100) ──────────────────────────────
            $score = 50;

            // Balance positivo/negativo
            if ($balance > 0) $score += 20;
            elseif ($balance < 0) $score -= 20;

            // Ratio ingreso/gasto
            if ($totalExpense > 0) {
                $ratio = $totalIncome / $totalExpense;
                if ($ratio >= 1.5)      $score += 15;
                elseif ($ratio >= 1.0)  $score += 5;
                else                    $score -= 15;
            }

            // Deudas vencidas
            $score -= min($overdueDebts * 5, 20);

            // Progreso de deudas
            if ($avgDebtProgress >= 75)      $score += 10;
            elseif ($avgDebtProgress >= 50)  $score += 5;

            // Meses en pérdida
            if ($maxConsecutive >= 3)      $score -= 15;
            elseif ($maxConsecutive >= 2)  $score -= 8;

            // Inversiones y activos
            if ($totalInvestment > 0) $score += 5;
            if ($totalInventory > 0)  $score += 5;

            $score = max(0, min(100, $score));

            // ── 4. Construir prompt ────────────────────────────────────
            $periodLabel = ($from && $to)
                ? "del $from al $to"
                : ($from ? "desde $from" : ($to ? "hasta $to" : "histórico completo"));

            $expenseCatText = collect($expenseByCategory)
                ->map(fn($v, $k) => '- ' . ($k ?: 'Sin categoría') . ': $' . number_format($v, 0, ',', '.'))
                ->implode("\n") ?: '- Sin datos';

            $incomeCatText = collect($incomeByCategory)
                ->map(fn($v, $k) => '- ' . ($k ?: 'Sin categoría') . ': $' . number_format($v, 0, ',', '.'))
                ->implode("\n") ?: '- Sin datos';

            $cropCostText = empty($cropCosts)
                ? '- Sin datos de cultivos'
                : collect($cropCosts)
                    ->map(fn($v, $k) => '- ' . ($k ?: 'Sin nombre') . ': $' . number_format($v, 0, ',', '.'))
                    ->implode("\n");

            $summaryText = "
RESUMEN FINANCIERO AGRÍCOLA ($periodLabel):

TOTALES:
- Ingresos: $" . number_format($totalIncome, 0, ',', '.') . "
- Gastos: $" . number_format($totalExpense, 0, ',', '.') . "
- Balance neto: $" . number_format($balance, 0, ',', '.') . " (" . ($balance >= 0 ? 'POSITIVO' : 'NEGATIVO') . ")
- Inversiones: $" . number_format($totalInvestment, 0, ',', '.') . "
- Deudas totales: $" . number_format($totalDebt, 0, ',', '.') . "
- Inventario: $" . number_format($totalInventory, 0, ',', '.') . "
- Costos de producción: $" . number_format($totalCosts, 0, ',', '.') . "

DEUDAS:
- Deudas vencidas sin pagar: $overdueDebts
- Progreso promedio de pago: {$avgDebtProgress}%
- Deudas completamente pagadas: $paidDebts

MESES CONSECUTIVOS CON PÉRDIDAS (máximo): $maxConsecutive

GASTOS POR CATEGORÍA (top 5):
$expenseCatText

INGRESOS POR CATEGORÍA (top 5):
$incomeCatText

COSTOS POR CULTIVO:
$cropCostText

REGISTROS ANALIZADOS: " . $finances->count() . "
SCORE CALCULADO: $score/100
";

            $prompt = "Eres un asesor financiero agrícola experto en fincas y agricultura colombiana.
Analiza el siguiente resumen financiero de un agricultor.
Responde ÚNICAMENTE con un objeto JSON válido. Sin texto adicional. Sin bloques de código markdown. Solo el JSON.

$summaryText

El JSON debe tener exactamente esta estructura:
{
  \"score\": <entero entre 0 y 100>,
  \"score_label\": <\"Crítica\" o \"En riesgo\" o \"Moderada\" o \"Saludable\" o \"Excelente\">,
  \"score_color\": <\"#c0392b\" o \"#e67e22\" o \"#d4841a\" o \"#4A7C3F\" o \"#1a6b3a\">,
  \"resumen\": <una oración resumiendo la situación>,
  \"recomendaciones\": [
    {
      \"tipo\": <\"alerta\" o \"mejora\" o \"positivo\">,
      \"icono\": <nombre Font Awesome sin 'fa-', ejemplo: \"triangle-exclamation\">,
      \"titulo\": <máximo 6 palabras>,
      \"detalle\": <3 a 5 oraciones detalladas. Incluye: qué está pasando, por qué importa, y pasos concretos que el agricultor puede tomar esta semana o este mes. Usa números reales del resumen cuando sea posible. Habla directo, como un asesor de confianza.>
    }
  ],
  \"alertas_criticas\": <número de items con tipo 'alerta'>,
  \"mayor_fortaleza\": <una oración sobre el punto más positivo>,
  \"mayor_riesgo\": <una oración sobre el riesgo más urgente, o null si no hay riesgos>
}
Cada detalle debe seguir este esquema mental (sin escribir los títulos):
1) Situación actual con dato real del resumen
2) Consecuencia si no se actúa
3) Acción concreta esta semana
4) Acción a mediano plazo (1-3 meses)
Reglas:
- Genera entre 3 y 5 recomendaciones.
- Usa números reales del resumen en los detalles.
- Habla en lenguaje que un agricultor colombiano entienda.
- Si el balance es negativo, indícalo con claridad y da pasos concretos.
- score_label y score_color deben coincidir: Crítica=#c0392b, En riesgo=#e67e22, Moderada=#d4841a, Saludable=#4A7C3F, Excelente=#1a6b3a";

            // ── 5. Llamar a Groq API ───────────────────────────────────
            $groqKey = env('GROQ_API_KEY');

            if (!$groqKey) {
                // Fallback local si no hay API key configurada
                Log::warning('GROQ_API_KEY no configurada, usando análisis local.');
                return response()->json([
                    'success'          => true,
                    'source'           => 'local',
                    'score'            => $score,
                    'score_label'      => $this->getScoreLabel($score),
                    'score_color'      => $this->getScoreColor($score),
                    'resumen'          => $balance >= 0
                        ? 'Tu finca muestra un balance positivo de $' . number_format($balance, 0, ',', '.') . ' en el período analizado.'
                        : 'Tu finca presenta un déficit de $' . number_format(abs($balance), 0, ',', '.') . ' que requiere atención urgente.',
                    'recomendaciones'  => $this->getLocalRecommendations($balance, $overdueDebts, $maxConsecutive, $totalInvestment),
                    'alertas_criticas' => $overdueDebts + ($maxConsecutive >= 3 ? 1 : 0),
                    'mayor_fortaleza'  => $totalIncome > $totalExpense
                        ? 'Tus ingresos superan tus gastos, señal de una gestión financiera positiva.'
                        : 'Tienes registros de inversión que representan activos para tu finca.',
                    'mayor_riesgo'     => $overdueDebts > 0
                        ? "Tienes $overdueDebts deuda(s) vencida(s) sin pagar que generan intereses adicionales."
                        : ($maxConsecutive >= 2 ? "Llevas $maxConsecutive meses con pérdidas consecutivas." : null),
                    'period'           => $periodLabel,
                    'total_records'    => $finances->count(),
                ]);
            }

            $groqResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $groqKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.1-8b-instant',
                'temperature' => 0.3,
                'max_tokens'  => 2000,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Eres un asesor financiero agrícola experto en Colombia. Responde ÚNICAMENTE con JSON válido, sin texto adicional, sin bloques markdown.'
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt
                    ]
                ],
            ]);

            if (!$groqResponse->successful()) {
                Log::error('Groq API error', [
                    'status' => $groqResponse->status(),
                    'body'   => $groqResponse->body()
                ]);
                throw new \Exception('Error al conectar con el servicio de análisis IA.');
            }

            $groqData = $groqResponse->json();
            $rawText  = $groqData['choices'][0]['message']['content'] ?? '';

            // Limpiar posibles markdown fences que el modelo incluya
            $jsonText = preg_replace('/^```json\s*/i', '', trim($rawText));
            $jsonText = preg_replace('/^```\s*/i', '', $jsonText);
            $jsonText = preg_replace('/```\s*$/i', '', $jsonText);
            $analysis = json_decode(trim($jsonText), true);

            if (!$analysis || !isset($analysis['score'])) {
                Log::error('Groq invalid JSON response', ['raw' => $rawText]);
                throw new \Exception('La respuesta del análisis no tiene el formato esperado.');
            }

            return response()->json([
                'success'       => true,
                'source'        => 'groq',
                'period'        => $periodLabel,
                'total_records' => $finances->count(),
                ...$analysis,
            ]);

        } catch (\Exception $e) {
            Log::error('Finance analyze error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el análisis: ' . $e->getMessage()
            ], 500);
        }
    }

    // ═════════════════════════════════════════════════════════════
    //  HELPERS PRIVADOS — Análisis local (fallback sin API key)
    // ═════════════════════════════════════════════════════════════
    private function getScoreLabel(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Excelente',
            $score >= 70 => 'Saludable',
            $score >= 50 => 'Moderada',
            $score >= 30 => 'En riesgo',
            default      => 'Crítica',
        };
    }

    private function getScoreColor(int $score): string
    {
        return match (true) {
            $score >= 85 => '#1a6b3a',
            $score >= 70 => '#4A7C3F',
            $score >= 50 => '#d4841a',
            $score >= 30 => '#e67e22',
            default      => '#c0392b',
        };
    }

    private function getLocalRecommendations(
        float $balance,
        int $overdueDebts,
        int $consecutiveLosses,
        float $investment
    ): array {
        $recs = [];

        if ($balance < 0) {
            $recs[] = [
                'tipo'   => 'alerta',
                'icono'  => 'triangle-exclamation',
                'titulo' => 'Balance negativo',
                'detalle' => 'Tus gastos superan tus ingresos. Revisa los rubros más altos y evalúa cuáles puedes reducir o eliminar este mes para recuperar el equilibrio.'
            ];
        } else {
            $recs[] = [
                'tipo'   => 'positivo',
                'icono'  => 'circle-check',
                'titulo' => 'Balance positivo',
                'detalle' => 'Tus ingresos superan tus gastos. Considera apartar un porcentaje del excedente para reinversión en la finca o ahorro para temporada baja.'
            ];
        }

        if ($overdueDebts > 0) {
            $recs[] = [
                'tipo'   => 'alerta',
                'icono'  => 'clock',
                'titulo' => 'Deudas vencidas pendientes',
                'detalle' => "Tienes $overdueDebts deuda(s) vencida(s). Prioriza su pago cuanto antes para evitar intereses adicionales y proteger tu historial crediticio."
            ];
        }

        if ($consecutiveLosses >= 2) {
            $recs[] = [
                'tipo'   => 'alerta',
                'icono'  => 'arrow-trend-down',
                'titulo' => 'Pérdidas consecutivas detectadas',
                'detalle' => "Llevas $consecutiveLosses meses seguidos con gastos superiores a los ingresos. Analiza si hay costos estacionales que puedas anticipar y planificar mejor."
            ];
        }

        if ($investment == 0) {
            $recs[] = [
                'tipo'   => 'mejora',
                'icono'  => 'seedling',
                'titulo' => 'Sin inversiones registradas',
                'detalle' => 'No tienes inversiones registradas. Documenta maquinaria, herramientas o mejoras de infraestructura para tener un panorama completo del valor de tu finca.'
            ];
        }

        $recs[] = [
            'tipo'   => 'mejora',
            'icono'  => 'chart-line',
            'titulo' => 'Registra datos regularmente',
            'detalle' => 'Mantener el historial financiero actualizado mes a mes te permite detectar tendencias, anticipar problemas y tomar mejores decisiones para tu finca.'
        ];

        return $recs;
    }
}