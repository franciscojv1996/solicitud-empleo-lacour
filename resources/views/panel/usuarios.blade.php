<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Panel de Control | Grupo Lacour</title>

    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <style>
        .users-layout {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .users-layout {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: var(--radius-md);
            padding: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .card h2 {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 13px;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

    </style>
</head>
<body>

    <!-- NAVBAR SUPERIOR -->
    <header class="navbar">
        <div class="navbar-container">
            <a href="{{ route('panel.index') }}" class="navbar-brand">
                <img src="{{ asset('img/logoFirma.png') }}" alt="Logo Grupo Lacour" class="navbar-logo">
                <div class="navbar-title">
                    <h1>GRUPO LACOUR, C.A.</h1>
                    <p>Gestión y Control de Usuarios</p>
                </div>
            </a>

            <div class="navbar-actions">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 13px;">👤 <strong>{{ Auth::user()->name }}</strong></span>
                    <span class="role-badge role-{{ Auth::user()->role }}">{{ Auth::user()->role_badge }}</span>
                </div>

                <a href="{{ route('panel.index') }}" class="btn btn-outline">
                    ⬅️ Volver a Solicitudes
                </a>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">🚪 Salir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="main-content">

        @if(session('success'))
            <div class="alert-banner success">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-banner error">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-banner error">
                <div>
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul style="margin-top: 6px; padding-left: 18px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="users-layout">

            <!-- COLUMNA 1: FORMULARIO DE CREACIÓN DE USUARIO -->
            <div class="card">
                <h2>➕ Registrar Nuevo Usuario</h2>

                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nombre y Apellido:</label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="{{ old('name') }}" required placeholder="Ej: Carlos Mendoza">
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="{{ old('email') }}" required placeholder="usuario@grupolacour.com"
                               pattern="^[^@\s]+@grupolacour\.com$" title="Usa un correo que termine en @grupolacour.com">
                        <small>Solo se permiten correos @grupolacour.com.</small>
                    </div>

                    <div class="form-group">
                        <label for="role">Nivel de Seguridad:</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="">Selecciona el nivel...</option>
                            @foreach($rolesPermitidos as $key => $label)
                                <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña (mínimo 6 caracteres):</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña:</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 5px;">
                        💾 Guardar Usuario
                    </button>
                </form>
            </div>

            <!-- COLUMNA 2: TABLA DE USUARIOS DEL SISTEMA -->
            <div class="card">
                <h2>👥 Usuarios Registrados en el Sistema</h2>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Nivel / Rol</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $u)
                                <tr>
                                    <td>{{ $u->id }}</td>
                                    <td>
                                        <strong>{{ $u->name }}</strong>
                                        @if($u->id === Auth::id())
                                            <span style="font-size: 10px; background: #e0e7ff; color: #3730a3; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Tu cuenta</span>
                                        @endif
                                    </td>
                                    <td>{{ $u->email }}</td>
                                    <td>
                                        <span class="role-badge role-{{ $u->role }}">
                                            {{ $u->role_badge }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($u->id !== Auth::id())
                                            {{-- Maestro puede eliminar a cualquiera; Admin solo a estándar --}}
                                            @if(Auth::user()->isMaestro() || (Auth::user()->isAdmin() && $u->isEstandar()))
                                                <form action="{{ route('usuarios.destroy', $u) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('¿Seguro que deseas eliminar al usuario {{ $u->name }}?')">
                                                        🗑️ Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <span style="font-size: 11px; color: var(--text-muted);">Sin permiso</span>
                                            @endif
                                        @else
                                            <span style="font-size: 11px; color: var(--text-muted);">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>

</body>
</html>
