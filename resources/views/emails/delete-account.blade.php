<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1a0d0d, #3a1010); padding: 36px; text-align: center; }
        .header h1 { color: #e74c3c; font-size: 1.5rem; margin: 12px 0 4px; }
        .header p { color: rgba(255,255,255,0.4); font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; margin: 0; }
        .body { padding: 40px 36px; }
        .body p { color: #555; font-size: 0.92rem; line-height: 1.7; margin: 0 0 16px; }
        .warning-box { background: #fff5f5; border: 1px solid #fca5a5; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; }
        .warning-box p { color: #b91c1c; font-size: 0.85rem; margin: 0; }
        .code-box { background: #fff5f5; border: 2px dashed #e74c3c; border-radius: 12px; text-align: center; padding: 28px; margin: 24px 0; }
        .code-box span { font-size: 2.8rem; font-weight: 700; letter-spacing: 10px; color: #b91c1c; font-family: 'Courier New', monospace; }
        .code-box small { display: block; color: #999; font-size: 0.75rem; margin-top: 8px; }
        .footer { background: #f9f9f9; border-top: 1px solid #eee; padding: 20px 36px; text-align: center; }
        .footer p { color: #bbb; font-size: 0.72rem; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ AgroFinanzas</h1>
            <p>Confirmación de eliminación</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $name }}</strong></p>
            <p>Recibimos una solicitud para <strong>eliminar permanentemente</strong> tu cuenta de AgroFinanzas.</p>

            <div class="warning-box">
                <p>⚠️ Esta acción es <strong>irreversible</strong>. Se eliminarán todos tus datos financieros, ganado y registros.</p>
            </div>

            <p>Si confirmas, ingresa este código en la aplicación:</p>

            <div class="code-box">
                <span>{{ $code }}</span>
                <small>Este código expira en <strong>15 minutos</strong></small>
            </div>

            <p>Si <strong>no solicitaste</strong> esto, ignora este correo. Tu cuenta está segura.</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} AgroFinanzas — Decisiones inteligentes para el campo</p>
        </div>
    </div>
</body>
</html>