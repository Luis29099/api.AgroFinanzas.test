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
    color: #1C2B1A;
    background: #ffffff;
  }

  /* ── Franja superior con la paleta completa ── */
  .top-stripe {
    width: 100%;
    height: 6px;
    background: #1C2B1A;
    font-size: 0;
    line-height: 0;
  }
  .top-stripe-inner {
    width: 100%;
    height: 3px;
    background: #C8A96E;
    font-size: 0;
    line-height: 0;
  }

  .page-inner { padding: 20px 28px 26px; }

  /* ══════════════════════════════════════
     HEADER
  ══════════════════════════════════════ */
  .header-wrap {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
  }
  .header-wrap td {
    padding: 0;
    border: none;
    background: transparent;
    vertical-align: top;
  }

  .brand-name {
    font-size: 22px;
    font-weight: 700;
    color: #1C2B1A;
    letter-spacing: -0.5px;
    line-height: 1;
  }
  .brand-name-accent { color: #4A7C3F; }
  .brand-sub {
    font-size: 6.5px;
    color: #A0522D;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-top: 3px;
    opacity: .6;
  }

  .report-h1 {
    font-size: 14px;
    font-weight: 700;
    color: #1C2B1A;
    margin-top: 10px;
    letter-spacing: -0.2px;
  }
  .report-h1-sub {
    font-size: 8px;
    color: #A0522D;
    margin-top: 2px;
    opacity: .65;
  }

  .meta-wrap { border-collapse: collapse; }
  .meta-wrap td {
    border: none;
    background: transparent;
    padding: 2px 0;
    font-size: 8px;
    line-height: 1.5;
    vertical-align: top;
  }
  .meta-key {
    color: #C8A96E;
    text-transform: uppercase;
    font-size: 6px;
    letter-spacing: 1px;
    text-align: right;
    padding-right: 8px;
    width: 52px;
  }
  .meta-v { color: #1C2B1A; font-weight: 600; text-align: right; }

  /* Regla dorada */
  .hr-gold {
    border: none;
    border-top: 1px solid #C8A96E;
    margin: 14px 0;
    opacity: .4;
  }
  .hr-light {
    border: none;
    border-top: 1px solid #E6D5AA;
    margin: 14px 0 16px;
  }

  /* ══════════════════════════════════════
     KPI CARDS
  ══════════════════════════════════════ */
  .eyebrow {
    font-size: 6px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2.5px;
    color: #A0522D;
    margin-bottom: 8px;
    opacity: .7;
  }

  .kpi-row {
    width: 100%;
    border-collapse: separate;
    border-spacing: 4px 0;
    margin-bottom: 14px;
  }
  .kpi-row > tbody > tr > td {
    border: none;
    padding: 0;
    vertical-align: top;
    width: 16.66%;
  }

  .kpi-card-tbl {
    width: 100%;
    border-collapse: collapse;
    background: #F5EDD6;
    border: 1px solid #E6D5AA;
    border-radius: 3px;
  }
  .kpi-card-tbl > tbody > tr > td {
    padding: 0;
    border: none;
    background: transparent;
    vertical-align: top;
  }

  .kpi-bar-cell {
    width: 4px;
    padding: 0 !important;
  }

  .kpi-content-cell { padding: 9px 9px 8px !important; }

  .kpi-lbl {
    font-size: 6px;
    font-weight: 700;
    color: #A0522D;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 4px;
    opacity: .75;
  }
  .kpi-amount {
    font-size: 12px;
    font-weight: 700;
    color: #1C2B1A;
    letter-spacing: -0.5px;
    line-height: 1.1;
  }
  .kpi-sub {
    font-size: 6px;
    color: #A0522D;
    margin-top: 3px;
    opacity: .5;
  }

  /* ══════════════════════════════════════
     SUMMARY BAR
  ══════════════════════════════════════ */
  .sum-wrap {
    background: #1C2B1A;
    border-radius: 3px;
    padding: 9px 13px;
    margin-bottom: 16px;
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
    font-size: 7.5px;
    color: #F5EDD6;
  }
  .dot {
    display: inline-block;
    width: 6px; height: 6px;
    border-radius: 3px;
    vertical-align: middle;
    margin-right: 3px;
  }
  .sum-lbl { opacity: .5; }
  .sum-val { font-weight: 700; margin-left: 2px; }
  .sum-sep { color: rgba(200,169,110,.35); padding: 0 7px; font-size: 10px; }

  /* ══════════════════════════════════════
     TABLA TRANSACCIONES
  ══════════════════════════════════════ */
  .tbl-ttl-row {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
  }
  .tbl-ttl-row td { border: none; background: transparent; padding: 0; vertical-align: middle; }

  .tbl-ttl {
    font-size: 8px;
    font-weight: 700;
    color: #6B3D14;
    text-transform: uppercase;
    letter-spacing: 1.5px;
  }
  .tbl-ttl-accent {
    display: inline-block;
    width: 3px; height: 10px;
    background: #4A7C3F;
    border-radius: 2px;
    vertical-align: middle;
    margin-right: 6px;
  }
  .tbl-cnt {
    display: inline-block;
    font-size: 7px;
    color: #A0522D;
    background: #F5EDD6;
    border: 1px solid #E6D5AA;
    padding: 2px 9px;
    border-radius: 20px;
  }

  table.txn {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #E6D5AA;
    border-radius: 3px;
  }
  table.txn thead tr { background: #1C2B1A; }
  table.txn th {
    padding: 8px 10px;
    text-align: left;
    font-size: 6.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #C8A96E;
    border-bottom: 2px solid #C8A96E;
    white-space: nowrap;
  }
  table.txn th.right { text-align: right; }

  table.txn td {
    padding: 7px 10px;
    font-size: 8.5px;
    color: #1C2B1A;
    border-bottom: 1px solid #F0E8D0;
    vertical-align: middle;
  }
  table.txn tbody tr.odd  td { background: #FDFAF4; }
  table.txn tbody tr.even td { background: #ffffff; }
  table.txn tbody tr:last-child td { border-bottom: none; }

  .td-date {
    font-family: 'DejaVu Sans Mono', monospace;
    font-size: 7.5px;
    color: #A0522D;
    white-space: nowrap;
    width: 54px;
    opacity: .75;
  }
  .td-amt {
    font-family: 'DejaVu Sans Mono', monospace;
    font-size: 9px;
    font-weight: 700;
    color: #1C2B1A;
    white-space: nowrap;
    text-align: right;
    width: 74px;
  }
  .td-cat  { width: 68px; }
  .td-det  { color: #6B3D14; font-size: 8px; opacity: .75; }
  .td-dsc  { color: #6B3D14; font-size: 8px; opacity: .6; font-style: italic; }

  /* Badges con paleta tierra */
  .bdg {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 6.5px;
    font-weight: 700;
    white-space: nowrap;
    letter-spacing: .3px;
  }
  /* Ingreso — pasto */
  .bdg-i { background: rgba(74,124,63,.12); color: #2d5a1b; border: 1px solid rgba(74,124,63,.3); }
  /* Gasto — tierra/rojo */
  .bdg-e { background: rgba(192,57,43,.09); color: #7B1A1A; border: 1px solid rgba(192,57,43,.25); }
  /* Inversión — cielo */
  .bdg-v { background: rgba(91,141,184,.12); color: #2a5a80; border: 1px solid rgba(91,141,184,.3); }
  /* Deuda — acento */
  .bdg-d { background: rgba(212,132,26,.12); color: #7A4800; border: 1px solid rgba(212,132,26,.3); }
  /* Inventario — noche */
  .bdg-n { background: rgba(28,43,26,.08); color: #1C2B1A; border: 1px solid rgba(28,43,26,.2); }
  /* Costo — barro */
  .bdg-c { background: rgba(160,82,45,.1); color: #6B3D14; border: 1px solid rgba(160,82,45,.25); }

  .chip {
    display: inline-block;
    padding: 1.5px 6px;
    border-radius: 2px;
    font-size: 7px;
    background: #F5EDD6;
    color: #A0522D;
    border: 1px solid #E6D5AA;
  }
  .prog { color: #4A7C3F; font-weight: 700; font-size: 7.5px; }

  .empty-row td {
    text-align: center;
    padding: 24px;
    color: #C8A96E;
    font-size: 9px;
    opacity: .6;
  }

  /* ══════════════════════════════════════
     FOOTER
  ══════════════════════════════════════ */
  .footer-hr {
    border: none;
    border-top: 1px solid #E6D5AA;
    margin: 16px 0 10px;
  }
  .footer-wrap { width: 100%; border-collapse: collapse; }
  .footer-wrap td {
    border: none;
    background: transparent;
    padding: 0;
    vertical-align: middle;
  }
  .f-brand { font-size: 9px; font-weight: 700; color: #1C2B1A; }
  .f-brand-accent { color: #4A7C3F; }
  .f-sub { font-size: 6.5px; color: #A0522D; margin-top: 2px; opacity: .55; }
  .f-right { text-align: right; font-size: 7px; color: #A0522D; opacity: .5; line-height: 1.7; }
</style>
</head>
<body>

<div class="top-stripe">&nbsp;</div>
<div class="top-stripe-inner">&nbsp;</div>

<div class="page-inner">

{{-- ── HEADER ── --}}
<table class="header-wrap">
  <tr>
    <td style="width:55%">
      <div class="brand-name">Agro<span class="brand-name-accent">Finanzas</span></div>
      <div class="brand-sub">Sistema de Gestión Agrícola · Colombia</div>
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

<hr class="hr-gold">

{{-- ── KPI CARDS ── --}}
@php
  $fmt = fn($n) => '$' . number_format(abs($n), 0, ',', '.');
  $balColor = $totals['balance'] >= 0 ? '#2d5a1b' : '#7B1A1A';
  $kpis = [
    ['lbl'=>'Ingresos',    'val'=>$fmt($totals['totalIncome']),     'hint'=>'Total acumulado', 'color'=>'#4A7C3F'],
    ['lbl'=>'Gastos',      'val'=>$fmt($totals['totalExpense']),    'hint'=>'Total acumulado', 'color'=>'#c0392b'],
    ['lbl'=>'Balance',     'val'=>$fmt($totals['balance']),         'hint'=>$totals['balance']>=0?'Positivo':'Negativo', 'color'=>'#1C2B1A', 'valColor'=>$balColor],
    ['lbl'=>'Inversiones', 'val'=>$fmt($totals['totalInvestment']), 'hint'=>'Total acumulado', 'color'=>'#5B8DB8'],
    ['lbl'=>'Deudas',      'val'=>$fmt($totals['totalDebt']),       'hint'=>'Total acumulado', 'color'=>'#D4841A'],
    ['lbl'=>'Costos',      'val'=>$fmt($totals['totalCosts']),      'hint'=>'Total acumulado', 'color'=>'#A0522D'],
  ];
@endphp

<div class="eyebrow">Resumen del período</div>

<table class="kpi-row">
  <tr>
    @foreach($kpis as $k)
    <td>
      <table class="kpi-card-tbl">
        <tr>
          <td class="kpi-bar-cell" style="background:{{ $k['color'] }}">&nbsp;</td>
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

{{-- ── SUMMARY BAR (dark) ── --}}
<div class="sum-wrap">
  <table class="sum-tbl">
    <tr>
      <td><span class="dot" style="background:#4A7C3F"></span><span class="sum-lbl">Ingresos:</span><span class="sum-val">{{ $fmt($totals['totalIncome']) }}</span></td>
      <td style="width:1px"><span class="sum-sep">|</span></td>
      <td><span class="dot" style="background:#c0392b"></span><span class="sum-lbl">Gastos:</span><span class="sum-val">{{ $fmt($totals['totalExpense']) }}</span></td>
      <td style="width:1px"><span class="sum-sep">|</span></td>
      <td><span class="dot" style="background:#C8A96E"></span><span class="sum-lbl">Balance:</span><span class="sum-val" style="color:{{ $totals['balance']>=0?'#7AAF5A':'#e8735a' }}">{{ $fmt($totals['balance']) }}</span></td>
      <td style="width:1px"><span class="sum-sep">|</span></td>
      <td><span class="dot" style="background:#5B8DB8"></span><span class="sum-lbl">Inversiones:</span><span class="sum-val">{{ $fmt($totals['totalInvestment']) }}</span></td>
      <td style="width:1px"><span class="sum-sep">|</span></td>
      <td><span class="dot" style="background:#D4841A"></span><span class="sum-lbl">Deudas:</span><span class="sum-val">{{ $fmt($totals['totalDebt']) }}</span></td>
      <td style="width:1px"><span class="sum-sep">|</span></td>
      <td><span class="dot" style="background:#A0522D"></span><span class="sum-lbl">Costos:</span><span class="sum-val">{{ $fmt($totals['totalCosts']) }}</span></td>
    </tr>
  </table>
</div>

{{-- ── TABLA ── --}}
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
        'income'     => ['cls'=>'bdg-i', 'lbl'=>'Ingreso'],
        'expense'    => ['cls'=>'bdg-e', 'lbl'=>'Gasto'],
        'investment' => ['cls'=>'bdg-v', 'lbl'=>'Inversión'],
        'debt'       => ['cls'=>'bdg-d', 'lbl'=>'Deuda'],
        'inventory'  => ['cls'=>'bdg-n', 'lbl'=>'Inventario'],
        'costs'      => ['cls'=>'bdg-c', 'lbl'=>'Costo'],
      ];
      $b = $badges[$f->type] ?? ['cls'=>'bdg-i','lbl'=>ucfirst($f->type)];
    @endphp
    <tr class="{{ $rowClass }}">
      <td class="td-date">{{ $f->date_formatted ?? \Carbon\Carbon::parse($f->date)->format('d/m/Y') }}</td>
      <td><span class="bdg {{ $b['cls'] }}">{{ $b['lbl'] }}</span></td>
      <td class="td-amt">{{ $fmt($f->amount) }}</td>
      <td class="td-cat">
        @if($f->category)<span class="chip">{{ $f->category }}</span>@else<span style="color:#E6D5AA">—</span>@endif
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
          <span style="color:#E6D5AA">—</span>
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
      <div class="f-brand">Agro<span class="f-brand-accent">Finanzas</span></div>
      <div class="f-sub">Historial Financiero · Sistema de Gestión Agrícola</div>
    </td>
    <td class="f-right">
      Generado el {{ $generatedAt }}<br>
      {{ $finances->count() }} registros exportados
    </td>
  </tr>
</table>

</div>
</body>
</html>