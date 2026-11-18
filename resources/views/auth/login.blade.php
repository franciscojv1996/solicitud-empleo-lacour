<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Grupo Lacour</title>

    <style>
        :root {
            --primary-color: #003366;
            --primary-hover: #002244;
            --secondary-color: #0066cc;
            --bg-color: #f1f5f9;
            --text-color: #0f172a;
            --border-color: #cbd5e1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 440px;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 15px;
        }

        .login-header h2 {
            font-size: 19px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .login-header p {
            font-size: 13px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
        }

        .remember-group {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
            margin-bottom: 20px;
        }

        .btn-submit {
            width: 100%;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #b91c1c;
            padding: 10px 14px;
            font-size: 13px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-status {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            color: #065f46;
            padding: 10px 14px;
            font-size: 13px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('img/logoFirma.png') }}" alt="Logo Grupo Lacour" class="login-logo">
            <h2>Acceso al Panel de Control</h2>
            <p>Ingresa tus credenciales autorizadas</p>
        </div>

        @if(session('status'))
            <div class="alert-status">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" class="form-control" 
                       value="{{ old('email') }}" required autofocus placeholder="usuario@grupolacour.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
            </div>

            <div class="remember-group">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Recordar mi sesión en este equipo</label>
            </div>

            <button type="submit" class="btn-submit">Ingresar al Panel</button>
        </form>
    </div>

</body>
</html>
