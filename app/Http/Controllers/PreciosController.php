<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PreciosController extends Controller
{
    /**
     * GET /api/precios
     *
     * Devuelve precios agropecuarios de referencia.
     * Café: scraped en tiempo real desde federaciondecafeteros.org
     * Otros: valores de referencia estáticos (actualizables)
     *
     * Cachea el resultado 6 horas para no saturar la FNC.
     */
    public function index()
    {
        $precios = Cache::remember('precios_agro', now()->addHours(6), function () {
            return [
                'cafe'   => $this->scrapeCafe(),
                'maiz'   => $this->precioEstatico('Maíz',   820,    'COP/kg',    -1.4, 'SIPSA-DANE · Corabastos Bogotá'),
                'leche'  => $this->precioEstatico('Leche',  1420,   'COP/lt',     0.0, 'MADR · Resolución precio mínimo'),
                'pollo'  => $this->precioEstatico('Pollo',  6200,   'COP/kg',     0.8, 'FENAVI · Precio canal mayorista'),
                'papa'   => $this->precioEstatico('Papa',   1150,   'COP/kg',    -2.1, 'SIPSA-DANE · Corabastos Bogotá'),
                'carne'  => $this->precioEstatico('Carne',  18500,  'COP/kg',     1.5, 'FEDEGÁN · Precio gancho'),
            ];
        });

        return response()->json([
            'success'      => true,
            'actualizado'  => now()->toDateTimeString(),
            'precios'      => $precios,
        ]);
    }

    // ──────────────────────────────────────────────────────
    //  SCRAPING FNC — Café
    // ──────────────────────────────────────────────────────
    private function scrapeCafe(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AgroFinanzas/1.0)'])
                ->get('https://federaciondecafeteros.org/wp/');

            if ($response->failed()) {
                return $this->cafeFallback('Error al conectar con FNC');
            }

            $html = $response->body();

            // ── Precio interno (ej: "2.126.000") ──────────
            $precioCarga = null;
            $fechaPrecio = null;
            if (preg_match('/Precio interno de referencia[:\s]*\$([\d\.]+)/i', $html, $m)) {
                // Remover puntos de miles → entero
                $precioCarga = (int) str_replace('.', '', $m[1]);
            }
            if (preg_match('/Precio interno de referencia[^$]*\$[\d\.]+\s*Fecha:\s*(\d{4}-\d{2}-\d{2})/i', $html, $mf)) {
                $fechaPrecio = $mf[1];
            }

            // ── Bolsa NY (ej: "285,50") ───────────────────
            $bolsaNY = null;
            if (preg_match('/Bolsa[^:]*NY[^:]*:\s*([\d,\.]+)/i', $html, $mb)) {
                $bolsaNY = (float) str_replace(',', '.', $mb[1]);
            }

            // ── Tasa de cambio (ej: "3.704") ──────────────
            $tasaCambio = null;
            if (preg_match('/Tasa de cambio[:\s]*\$([\d\.]+)/i', $html, $mt)) {
                $tasaCambio = (int) str_replace('.', '', $mt[1]);
            }

            if (!$precioCarga) {
                return $this->cafeFallback('No se pudo parsear el precio');
            }

            // Precio por kilo = carga (125 kg) ÷ 125
            $precioKg = (int) round($precioCarga / 125);

            return [
                'nombre'        => 'Café',
                'precio'        => $precioKg,
                'precio_carga'  => $precioCarga,
                'unidad'        => 'COP/kg',
                'unidad_carga'  => 'COP/carga 125kg',
                'variacion'     => null,         // calculada vs día anterior si tuviéramos histórico
                'bolsa_ny'      => $bolsaNY,      // centavos USD/libra
                'tasa_cambio'   => $tasaCambio,   // COP/USD
                'fecha'         => $fechaPrecio ?? now()->toDateString(),
                'fuente'        => 'FNC · Precio interno de referencia (tiempo real)',
                'fuente_url'    => 'federaciondecafeteros.org',
                'en_vivo'       => true,
                'error'         => false,
            ];

        } catch (\Exception $e) {
            return $this->cafeFallback($e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────
    //  FALLBACK si falla el scraping
    // ──────────────────────────────────────────────────────
    private function cafeFallback(string $razon): array
    {
        return [
            'nombre'        => 'Café',
            'precio'        => 17008,        // $2.126.000 / 125 kg (último conocido)
            'precio_carga'  => 2126000,
            'unidad'        => 'COP/kg',
            'unidad_carga'  => 'COP/carga 125kg',
            'variacion'     => null,
            'bolsa_ny'      => 285.50,
            'tasa_cambio'   => 3704,
            'fecha'         => '2026-02-24',
            'fuente'        => 'FNC · Último precio conocido (sin conexión)',
            'fuente_url'    => 'federaciondecafeteros.org',
            'en_vivo'       => false,
            'error'         => false,
            'error_detalle' => $razon,
        ];
    }

    // ──────────────────────────────────────────────────────
    //  PRECIO ESTÁTICO (para los demás productos)
    // ──────────────────────────────────────────────────────
    private function precioEstatico(
        string $nombre,
        int    $precio,
        string $unidad,
        float  $variacion,
        string $fuente
    ): array {
        return [
            'nombre'    => $nombre,
            'precio'    => $precio,
            'unidad'    => $unidad,
            'variacion' => $variacion,
            'fecha'     => now()->toDateString(),
            'fuente'    => $fuente,
            'en_vivo'   => false,
            'error'     => false,
        ];
    }
}