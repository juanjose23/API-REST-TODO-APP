<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Verificar correo electrónico</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f9fafb;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .email-header {
            background-color: #4f46e5;
            padding: 24px;
            text-align: center;
        }

        .logo {
            height: 40px;
            margin-bottom: 0;
        }

        .email-body {
            padding: 32px 24px;
        }

        h1 {
            color: #111827;
            font-size: 24px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 16px;
        }

        p {
            color: #4b5563;
            font-size: 16px;
            line-height: 24px;
            margin: 16px 0;
        }

        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            font-size: 16px;
            border-radius: 6px;
            padding: 12px 24px;
            margin: 24px 0;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.15s ease;
        }

        .button:hover {
            background-color: #4338ca;
        }

        .email-footer {
            background-color: #f3f4f6;
            padding: 24px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }

        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 24px 0;
        }

        .security-notice {
            font-size: 14px;
            color: #6b7280;
            padding: 16px 0;
            border-top: 1px solid #e5e7eb;
            margin-top: 24px;
        }

        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 10px !important;
            }

            .email-header {
                padding: 20px !important;
            }

            .email-body {
                padding: 24px 16px !important;
            }

            h1 {
                font-size: 22px !important;
            }

            .button {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <img src="https://via.placeholder.com/200x40/4f46e5/ffffff?text=Tu+Empresa" alt="Logo" class="logo">
        </div>

        <!-- Body -->
        <div class="email-body">
            <h1>Hola, {{ $name }}</h1>

            <p>Gracias por registrarte en nuestra aplicación. Para completar tu registro y comenzar a usar tu cuenta, necesitamos verificar tu dirección de correo electrónico.</p>

            <p>Por favor, haz clic en el botón a continuación para verificar tu correo electrónico:</p>

            <a href="{{ $verificationUrl }}" class="button">Verificar correo electrónico</a>

            <p>Si el botón no funciona, también puedes copiar y pegar el siguiente enlace en tu navegador:</p>
            <p style="background-color: #f3f4f6; padding: 12px; border-radius: 6px; word-break: break-all; font-size: 14px;">
                {{ $verificationUrl }}
            </p>

            <div class="divider"></div>

            <p><strong>¿No te has registrado?</strong></p>
            <p>Si no has creado una cuenta con nosotros, puedes ignorar este correo electrónico de forma segura.</p>

            <div class="security-notice">
                <p>Por razones de seguridad, este enlace caducará en 60 minutos.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>&copy; 2024 Tu Empresa. Todos los derechos reservados.</p>
            <p>Dirección de la empresa, Ciudad, País</p>
        </div>
    </div>
</div>
</body>
</html>
