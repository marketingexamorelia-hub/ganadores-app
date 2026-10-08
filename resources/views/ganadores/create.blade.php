<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ganador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .box-caza-premios {
            background-color: #fff3cd;
            border: 1px solid #ffe69c;
            border-radius: 8px;
        }
        .box-alerta {
            background-color: #fff9db;
            border: 1px solid #fffecc;
            border-radius: 8px;
        }
        /* Estilo personalizado para el Premio Especial con el color #27F5C5 */
        .box-premio-especial {
            background-color: #e6fcf5; /* Un tono muy claro basado en tu color para el fondo */
            border: 1px solid #27F5C5;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 700px;">
        <h3 class="mb-4 text-dark fw-bold">➕ Registrar Nuevo Ganador</h3>

        <form action="{{ route('ganadores.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" value="{{ old('nombre') }}">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Edad</label>
                    <input type="number" name="edad" class="form-control" placeholder="Ej: 25" value="{{ old('edad') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" placeholder="Ej: 4431234567" value="{{ old('whatsapp') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Facebook ID / Perfil</label>
                <input type="text" name="facebook_id" class="form-control" placeholder="Ej: juan.perez" value="{{ old('facebook_id') }}">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fecha Dinámica</label>
                    <input type="date" name="fecha_dinamica" class="form-control" value="{{ old('fecha_dinamica') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fecha Entrega</label>
                    <input type="date" name="fecha_entrega" class="form-control" value="{{ old('fecha_entrega') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Programa</label>
                    <input type="text" name="programa" class="form-control" value="{{ old('programa') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Participando por (Premio)</label>
                    <input type="text" name="premio" class="form-control" value="{{ old('premio') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Patrocinador</label>
                    <input type="text" name="patrocinador" class="form-control" value="{{ old('patrocinador') }}">
                </div>
            </div>

            <!-- CHECK / MARCADOR CAZA PREMIOS -->
            <div class="box-caza-premios p-3 mb-3">
                <div class="form-check form-switch d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="caza_premios" id="caza_premios" value="1" {{ old('caza_premios') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="caza_premios">
                        ⚠️ Marcar como Caza Premios
                    </label>
                </div>
            </div>

            <!-- CHECK / MARCADOR ALERTA -->
            <div class="box-alerta p-3 mb-3 border border-warning">
                <div class="form-check form-switch d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="alerta" id="alerta" value="1" {{ old('alerta') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="alerta">
                        🔔 Marcar como Alerta
                    </label>
                </div>
            </div>

            <!-- CHECK / MARCADOR PREMIO ESPECIAL (NUEVO) -->
            <div class="box-premio-especial p-3 mb-4">
                <div class="form-check form-switch d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="premio_especial" id="premio_especial" value="1" {{ old('premio_especial') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="premio_especial">
                        🎁 Marcar como Premio Especial
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('ganadores.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar Registro</button>
            </div>
        </form>
    </div>

    <!-- Script para asegurar exclusión mutua visualmente entre los tres marcadores -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cazaPremios = document.getElementById('caza_premios');
            const alerta = document.getElementById('alerta');
            const premioEspecial = document.getElementById('premio_especial');

            if (cazaPremios && alerta && premioEspecial) {
                cazaPremios.addEventListener('change', function () {
                    if (this.checked) {
                        alerta.checked = false;
                        premioEspecial.checked = false;
                    }
                });

                alerta.addEventListener('change', function () {
                    if (this.checked) {
                        cazaPremios.checked = false;
                        premioEspecial.checked = false;
                    }
                });

                premioEspecial.addEventListener('change', function () {
                    if (this.checked) {
                        cazaPremios.checked = false;
                        alerta.checked = false;
                    }
                });
            }
        });
    </script>
</body>
</html>