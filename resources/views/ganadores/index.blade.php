<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Ganadores y Dinámicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #f8f9fa;
        }

        /* Estilos del Sidebar deslizable */
        #sidebar {
            width: 280px;
            position: fixed;
            top: 0;
            left: -280px;
            height: 100vh;
            z-index: 1050;
            background-color: #212529;
            color: #ffffff;
            transition: all 0.3s ease-in-out;
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.3);
        }

        #sidebar.active {
            left: 0;
        }

        #sidebarOverlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            top: 0;
            left: 0;
        }

        #sidebarOverlay.active {
            display: block;
        }

        /* Tabla y colores de filas */
        .table > thead > tr > th {
            background-color: #8d6e63 !important;
            color: #ffffff !important;
            border-color: #6d4c41 !important;
            font-weight: 600;
        }

        .table > tbody > tr.fila-entregado > td {
            background-color: #dcf8c6 !important;
            color: #0b4e22;
        }

        .table > tbody > tr.fila-caza-premios > td {
            background-color: #f8d7da !important;
            color: #842029;
        }

        .table > tbody > tr.fila-alerta > td {
            background-color: #ffeb3b !important;
            color: #000000 !important;
            font-weight: 500;
        }

        /* Color de fondo naranja, texto en negro y negrita para Premio Especial */
        .table > tbody > tr.fila-premio-especial > td {
            background-color: #f26a36 !important;
            color: #000000 !important;
            font-weight: bold !important;
        }

        .header-logo {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        /* Cajas de marcadores en tono bajo dentro del modal */
        .box-caza-premios {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            border-radius: 8px;
        }

        .box-alerta {
            background-color: #fff9c4;
            border: 1px solid #ffee58;
            border-radius: 8px;
        }

        .box-premio-especial {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            border-radius: 8px;
        }

        /* Botón desplegable de Marcadores en Verde Bajo */
        .btn-desplegable-marcador {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132 !important;
        }
        .btn-desplegable-marcador:hover {
            background-color: #bcd0c7;
            border-color: #a3c2b4;
            color: #0f5132 !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>
<body class="p-0 m-0">

    {{-- LA BARRA LATERAL SOLO SE RENDERIZA SI NO ES INVITADO --}}
    @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
        <!-- Capa Oscura de Fondo para el Sidebar -->
        <div id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Barra Lateral Izquierda (Sidebar) -->
        <div id="sidebar" class="d-flex flex-column p-3">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-2">
                <h5 class="m-0 fw-bold">⚙️ Menú Principal</h5>
                <button class="btn btn-sm btn-outline-light border-0" onclick="toggleSidebar()">✕</button>
            </div>

            <ul class="nav nav-pills flex-column mb-auto gap-2">
                <li class="nav-item">
                    <button class="btn btn-danger w-100 text-start d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalEliminarAno">
                        <span>🗑️ Eliminar por Año</span>
                        <span class="badge bg-light text-danger">Admin</span>
                    </button>
                </li>
            </ul>

            <div class="border-top border-secondary pt-3 mt-auto">
                <div class="small text-muted mb-1">Usuario activo:</div>
                <div class="fw-bold text-truncate">{{ Auth::user()->name ?? Auth::user()->email }}</div>
            </div>
        </div>
    @endif

    <!-- Contenido Principal -->
    <div class="container-fluid px-3 py-3 bg-white shadow-none w-100" style="min-height: 100vh;">
        
        <!-- Header Principal -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
            <div class="d-flex align-items-center gap-3">
                @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
                    <button class="btn btn-outline-dark" onclick="toggleSidebar()" title="Abrir Menú">
                        ☰
                    </button>
                @endif

                <img src="{{ asset('images/logo.png') }}" alt="Exa FM" class="header-logo">
                <h2 class="m-0 fw-bold text-dark">Control de Ganadores y Dinámicas</h2>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                @auth
                    <span class="text-secondary small me-2">
                        👤 {{ Auth::user()->name ?? Auth::user()->email }} 
                        @if(Auth::user()->email === 'exa@invitado.com')
                            <span class="badge bg-secondary">Invitado</span>
                        @else
                            <span class="badge bg-primary">Administrador</span>
                        @endif
                    </span>
                @endauth

                <a href="{{ route('ganadores.export') }}" class="btn btn-outline-success">
                    📊 Excel
                </a>

                @auth
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrear">
                        + Nuevo Registro
                    </button>
                @endauth
                
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Cerrar Sesión</button>
                    </form>
                @endauth
            </div>
        </div>

        <!-- Alertas de Éxito / Error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Barra de Herramientas (Importar y Buscar) -->
        <div class="card mb-4 bg-light border">
            <div class="card-body py-3">
                <div class="row align-items-center g-3">
                    
                    @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
                        <div class="col-lg-6 border-end">
                            <form action="{{ route('ganadores.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-auto">
                                    <label for="archivo_excel" class="col-form-label fw-bold">📥 Importar Excel:</label>
                                </div>
                                <div class="col">
                                    <input type="file" name="archivo_excel" id="archivo_excel" class="form-control form-control-sm" accept=".xlsx, .xls, .csv" required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-sm">Subir e Importar</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="{{ (Auth::check() && Auth::user()->email !== 'exa@invitado.com') ? 'col-lg-6' : 'col-lg-12' }}">
                        <form action="{{ route('ganadores.index') }}" method="GET" class="row g-2 align-items-center justify-content-end">
                            <div class="col">
                                <input type="text" 
                                       name="search" 
                                       class="form-control form-control-sm" 
                                       placeholder="Buscar por nombre, whatsapp o programa..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                            </div>
                            @if(request('search'))
                                <div class="col-auto">
                                    <a href="{{ route('ganadores.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                                </div>
                            @endif
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Tabla de Registros -->
        <div class="table-responsive w-100">
            <table class="table table-bordered table-hover align-middle w-100 m-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Nombre del ganador</th>
                        <th class="text-center">Edad</th>
                        <th class="text-center">WhatsApp</th>
                        <th>Facebook ID</th>
                        <th class="text-center">Fecha Dinámica</th>
                        <th class="text-center">Fecha Entrega</th>
                        <th>Programa</th>
                        <th>Participando por</th>
                        <th>Patrocinador</th>
                        
                        @auth
                            <th class="text-center">Acciones</th>
                        @endauth
                    </tr>
                </thead>
                <tbody>
                    @forelse($ganadores as $ganador)
                        <tr class="{{ $ganador->fecha_entrega ? 'fila-entregado' : '' }} {{ $ganador->caza_premios ? 'fila-caza-premios' : '' }} {{ $ganador->alerta ? 'fila-alerta' : '' }} {{ $ganador->premio_especial ? 'fila-premio-especial' : '' }}">
                            <td class="text-center fw-bold">{{ $ganador->id }}</td>
                            
                            <td>
                                {{ ($ganador->nombre && strtolower(trim($ganador->nombre)) !== 'sin nombre') ? $ganador->nombre : '-' }}
                                
                                @if($ganador->caza_premios)
                                    <span class="badge bg-danger text-white ms-1" title="Marcado como Caza Premios">
                                        ⚠️ Caza Premios
                                    </span>
                                @endif

                                @if($ganador->alerta)
                                    <span class="badge bg-dark text-warning border border-warning ms-1" title="Marcado con Alerta">
                                        🚨 Alerta
                                    </span>
                                @endif

                                @if($ganador->premio_especial)
                                    <span class="badge bg-dark text-white border border-light ms-1" title="Premio Especial">
                                        🎁 Premio Especial
                                    </span>
                                @endif
                            </td>
                            
                            <td class="text-center">{{ $ganador->edad ?? '-' }}</td>
                            <td class="text-center">{{ $ganador->whatsapp ?? '-' }}</td>
                            <td>{{ $ganador->facebook_id ?? '-' }}</td>
                            <td class="text-center">
                                {{ $ganador->fecha_dinamica ? date('d/m/Y', strtotime($ganador->fecha_dinamica)) : '-' }}
                            </td>
                            <td class="text-center">
                                {{ $ganador->fecha_entrega ? date('d/m/Y', strtotime($ganador->fecha_entrega)) : '-' }}
                            </td>
                            <td>{{ $ganador->programa ?? '-' }}</td>
                            <td>{{ $ganador->premio ?? '-' }}</td>
                            <td>{{ $ganador->patrocinador ?? '-' }}</td>
                            
                            @auth
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Botón Editar -->
                                        <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $ganador->id }}" title="Editar">
                                            Editar
                                        </button>

                                        <!-- Botón Borrar Registro Individual (Disponible para Admin e Invitado) -->
                                        <form action="{{ route('ganadores.destroy', $ganador->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm fw-bold" title="Eliminar registro">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endauth
                        </tr>

                        @auth
                            <!-- Modal Editar para cada registro -->
                            <div class="modal fade" id="modalEditar{{ $ganador->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <form action="{{ route('ganadores.update', $ganador->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold">Editar Registro #{{ $ganador->id }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-bold">Nombre</label>
                                                        <input type="text" name="nombre" class="form-control" value="{{ $ganador->nombre }}" required>
                                                    </div>
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label fw-bold">Edad</label>
                                                        <input type="number" name="edad" class="form-control" value="{{ $ganador->edad }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold">WhatsApp</label>
                                                        <input type="text" name="whatsapp" class="form-control" value="{{ $ganador->whatsapp }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold">Facebook ID</label>
                                                        <input type="text" name="facebook_id" class="form-control" value="{{ $ganador->facebook_id }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold">Fecha Dinámica</label>
                                                        <input type="date" name="fecha_dinamica" class="form-control" value="{{ $ganador->fecha_dinamica ? \Carbon\Carbon::parse($ganador->fecha_dinamica)->format('Y-m-d') : '' }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold">Fecha Entrega</label>
                                                        <input type="date" name="fecha_entrega" class="form-control" value="{{ $ganador->fecha_entrega ? \Carbon\Carbon::parse($ganador->fecha_entrega)->format('Y-m-d') : '' }}">
                                                    </div>
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label fw-bold">Programa</label>
                                                        <input type="text" name="programa" class="form-control" value="{{ $ganador->programa }}">
                                                    </div>
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label fw-bold">Premio</label>
                                                        <input type="text" name="premio" class="form-control" value="{{ $ganador->premio }}">
                                                    </div>
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label fw-bold">Patrocinador</label>
                                                        <input type="text" name="patrocinador" class="form-control" value="{{ $ganador->patrocinador }}">
                                                    </div>
                                                </div>

                                                <!-- Botón desplegable para Marcadores en Editar -->
                                                <div class="mb-3">
                                                    <a class="btn btn-desplegable-marcador btn-sm d-flex justify-content-between align-items-center fw-bold" 
                                                       data-bs-toggle="collapse" 
                                                       href="#seccionMarcadoresEditar{{ $ganador->id }}" 
                                                       role="button" 
                                                       aria-expanded="false" 
                                                       aria-controls="seccionMarcadoresEditar{{ $ganador->id }}">
                                                        <span>🏷️ Marcadores</span>
                                                        <span class="fs-6">▼</span>
                                                    </a>
                                                </div>

                                                <!-- Contenido desplegable de Marcadores en Editar -->
                                                <div class="collapse mb-3" id="seccionMarcadoresEditar{{ $ganador->id }}">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-2">
                                                            <div class="box-caza-premios p-3 h-100">
                                                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                                                    <input type="hidden" name="caza_premios" value="0">
                                                                    <input class="form-check-input" type="checkbox" name="caza_premios" id="caza_premios_edit_{{ $ganador->id }}" value="1" {{ $ganador->caza_premios ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-bold text-danger cursor-pointer" for="caza_premios_edit_{{ $ganador->id }}">
                                                                        ⚠️ Caza Premios
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <div class="box-alerta p-3 h-100">
                                                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                                                    <input type="hidden" name="alerta" value="0">
                                                                    <input class="form-check-input" type="checkbox" name="alerta" id="alerta_edit_{{ $ganador->id }}" value="1" {{ $ganador->alerta ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="alerta_edit_{{ $ganador->id }}">
                                                                        🚨 Alerta
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <div class="box-premio-especial p-3 h-100">
                                                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                                                    <input type="hidden" name="premio_especial" value="0">
                                                                    <input class="form-check-input" type="checkbox" name="premio_especial" id="premio_especial_edit_{{ $ganador->id }}" value="1" {{ $ganador->premio_especial ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-bold cursor-pointer text-success" for="premio_especial_edit_{{ $ganador->id }}">
                                                                        🎁 Premio Especial
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary fw-bold">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth

                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                @if(request('search'))
                                    No se encontraron resultados para "{{ request('search') }}".
                                @else
                                    No hay ganadores registrados.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-3">
            {{ $ganadores->appends(request()->query())->links() }}
        </div>

    </div>

    <!-- Modal: Nuevo Ganador -->
    @auth
        <div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form action="{{ route('ganadores.store') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title fw-bold">➕ Registrar Nuevo Ganador</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Nombre Completo *</label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Edad</label>
                                    <input type="number" name="edad" class="form-control" placeholder="Ej: 25">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">WhatsApp</label>
                                    <input type="text" name="whatsapp" class="form-control" placeholder="Ej: 4431234567">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Facebook ID / Perfil</label>
                                    <input type="text" name="facebook_id" class="form-control" placeholder="Ej: juan.perez">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Fecha Dinámica</label>
                                    <input type="date" name="fecha_dinamica" class="form-control">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Fecha Entrega</label>
                                    <input type="date" name="fecha_entrega" class="form-control">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Programa</label>
                                    <input type="text" name="programa" class="form-control">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Premio</label>
                                    <input type="text" name="premio" class="form-control">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Patrocinador</label>
                                    <input type="text" name="patrocinador" class="form-control">
                                </div>
                            </div>

                            <!-- Botón desplegable para Marcadores en Crear -->
                            <div class="mb-3">
                                <a class="btn btn-desplegable-marcador btn-sm d-flex justify-content-between align-items-center fw-bold" 
                                   data-bs-toggle="collapse" 
                                   href="#seccionMarcadoresCrear" 
                                   role="button" 
                                   aria-expanded="false" 
                                   aria-controls="seccionMarcadoresCrear">
                                    <span>🏷️ Marcadores</span>
                                    <span class="fs-6">▼</span>
                                </a>
                            </div>

                            <!-- Contenido desplegable de Marcadores en Crear -->
                            <div class="collapse mb-3" id="seccionMarcadoresCrear">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="box-caza-premios p-3 h-100">
                                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                                <input type="hidden" name="caza_premios" value="0">
                                                <input class="form-check-input" type="checkbox" name="caza_premios" id="caza_premios_create" value="1">
                                                <label class="form-check-label fw-bold text-danger cursor-pointer" for="caza_premios_create">
                                                    ⚠️ Caza Premios
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="box-alerta p-3 h-100">
                                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                                <input type="hidden" name="alerta" value="0">
                                                <input class="form-check-input" type="checkbox" name="alerta" id="alerta_create" value="1">
                                                <label class="form-check-label fw-bold text-dark cursor-pointer" for="alerta_create">
                                                    🚨 Alerta
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="box-premio-especial p-3 h-100">
                                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                                <input type="hidden" name="premio_especial" value="0">
                                                <input class="form-check-input" type="checkbox" name="premio_especial" id="premio_especial_create" value="1">
                                                <label class="form-check-label fw-bold cursor-pointer text-success" for="premio_especial_create">
                                                    🎁 Premio Especial
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success fw-bold">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    @if(Auth::check() && Auth::user()->email !== 'exa@invitado.com')
        <!-- Modal: Eliminar Registros por Año -->
        <div class="modal fade" id="modalEliminarAno" tabindex="-1" aria-labelledby="modalEliminarAnoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('ganadores.destroyPorAno') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="modalEliminarAnoLabel">🗑️ Eliminar Registros por Año</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <p class="text-muted">
                                Escribe el año para eliminar permanentemente todos los registros almacenados que pertenezcan a dicho año.
                            </p>

                            <div class="mb-3">
                                <label for="ano" class="form-label fw-bold">Escribe el año a eliminar (Ejemplo: 2024):</label>
                                <input type="number" 
                                       name="ano" 
                                       id="ano" 
                                       class="form-control" 
                                       placeholder="YYYY" 
                                       min="2000" 
                                       max="2099" 
                                       required>
                            </div>

                            <div class="alert alert-warning small mb-0">
                                ⚠️ <strong>Atención:</strong> Esta acción no se puede deshacer.
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger fw-bold" onclick="return confirm('¿Confirmas que deseas borrar todos los registros del año ingresado?')">
                                Confirmar y Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            if(sidebar && overlay) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>