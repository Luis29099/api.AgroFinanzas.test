<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial Financiero — AgroFinanzas</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 10px;
        color: #1c1c1c;
        background: #ffffff;
    }

    /* ── Top accent bar ── */
    .top-bar {
        width: 100%;
        height: 5px;
        background: #16a34a;
        font-size: 0;
        line-height: 0;
    }

    .page-inner {
        padding: 22px 30px 28px;
    }

    /* ══════════════════════════════════════════
       HEADER
    ══════════════════════════════════════════ */
    .header-wrap {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }
    .header-wrap td {
        padding: 0;
        border: none;
        background: transparent;
        vertical-align: top;
    }
    .brand-name {
        font-size: 21px;
        font-weight: 700;
        color: #111111;
        letter-spacing: -0.5px;
    }
    .brand-name span { color: #16a34a; }
    .brand-sub {
        font-size: 7px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-top: 2px;
    }
    .report-h1 {
        font-size: 15px;
        font-weight: 700;
        color: #111;
        margin-top: 9px;
        letter-spacing: -0.2px;
    }
    .report-h1-sub {
        font-size: 8.5px;
        color: #777;
        margin-top: 2px;
    }

    .meta-wrap {
        border-collapse: collapse;
        width: 100%;
    }
    .meta-wrap td {
        border: none;
        background: transparent;
        padding: 2px 0;
        font-size: 8px;
        line-height: 1.4;
        vertical-align: top;
    }
    .meta-key {
        color: #bbb;
        text-transform: uppercase;
        font-size: 6.5px;
        letter-spacing: 0.6px;
        text-align: right;
        padding-right: 8px;
        width: 55px;
    }
    .meta-v {
        color: #333;
        font-weight: 600;
        text-align: right;
    }

    .hr {
        border: none;
        border-top: 1px solid #e8e8e8;
        margin: 14px 0 18px;
    }

    /* ══════════════════════════════════════════
       KPI CARDS
       Each card = nested 2-col table:
       left col = color bar (4px), right col = content
    ══════════════════════════════════════════ */
    .eyebrow {
        font-size: 6.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #bbb;
        margin-bottom: 8px;
    }

    /* Outer table: 6 equal cells */
    .kpi-row {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
    }
    .kpi-row > tbody > tr > td {
        border: none;
        padding: 0 4px 0 0;
        vertical-align: top;
        width: 16.66%;
    }
    .kpi-row > tbody > tr > td:last-child {
        padding-right: 0;
    }

    /* Card shell */
    .kpi-card-tbl {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        background: #f9fafb;
    }
    .kpi-card-tbl > tbody > tr > td {
        padding: 0;
        border: none;
        background: transparent;
        vertical-align: top;
    }

    /* Left color bar cell */
    .kpi-bar-cell {
        width: 4px;
        padding: 0 !important;
        border-radius: 6px 0 0 6px;
    }

    /* Content cell */
    .kpi-content-cell {
        padding: 10px 10px 8px !important;
    }

    .kpi-lbl {
        font-size: 6.5px;
        font-weight: 700;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 4px;
    }
    .kpi-amount {
        font-size: 13px;
        font-weight: 700;
        color: #111;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }
    .kpi-sub {
        font-size: 6.5px;
        color: #ccc;
        margin-top: 3px;
    }

    /* ══════════════════════════════════════════
       SUMMARY BAR — simple table row
    ══════════════════════════════════════════ */
    .sum-wrap {
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        background: #f9fafb;
        padding: 9px 13px;
        margin-bottom: 18px;
    }
    .sum-tbl {
        width: 100%;
        border-collapse: collapse;
    }
    .sum-tbl td {
        border: none;
        background: transparent;
        padding: 0;
        vertical-align: middle;
        white-space: nowrap;
        font-size: 8px;
    }
    .dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 3px;
        vertical-align: middle;
        margin-right: 3px;
    }
    .sum-lbl { color: #888; }
    .sum-val { font-weight: 700; color: #111; margin-left: 2px; }
    .sum-sep {
        color: #ddd;
        padding: 0 8px;
        font-size: 10px;
    }

    /* ══════════════════════════════════════════
       TRANSACTIONS TABLE
    ══════════════════════════════════════════ */
    .tbl-ttl-row {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .tbl-ttl-row td { border: none; background: transparent; padding: 0; vertical-align: middle; }
    .tbl-ttl {
        font-size: 8.5px;
        font-weight: 700;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }
    .tbl-ttl-accent {
        display: inline-block;
        width: 3px;
        height: 10px;
        background: #16a34a;
        border-radius: 2px;
        vertical-align: middle;
        margin-right: 6px;
    }
    .tbl-cnt {
        display: inline-block;
        font-size: 7.5px;
        color: #999;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        padding: 2px 9px;
        border-radius: 20px;
    }

    table.txn {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e8e8e8;
    }
    table.txn thead tr {
        background: #f3f4f6;
    }
    table.txn th {
        padding: 8px 10px;
        text-align: left;
        font-size: 7px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #777;
        border-bottom: 2px solid #e0e0e0;
        white-space: nowrap;
    }
    table.txn th.right { text-align: right; }
    table.txn td {
        padding: 8px 10px;
        font-size: 8.5px;
        color: #444;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    table.txn tbody tr.even td { background: #fafafa; }
    table.txn tbody tr.odd  td { background: #ffffff; }
    table.txn tbody tr:last-child td { border-bottom: none; }

    .td-date { font-family: 'DejaVu Sans Mono', monospace; font-size: 7.5px; color: #888; white-space: nowrap; width: 56px; }
    .td-amt  { font-family: 'DejaVu Sans Mono', monospace; font-size: 9px; font-weight: 700; color: #111; white-space: nowrap; text-align: right; width: 76px; }
    .td-cat  { width: 70px; }
    .td-det  { color: #777; font-size: 8px; }
    .td-dsc  { color: #777; font-size: 8px; }

    /* Badges */
    .bdg {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 7px;
        font-weight: 700;
        white-space: nowrap;
    }
    .bdg-i { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .bdg-e { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .bdg-v { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .bdg-d { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .bdg-n { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
    .bdg-c { background: #cffafe; color: #155e75; border: 1px solid #a5f3fc; }

    .chip {
        display: inline-block;
        padding: 1.5px 6px;
        border-radius: 4px;
        font-size: 7.5px;
        background: #f3f4f6;
        color: #777;
        border: 1px solid #e5e7eb;
    }
    .prog { color: #16a34a; font-weight: 700; font-size: 7.5px; }

    /* empty */
    .empty-row td { text-align: center; padding: 24px; color: #ccc; font-size: 9px; }

    /* ══════════════════════════════════════════
       FOOTER
    ══════════════════════════════════════════ */
    .footer-hr { border: none; border-top: 1px solid #f0f0f0; margin: 18px 0 10px; }
    .footer-wrap { width: 100%; border-collapse: collapse; }
    .footer-wrap td { border: none; background: transparent; padding: 0; vertical-align: middle; }
    .f-brand { font-size: 9px; font-weight: 700; color: #333; }
    .f-brand span { color: #16a34a; }
    .f-sub { font-size: 7px; color: #bbb; margin-top: 2px; }
    .f-right { text-align: right; font-size: 7.5px; color: #bbb; line-height: 1.7; }
</style>
</head>
<body>

{{-- Top bar --}}
<div class="top-bar">&nbsp;</div>

<div class="page-inner">

{{-- ── HEADER ── --}}
<table class="header-wrap">
    <tr>
        <td style="width:55%">
            <div class="brand-name">Agro<span>Finanzas</span></div>
            <div class="brand-sub">Sistema de Gestión Agrícola</div>
            <div class="report-h1">Historial Financiero</div>
            <div class="report-h1-sub">Resumen detallado de movimientos y balance general</div>
        </td>
        <td style="width:45%;vertical-align:top">
            <table class="meta-wrap" style="float:right">
                <tr>
                    <td class="meta-key">Cliente</td>
                    <td class="meta-v">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td class="meta-key">Generado</td>
                    <td class="meta-v">{{ $generatedAt }}</td>
                </tr>
                @if($dateFrom || $dateTo)
                <tr>
                    <td class="meta-key">Período</td>
                    <td class="meta-v">{{ $dateFrom ?: '—' }} → {{ $dateTo ?: '—' }}</td>
                </tr>
                @endif
                <tr>
                    <td class="meta-key">Filtro</td>
                    <td class="meta-v">{{ ucfirst($filter) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<hr class="hr">

{{-- ── KPI CARDS ── --}}
@php
    $fmt = fn($n) => '$' . number_format(abs($n), 0, ',', '.');
    $balColor = $totals['balance'] >= 0 ? '#166534' : '#991b1b';
    $kpis = [
        ['lbl'=>'Ingresos',    'val'=>$fmt($totals['totalIncome']),     'hint'=>'Total acumulado', 'color'=>'#22c55e'],
        ['lbl'=>'Gastos',      'val'=>$fmt($totals['totalExpense']),    'hint'=>'Total acumulado', 'color'=>'#ef4444'],
        ['lbl'=>'Balance',     'val'=>$fmt($totals['balance']),         'hint'=>$totals['balance']>=0?'Positivo':'Negativo', 'color'=>'#16a34a', 'valColor'=>$balColor],
        ['lbl'=>'Inversiones', 'val'=>$fmt($totals['totalInvestment']), 'hint'=>'Total acumulado', 'color'=>'#3b82f6'],
        ['lbl'=>'Deudas',      'val'=>$fmt($totals['totalDebt']),       'hint'=>'Total acumulado', 'color'=>'#f59e0b'],
        ['lbl'=>'Costos',      'val'=>$fmt($totals['totalCosts']),      'hint'=>'Total acumulado', 'color'=>'#06b6d4'],
    ];
@endphp

<div class="eyebrow">Resumen del período</div>

<table class="kpi-row">
    <tr>
        @foreach($kpis as $k)
        <td>
            {{-- Card: nested table — bar col + content col --}}
            <table class="kpi-card-tbl">
                <tr>
                    <td class="kpi-bar-cell" style="background:{{ $k['color'] }};width:4px">&nbsp;</td>
                    <td class="kpi-content-cell">
                        <div class="kpi-lbl">{{ $k['lbl'] }}</div>
                        <div class="kpi-amount" style="{{ isset($k['valColor']) ? 'color:'.$k['valColor'] : '' }}">{{ $k['val'] }}</div>
                        <div class="kpi-sub">{{ $k['hint'] }}</div>
                    </td>
                </tr>
            </table>
        </td>
        @endforeach
    </tr>
</table>

{{-- ── SUMMARY BAR ── --}}
<div class="sum-wrap">
    <table class="sum-tbl">
        <tr>
            <td><span class="dot" style="background:#22c55e"></span><span class="sum-lbl">Ingresos:</span><span class="sum-val">{{ $fmt($totals['totalIncome']) }}</span></td>
            <td style="width:1px"><span class="sum-sep">|</span></td>
            <td><span class="dot" style="background:#ef4444"></span><span class="sum-lbl">Gastos:</span><span class="sum-val">{{ $fmt($totals['totalExpense']) }}</span></td>
            <td style="width:1px"><span class="sum-sep">|</span></td>
            <td><span class="dot" style="background:#16a34a"></span><span class="sum-lbl">Balance:</span><span class="sum-val" style="color:{{ $balColor }}">{{ $fmt($totals['balance']) }}</span></td>
            <td style="width:1px"><span class="sum-sep">|</span></td>
            <td><span class="dot" style="background:#3b82f6"></span><span class="sum-lbl">Inversiones:</span><span class="sum-val">{{ $fmt($totals['totalInvestment']) }}</span></td>
            <td style="width:1px"><span class="sum-sep">|</span></td>
            <td><span class="dot" style="background:#f59e0b"></span><span class="sum-lbl">Deudas:</span><span class="sum-val">{{ $fmt($totals['totalDebt']) }}</span></td>
            <td style="width:1px"><span class="sum-sep">|</span></td>
            <td><span class="dot" style="background:#06b6d4"></span><span class="sum-lbl">Costos:</span><span class="sum-val">{{ $fmt($totals['totalCosts']) }}</span></td>
        </tr>
    </table>
</div>

{{-- ── TRANSACTIONS TABLE ── --}}
<table class="tbl-ttl-row">
    <tr>
        <td><span class="tbl-ttl"><span class="tbl-ttl-accent"></span>Transacciones</span></td>
        <td style="text-align:right"><span class="tbl-cnt">{{ $finances->count() }} registros</span></td>
    </tr>
</table>

<table class="txn">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th class="right">Monto</th>
            <th>Categoría</th>
            <th>Detalles</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($finances as $i => $f)
        @php
            $rowClass = $i % 2 === 0 ? 'odd' : 'even';
            $badges = [
                'income'     => ['cls'=>'bdg-i','lbl'=>'Ingreso'],
                'expense'    => ['cls'=>'bdg-e','lbl'=>'Gasto'],
                'investment' => ['cls'=>'bdg-v','lbl'=>'Inversión'],
                'debt'       => ['cls'=>'bdg-d','lbl'=>'Deuda'],
                'inventory'  => ['cls'=>'bdg-n','lbl'=>'Inventario'],
                'costs'      => ['cls'=>'bdg-c','lbl'=>'Costo'],
            ];
            $b = $badges[$f->type] ?? ['cls'=>'bdg-i','lbl'=>ucfirst($f->type)];
        @endphp
        <tr class="{{ $rowClass }}">
            <td class="td-date">{{ $f->date_formatted ?? \Carbon\Carbon::parse($f->date)->format('d/m/Y') }}</td>
            <td><span class="bdg {{ $b['cls'] }}">{{ $b['lbl'] }}</span></td>
            <td class="td-amt">{{ $fmt($f->amount) }}</td>
            <td class="td-cat">
                @if($f->category)<span class="chip">{{ $f->category }}</span>@else<span style="color:#ddd">—</span>@endif
            </td>
            <td class="td-det">
                @if($f->type==='debt' && $f->creditor)
                    {{ $f->creditor }}@if($f->paid_installments!==null && $f->installments)<span class="prog"> · {{ $f->paid_installments }}/{{ $f->installments }}</span>@endif
                @elseif($f->type==='investment' && $f->asset_name)
                    {{ $f->asset_name }}
                @elseif($f->type==='inventory' && $f->product_name)
                    {{ $f->product_name }}{{ $f->quantity ? ' · '.$f->quantity.' '.$f->unit : '' }}
                @elseif($f->type==='costs' && $f->crop_name)
                    {{ $f->crop_name }}{{ $f->area ? ' · '.$f->area.' ha' : '' }}
                @else
                    <span style="color:#ddd">—</span>
                @endif
            </td>
            <td class="td-dsc">{{ $f->description ?? '—' }}</td>
        </tr>
        @empty
        <tr class="empty-row">
            <td colspan="6">Sin registros para este período</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- ── FOOTER ── --}}
<hr class="footer-hr">
<table class="footer-wrap">
    <tr>
        <td>
            <div class="f-brand">Agro<span>Finanzas</span></div>
            <div class="f-sub">Historial Financiero · Sistema de Gestión Agrícola</div>
        </td>
        <td class="f-right">
            Generado el {{ $generatedAt }}<br>
            {{ $finances->count() }} registros exportados
        </td>
    </tr>
</table>

</div>{{-- /page-inner --}}
</body>
</html>