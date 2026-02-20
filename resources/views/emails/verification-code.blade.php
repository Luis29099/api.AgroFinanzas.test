<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de verificación</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0f0f0f;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #e0e0e0;
        }
        .wrapper {
            max-width: 520px;
            margin: 40px auto;
            background: #1a1a1a;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(138, 201, 38, 0.2);
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
        }
        .header {
            background: linear-gradient(135deg, #1e3a0f, #2d5a1b);
            padding: 36px 32px;
            text-align: center;
            border-bottom: 2px solid #8ac926;
        }
        .logo-text {
            font-size: 1.6rem;
            font-weight: 800;
            color: #8ac926;
            letter-spacing: 1px;
        }
        .logo-sub {
            color: #aaa;
            font-size: 0.82rem;
            margin-top: 4px;
        }
        .body {
            padding: 36px 32px;
        }
        .greeting {
            font-size: 1rem;
            color: #ccc;
            margin-bottom: 16px;
        }
        .greeting span { color: #8ac926; font-weight: 700; }
        .intro {
            color: #999;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .code-label {
            text-align: center;
            font-size: 0.78rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }
        .code-box {
            background: #111;
            border: 2px solid #8ac926;
            border-radius: 12px;
            padding: 22px;
            text-align: center;
            margin-bottom: 24px;
            box-shadow: 0 0 20px rgba(138, 201, 38, 0.15);
        }
        .code {
            font-size: 3rem;
            font-weight: 800;
            color: #8ac926;
            letter-spacing: 12px;
            text-shadow: 0 0 20px rgba(138, 201, 38, 0.4);
            font-family: 'Courier New', monospace;
        }
        .expiry {
            text-align: center;
            color: #e67e22;
            font-size: 0.82rem;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .warning {
            background: rgba(231, 76, 60, 0.08);
            border: 1px solid rgba(231, 76, 60, 0.2);
            border-radius: 8px;
            padding: 12px 16px;
            color: #e74c3c;
            font-size: 0.8rem;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .footer {
            border-top: 1px solid #2a2a2a;
            padding: 20px 32px;
            text-align: center;
            color: #444;
            font-size: 0.75rem;
            line-height: 1.6;
        }
        .footer a { color: #8ac926; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo-text">🌱 AgroFinanzas</div>
            <div class="logo-sub">Gestión financiera para el campo colombiano</div>
        </div>

        <div class="body">
            <p class="greeting">Hola, <span>{{ $userName }}</span> 👋</p>
            <p class="intro">
                Gracias por registrarte en AgroFinanzas. Para activar tu cuenta,
                ingresa el siguiente código de verificación en la pantalla que te
                aparece en la aplicación:
            </p>

            <p class="code-label">Tu código de verificación</p>
            <div class="code-box">
                <div class="code">{{ $code }}</div>
            </div>

            <div class="expiry">
                ⏱ Este código expira en <strong>15 minutos</strong>
            </div>

            <div class="warning">
                <strong>⚠ Importante:</strong> Si no creaste una cuenta en AgroFinanzas,
                ignora este correo. Nadie más puede usar este código.
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} AgroFinanzas · Todos los derechos reservados<br>
            Este es un correo automático, por favor no respondas a este mensaje.
        </div>
    </div>
</body>
</html>