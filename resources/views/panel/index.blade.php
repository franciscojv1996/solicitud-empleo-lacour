<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Solicitudes de Empleo | Grupo Lacour</title>

    {{-- Estilos propios del panel --}}
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">

    {{-- JavaScript para la interactividad del panel y modal --}}
    <script src="{{ asset('js/panel.js') }}" defer></script>
</head>
<body>

    <!-- BARRA SUPERIOR DE NAVEGACIÓN -->
    <header class="navbar">
        <div class="navbar-container">
            <a href="{{ route('panel.index') }}" class="navbar-brand">
                <img src="{{ asset('img/logoFirma.png') }}" alt="Logo Grupo Lacour" class="navbar-logo">
                <div class="navbar-title">
                    <h1>GRUPO LACOUR, C.A.</h1>
                    <p>Panel Administrativo de Solicitudes de Empleo</p>
                </div>
            </a>

            <div class="navbar-actions">
                {{-- Identificación y rol del usuario autenticado --}}
                <div style="display: flex; align-items: center; gap: 8px; margin-right: 5px;">
                    <span style="font-size: 13px;">👤 <strong>{{ Auth::user()->name }}</strong></span>
                    <span class="role-badge role-{{ Auth::user()->role }}">{{ Auth::user()->role_badge }}</span>
                </div>

                {{-- Acceso a Gestión de Usuarios (Maestro y Admin) --}}
                @if(Auth::user()->canManageUsers())
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline" title="Administrar usuarios del sistema">
                        👥 Gestión de Usuarios
                    </a>
                @endif

                <a href="{{ route('solicitud.index') }}" target="_blank" class="btn btn-outline" title="Abrir el formulario público en otra pestaña">
                    ➕ Formulario Web
                </a>

                {{-- Cierre de Sesión --}}
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" title="Cerrar sesión">
                        🚪 Salir
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="main-content">

        {{-- Alertas de éxito o error --}}
        @if(session('status'))
            <div class="alert-banner success">
                ✅ {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-banner error">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <!-- BARRA DE BÚSQUEDA Y FILTROS -->
        <section class="filter-card">
            <form action="{{ route('panel.index') }}" method="GET" class="filter-form">
                <div class="search-input-group">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" 
                           placeholder="Buscar por Nombre, Cédula, Correo, Teléfono o Cargo...">
                </div>

                <select name="cargo" class="filter-select">
                    <option value="">Todos los cargos</option>
                    @foreach($listaCargos as $cargo)
                        <option value="{{ $cargo }}" {{ request('cargo') == $cargo ? 'selected' : '' }}>
                            {{ $cargo }}
                        </option>
                    @endforeach
                </select>

                <select name="con_cv" class="filter-select">
                    <option value="">Filtro de CV (Todos)</option>
                    <option value="si" {{ request('con_cv') == 'si' ? 'selected' : '' }}>Con CV Adjunto</option>
                    <option value="no" {{ request('con_cv') == 'no' ? 'selected' : '' }}>Sin CV</option>
                </select>

                {{-- Filtro de estado: visible solo para Admin y Maestro (el estándar nunca ve inhabilitados) --}}
                @if(!Auth::user()->isEstandar())
                    <select name="estado" class="filter-select">
                        <option value="">Estado: Todos</option>
                        <option value="activos" {{ request('estado') === 'activos' ? 'selected' : '' }}>Solo Activos</option>
                        <option value="inhabilitados" {{ request('estado') === 'inhabilitados' ? 'selected' : '' }}>Solo Inhabilitados</option>
                    </select>
                @endif

                <button type="submit" class="btn btn-primary">Filtrar</button>

                @if(request()->hasAny(['buscar', 'cargo', 'con_cv', 'estado']))
                    <a href="{{ route('panel.index') }}" class="btn btn-secondary">Limpiar</a>
                @endif
            </form>
        </section>

        <!-- TABLA PRINCIPAL DE SOLICITUDES -->
        <section class="table-card">
            <div class="table-header-info">
                <h2>Solicitudes Registradas</h2>
                <span>Mostrando {{ $solicitudes->count() }} de {{ $solicitudes->total() }} registros</span>
            </div>

            @if($solicitudes->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>No se encontraron solicitudes</h3>
                    <p>No hay registros que coincidan con los filtros aplicados o aún no se han recibido solicitudes.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Postulante</th>
                                <th>Cédula</th>
                                <th>Cargo Solicitado</th>
                                <th>Contacto</th>
                                <th>Adjuntos</th>
                                @if(!Auth::user()->isEstandar())
                                    <th style="text-align: center;">Estado</th>
                                @endif
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $s)
                                <tr style="{{ !$s->activo ? 'background-color: #fef2f2; opacity: 0.85;' : '' }}">
                                    <td><strong>{{ $s->id }}</strong></td>
                                    <td>{{ $s->fecha_solicitud ? \Carbon\Carbon::parse($s->fecha_solicitud)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <strong>{{ $s->nombres }}</strong>
                                        @if($s->empleado_actual === 'Si')
                                            <span style="font-size: 10px; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Empleado Actual</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-cedula">{{ $s->cedula_tipo }}-{{ $s->cedula_numero }}</span>
                                    </td>
                                    <td>
                                        <span class="cargo-tag">{{ $s->cargo_aspira }}</span>
                                        @if($s->sueldo_aspira)
                                            <div style="font-size: 11px; color: var(--text-muted);">Aspira: {{ $s->sueldo_aspira }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>📞 <a href="tel:{{ $s->telefono }}" style="color: inherit; text-decoration: none;">{{ $s->telefono }}</a></div>
                                        <div style="font-size: 11px; color: var(--text-secondary);">✉️ {{ $s->correo }}</div>
                                    </td>
                                    <td>
                                        @if($s->documentos && $s->documentos->cv_ruta)
                                            <a href="{{ asset('storage/' . $s->documentos->cv_ruta) }}" target="_blank" class="badge badge-cv" title="Ver Currículum">
                                                📄 CV
                                            </a>
                                        @else
                                            <span class="badge badge-no-cv">Sin CV</span>
                                        @endif
                                    </td>

                                    {{-- Columna de Estado para Admin y Maestro --}}
                                    @if(!Auth::user()->isEstandar())
                                        <td style="text-align: center;">
                                            @if($s->activo)
                                                <span class="badge badge-activo">Activo</span>
                                            @else
                                                <span class="badge badge-inactivo">Inhabilitado</span>
                                            @endif
                                        </td>
                                    @endif

                                    <td style="text-align: center;">
                                        <div class="table-actions">
                                            {{-- Botón Ver Detalle (para todos los usuarios) --}}
                                            <button type="button" class="btn btn-secondary btn-sm btn-ver-detalle" 
                                                    data-solicitud="{{ json_encode($s) }}"
                                                    title="Ver expediente completo">
                                                👁️ Ver Detalle
                                            </button>

                                            {{-- Botón Inhabilitar / Habilitar (Nivel Admin y Maestro) --}}
                                            @if(Auth::user()->canDisableSolicitud())
                                                <form action="{{ route('panel.solicitud.toggle', $s) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if($s->activo)
                                                        <button type="submit" class="btn btn-warning btn-sm" 
                                                                onclick="return confirm('¿Inhabilitar a {{ $s->nombres }}? Los usuarios estándar ya no podrán verlo.')"
                                                                title="Inhabilitar solicitud">
                                                            🚫 Inhabilitar
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-success btn-sm" 
                                                                onclick="return confirm('¿Habilitar nuevamente a {{ $s->nombres }}?')"
                                                                title="Habilitar solicitud">
                                                            ✅ Habilitar
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif

                                            {{-- Botón Eliminar Permanente (Exclusivo Nivel Maestro / Desarrollador) --}}
                                            @if(Auth::user()->canDeleteSolicitud())
                                                <form action="{{ route('panel.solicitud.destroy', $s) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('¡ADVERTENCIA DE SEGURIDAD!\n\n¿Estás seguro de ELIMINAR PERMANENTEMENTE la solicitud de {{ $s->nombres }} y todos sus archivos adjuntos?\n\nEsta acción NO se puede deshacer.')"
                                                            title="Eliminar permanentemente del sistema">
                                                        🗑️ Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                @if($solicitudes->hasPages())
                    <div class="pagination-wrapper">
                        {{ $solicitudes->links() }}
                    </div>
                @endif
            @endif
        </section>

    </main>

    <!-- MODAL DE EXPEDIENTE COMPLETO DEL POSTULANTE -->
    <div class="modal-overlay" id="candidateModal">
        <div class="modal-container">
            <header class="modal-header">
                <div>
                    <h3 id="modalCandidateName">Expediente del Postulante</h3>
                    <p id="modalCandidateSub">Cargando información...</p>
                </div>
                <button type="button" class="modal-close-btn" id="modalCloseBtn" aria-label="Cerrar modal">&times;</button>
            </header>

            <nav class="modal-tabs">
                <button type="button" class="modal-tab-btn active" data-tab="tab-personal">👤 Datos Personales</button>
                <button type="button" class="modal-tab-btn" data-tab="tab-familiares">👨‍👩‍👦 Familiares</button>
                <button type="button" class="modal-tab-btn" data-tab="tab-educacion">🎓 Educación</button>
                <button type="button" class="modal-tab-btn" data-tab="tab-experiencia">💼 Experiencia Laboral</button>
                <button type="button" class="modal-tab-btn" data-tab="tab-referencias">👥 Referencias</button>
                <button type="button" class="modal-tab-btn" data-tab="tab-documentos">📁 Documentos</button>
            </nav>

            <div class="modal-body">
                <!-- PESTAÑA 1: DATOS PERSONALES Y CARGO -->
                <div class="tab-pane active" id="tab-personal">
                    <h4 style="margin-bottom: 12px; color: var(--primary-color);">Información del Cargo y Datos Generales</h4>
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">Cargo que aspira</div><div class="detail-value" id="m_cargo">-</div></div>
                        <div class="detail-item"><div class="detail-label">Sueldo que aspira</div><div class="detail-value" id="m_sueldo">-</div></div>
                        <div class="detail-item"><div class="detail-label">¿Empleado actual?</div><div class="detail-value" id="m_empleado_actual">-</div></div>
                        <div class="detail-item"><div class="detail-label">Fecha de Solicitud</div><div class="detail-value" id="m_fecha_solicitud">-</div></div>
                        <div class="detail-item"><div class="detail-label">Cédula de Identidad</div><div class="detail-value" id="m_cedula">-</div></div>
                        <div class="detail-item"><div class="detail-label">Nombres y Apellidos</div><div class="detail-value" id="m_nombres">-</div></div>
                        <div class="detail-item"><div class="detail-label">Sexo</div><div class="detail-value" id="m_sexo">-</div></div>
                        <div class="detail-item"><div class="detail-label">Estado Civil</div><div class="detail-value" id="m_estado_civil">-</div></div>
                        <div class="detail-item"><div class="detail-label">Lugar y Fecha Nac.</div><div class="detail-value" id="m_nacimiento">-</div></div>
                        <div class="detail-item"><div class="detail-label">Edad</div><div class="detail-value" id="m_edad">-</div></div>
                        <div class="detail-item"><div class="detail-label">Nacionalidad</div><div class="detail-value" id="m_nacionalidad">-</div></div>
                        <div class="detail-item"><div class="detail-label">Teléfono</div><div class="detail-value" id="m_telefono">-</div></div>
                        <div class="detail-item"><div class="detail-label">Correo Electrónico</div><div class="detail-value" id="m_correo">-</div></div>
                        <div class="detail-item"><div class="detail-label">Vehículo</div><div class="detail-value" id="m_vehiculo">-</div></div>
                        <div class="detail-item"><div class="detail-label">Licencia de Conducir</div><div class="detail-value" id="m_licencia">-</div></div>
                        <div class="detail-item"><div class="detail-label">Certificado Médico</div><div class="detail-value" id="m_certificado_medico">-</div></div>
                    </div>

                    <h4 style="margin: 18px 0 10px 0; color: var(--primary-color);">Dirección y Medidas Físicas</h4>
                    <div class="detail-grid">
                        <div class="detail-item" style="grid-column: 1 / -1;"><div class="detail-label">Dirección de Habitación</div><div class="detail-value" id="m_direccion">-</div></div>
                        <div class="detail-item" style="grid-column: 1 / -1;"><div class="detail-label">Otra Dirección</div><div class="detail-value" id="m_otra_direccion">-</div></div>
                        <div class="detail-item"><div class="detail-label">Tallas (Camisa / Pantalón / Botas)</div><div class="detail-value" id="m_tallas">-</div></div>
                        <div class="detail-item"><div class="detail-label">Medidas (Estatura / Peso)</div><div class="detail-value" id="m_medidas">-</div></div>
                    </div>
                </div>

                <!-- PESTAÑA 2: FAMILIARES -->
                <div class="tab-pane" id="tab-familiares">
                    <h4 style="margin-bottom: 12px; color: var(--primary-color);">Grupo Familiar Registrado</h4>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Parentesco</th>
                                    <th>Nombre Completo</th>
                                    <th>Cédula</th>
                                    <th>Teléfono</th>
                                    <th>Edad</th>
                                </tr>
                            </thead>
                            <tbody id="m_familiares_tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- PESTAÑA 3: EDUCACIÓN -->
                <div class="tab-pane" id="tab-educacion">
                    <h4 style="margin-bottom: 12px; color: var(--primary-color);">Historial Académico</h4>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nivel</th>
                                    <th>Instituto</th>
                                    <th>Años Cursados</th>
                                    <th>Periodo</th>
                                    <th>Título</th>
                                    <th>Especialidad</th>
                                </tr>
                            </thead>
                            <tbody id="m_educacion_tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- PESTAÑA 4: EXPERIENCIA LABORAL -->
                <div class="tab-pane" id="tab-experiencia">
                    <h4 style="margin-bottom: 12px; color: var(--primary-color);">Trayectoria Laboral</h4>
                    <div id="m_experiencia_container"></div>
                </div>

                <!-- PESTAÑA 5: REFERENCIAS PERSONALES -->
                <div class="tab-pane" id="tab-referencias">
                    <h4 style="margin-bottom: 12px; color: var(--primary-color);">Referencias Personales</h4>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Empresa</th>
                                    <th>Ocupación</th>
                                    <th>Teléfono</th>
                                    <th>Tiempo Conociéndolo</th>
                                </tr>
                            </thead>
                            <tbody id="m_referencias_tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- PESTAÑA 6: DOCUMENTOS ADJUNTOS -->
                <div class="tab-pane" id="tab-documentos">
                    <h4 style="margin-bottom: 12px; color: var(--primary-color);">Archivos Digitales Adjuntos</h4>
                    <div id="m_documentos_container"></div>
                </div>
            </div>

            <footer class="modal-footer">
                <button type="button" class="btn btn-primary" id="printExpedienteBtn">🖨️ Imprimir Expediente</button>
                <button type="button" class="btn btn-secondary" id="modalCloseFooterBtn">Cerrar</button>
            </footer>
        </div>
    </div>

</body>
</html>
