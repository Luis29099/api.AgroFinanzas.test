<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmación de eliminación — AgroFinanzas</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: #EFE3C4;
    font-family: 'Source Sans 3', 'Segoe UI', sans-serif;
    color: #1C2B1A;
    padding: 40px 16px 60px;
  }

  .wrap {
    max-width: 520px;
    margin: 0 auto;
    background: #fff;
    border-radius: 4px;
    overflow: hidden;
    box-shadow:
      0 2px 0 #E6D5AA,
      0 8px 32px rgba(107,61,20,.14),
      0 24px 56px rgba(107,61,20,.08);
  }

  /* Franja: roja para indicar acción peligrosa */
  .top-stripe {
    height: 4px;
    background: linear-gradient(90deg, #6B3D14 0%, #c0392b 40%, #A0522D 70%, #6B3D14 100%);
  }

  /* ── Header oscuro con tono de alerta ── */
  .header {
    background: #1C2B1A;
    padding: 36px 36px 32px;
    position: relative;
    overflow: hidden;
  }

  .header::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
      repeating-linear-gradient(
        45deg,
        transparent,
        transparent 18px,
        rgba(192,57,43,.04) 18px,
        rgba(192,57,43,.04) 19px
      );
  }

  .header-inner { position: relative; }

  .brand-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
  }

  .brand-icon {
    width: 40px; height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    display: block;
    border: 2px solid rgba(192,57,43,.35);
    box-shadow: 0 2px 10px rgba(28,43,26,.35);
  }

  .brand-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: #C8A96E;
    letter-spacing: .5px;
  }
  .brand-sub {
    font-size: .68rem;
    color: rgba(200,169,110,.45);
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-top: 1px;
  }

  .header-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.45rem;
    font-weight: 900;
    color: #F5EDD6;
    line-height: 1.2;
    margin-bottom: 6px;
  }

  .header-sub {
    font-size: .78rem;
    color: rgba(245,237,214,.35);
    text-transform: uppercase;
    letter-spacing: 2.5px;
    font-weight: 600;
  }

  .danger-rule {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(192,57,43,.4), transparent);
    margin: 28px 0 0;
  }

  /* ── Body ── */
  .body {
    padding: 36px 36px 28px;
    background: #fff;
  }

  .greeting {
    font-size: 1rem;
    color: #1C2B1A;
    margin-bottom: 12px;
    font-weight: 600;
  }
  .greeting span { color: #6B3D14; }

  .intro {
    font-size: .88rem;
    color: #6B3D14;
    line-height: 1.7;
    margin-bottom: 20px;
    opacity: .75;
  }

  /* Caja de advertencia */
  .warning-box {
    background: rgba(192,57,43,.05);
    border: 1px solid rgba(192,57,43,.25);
    border-left: 3px solid #c0392b;
    border-radius: 3px;
    padding: 14px 16px;
    margin-bottom: 24px;
    display: flex;
    gap: 10px;
    align-items: flex-start;
  }
  .warning-icon {
    font-size: 1rem;
    flex-shrink: 0;
    margin-top: 1px;
  }
  .warning-text {
    font-size: .82rem;
    color: #8B1A1A;
    line-height: 1.6;
    font-weight: 600;
  }
  .warning-text ul {
    margin-top: 6px;
    padding-left: 16px;
    font-weight: 400;
    opacity: .85;
  }
  .warning-text li { margin-bottom: 2px; }

  .code-prompt {
    font-size: .82rem;
    color: rgba(107,61,20,.65);
    margin-bottom: 14px;
  }

  /* Código — tono tierra/rojo */
  .code-label {
    font-size: .6rem;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #A0522D;
    opacity: .6;
    text-align: center;
    margin-bottom: 10px;
  }

  .code-wrap {
    background: #FDF5F0;
    border: 1px solid rgba(192,57,43,.25);
    border-radius: 3px;
    padding: 28px 20px 22px;
    text-align: center;
    margin-bottom: 6px;
    position: relative;
  }

  .code-wrap::before,
  .code-wrap::after {
    content: '';
    position: absolute;
    width: 12px; height: 12px;
    border-color: rgba(192,57,43,.35);
    border-style: solid;
  }
  .code-wrap::before { top: 8px; left: 8px;    border-width: 1px 0 0 1px; }
  .code-wrap::after  { bottom: 8px; right: 8px; border-width: 0 1px 1px 0; }

  .code {
    font-family: 'Courier New', 'DejaVu Sans Mono', monospace;
    font-size: 2.8rem;
    font-weight: 700;
    letter-spacing: 14px;
    text-indent: 14px;
    color: #6B3D14;
    line-height: 1;
    display: block;
    margin-bottom: 12px;
  }

  .code-expiry {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .72rem;
    color: #c0392b;
    background: rgba(192,57,43,.07);
    border: 1px solid rgba(192,57,43,.2);
    border-radius: 2px;
    padding: 5px 12px;
    font-weight: 600;
  }

  .divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #E6D5AA, transparent);
    margin: 24px 0;
  }

  .note {
    font-size: .82rem;
    color: rgba(107,61,20,.55);
    line-height: 1.65;
    font-style: italic;
  }

  /* ── Footer ── */
  .footer {
    background: #F5EDD6;
    border-top: 1px solid #E6D5AA;
    padding: 18px 36px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .footer-brand {
    font-family: 'Playfair Display', serif;
    font-size: .82rem;
    font-weight: 700;
    color: #6B3D14;
  }
  .footer-brand span { color: #4A7C3F; }

  .footer-copy {
    font-size: .68rem;
    color: #A0522D;
    opacity: .5;
    text-align: right;
    line-height: 1.5;
  }
</style>
</head>
<body>
  <div class="wrap">
    <div class="top-stripe"></div>

    <div class="header">
      <div class="header-inner">
        <div class="brand-row">
          <img src="{{ asset('images/logonv.png') }}" alt="AgroFinanzas" class="brand-icon">
          <div>
            <div class="brand-name">AgroFinanzas</div>
            <div class="brand-sub">Gestión financiera del campo</div>
          </div>
        </div>
        <div class="header-title">Eliminación de cuenta</div>
        <div class="header-sub">Confirmación requerida</div>
        <div class="danger-rule"></div>
      </div>
    </div>

    <div class="body">
      <p class="greeting">Hola, <span>{{ $name }}</span></p>
      <p class="intro">
        Recibimos una solicitud para <strong>eliminar permanentemente</strong>
        tu cuenta de AgroFinanzas. Esta acción no puede deshacerse.
      </p>

      <div class="warning-box">
        <div class="warning-icon">⚠️</div>
        <div class="warning-text">
          Al confirmar, se eliminarán definitivamente:
          <ul>
            <li>Todos tus registros financieros e historial</li>
            <li>Tu hato ganadero y animales registrados</li>
            <li>Cultivos, inventarios y costos de producción</li>
            <li>Tu cuenta y datos personales</li>
          </ul>
        </div>
      </div>

      <p class="code-prompt">Si deseas continuar, ingresa este código en la aplicación:</p>

      <div class="code-label">Código de confirmación</div>
      <div class="code-wrap">
        <span class="code">{{ $code }}</span>
        <span class="code-expiry">
          ⏱ Expira en <strong>15 minutos</strong>
        </span>
      </div>

      <div class="divider"></div>
      <p class="note">
        Si <strong>no solicitaste</strong> eliminar tu cuenta, ignora este correo con total seguridad.
        Tu cuenta permanece activa y protegida.
      </p>
    </div>

    <div class="footer">
      <div class="footer-brand">Agro<span>Finanzas</span></div>
      <div class="footer-copy">
        © {{ date('Y') }} AgroFinanzas<br>
        Correo automático · No responder
      </div>
    </div>
  </div>
</body>
</html>