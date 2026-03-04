<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * FinanceExportController
 *
 * Generates a downloadable PDF of the authenticated client's financial history.
 *
 * Setup:
 *   composer require barryvdh/laravel-dompdf
 *   php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
 *
 * Route (in routes/api.php):
 *   Route::post('/client/finances/export-pdf', [FinanceExportController::class, 'exportPDF'])
 *        ->middleware('auth:sanctum');
 */
class FinanceExportController extends Controller
{
    public function exportPDF(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
{
    try {
        $user   = $request->user();
        $filter = $request->input('filter', 'all');
        $from   = $request->input('date_from');
        $to     = $request->input('date_to');

        $query = \App\Models\Finance::where('user_id', $user->id)
            ->orderBy('date', 'desc');

        if ($filter !== 'all') $query->where('type', $filter);
        if ($from) $query->whereDate('date', '>=', $from);
        if ($to)   $query->whereDate('date', '<=', $to);

        $finances = $query->get();

        $totals = [
            'totalIncome'     => $finances->where('type', 'income')->sum('amount'),
            'totalExpense'    => $finances->where('type', 'expense')->sum('amount'),
            'totalInvestment' => $finances->where('type', 'investment')->sum('amount'),
            'totalDebt'       => $finances->where('type', 'debt')->sum('amount'),
            'totalInventory'  => $finances->where('type', 'inventory')->sum('amount'),
            'totalCosts'      => $finances->where('type', 'costs')->sum('amount'),
        ];
        $totals['balance'] = $totals['totalIncome'] - $totals['totalExpense'];

        $pdf = Pdf::loadView('pdf.finance-history', [
            'user'        => $user,
            'finances'    => $finances,
            'totals'      => $totals,
            'filter'      => $filter,
            'dateFrom'    => $from,
            'dateTo'      => $to,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        $filename = 'historial-financiero-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);

    } catch (\Throwable $e) {
        return response()->json([
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => collect($e->getTrace())->take(5)->toArray(),
        ], 500);
    }
}
}