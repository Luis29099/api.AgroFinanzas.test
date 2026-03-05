<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0d1a0d, #1a3a1a); padding: 36px; text-align: center; }
        .header img { width: 60px; height: 60px; border-radius: 50%; border: 2px solid #8ac926; }
        .header h1 { color: #8ac926; font-size: 1.5rem; margin: 12px 0 4px; }
        .header p { color: rgba(255,255,255,0.4); font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; margin: 0; }
        .body { padding: 40px 36px; }
        .body p { color: #555; font-size: 0.92rem; line-height: 1.7; margin: 0 0 16px; }
        .code-box { background: #f8fdf0; border: 2px dashed #8ac926; border-radius: 12px; text-align: center; padding: 28px; margin: 28px 0; }
        .code-box span { font-size: 2.8rem; font-weight: 700; letter-spacing: 10px; color: #3a6b00; font-family: 'Courier New', monospace; }
        .code-box small { display: block; color: #999; font-size: 0.75rem; margin-top: 8px; }
        .footer { background: #f9f9f9; border-top: 1px solid #eee; padding: 20px 36px; text-align: center; }
        .footer p { color: #bbb; font-size: 0.72rem; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AgroFinanzas</h1>
            <p>Recuperación de contraseña</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $name }}</strong> 👋</p>
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta. Usa el siguiente código:</p>
            <div class="code-box">
                <span>{{ $code }}</span>
                <small>Este código expira en <strong>15 minutos</strong></small>
            </div>
            <p>Si no solicitaste este cambio, ignora este correo. Tu contraseña no será modificada.</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} AgroFinanzas — Decisiones inteligentes para el campo</p>
        </div>
    </div>
</body>
</html>