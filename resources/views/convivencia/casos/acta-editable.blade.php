@extends('layouts.tabler')

@section('title', 'Acta de Atención a Situación de Convivencia N° ' . ($caso->numero_acta ?? $caso->id))

@section('content')
<div class="container-xl">
    <!-- Barra de acciones superior -->
    <div class="row mb-3 no-print">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Acta de Convivencia N° {{ $caso->numero_acta ?? $caso->id }}</h3>
                        <small class="text-muted">Edición de documento oficial</small>
                    </div>
                    <div class="btn-list">
                        <a href="{{ route('convivencia.casos.show', $caso) }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Volver
                        </a>
                        <button type="button" onclick="document.getElementById('formActa').submit()" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('convivencia.casos.pdf', $caso) }}" class="btn btn-success">
                            <i class="ti ti-file-download me-1"></i> Descargar PDF
                        </a>
                        <a href="{{ route('convivencia.casos.print', $caso) }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="ti ti-printer me-1"></i> Vista Previa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documento tipo carta (21.59 cm x 27.94 cm) -->
    <form id="formActa" method="POST" action="{{ route('convivencia.casos.update', $caso) }}">
        @csrf
        @method('PUT')

        <div class="documento-carta">
            <!-- PÁGINA 1: Información General y Desarrollo -->
            <div class="hoja-carta page-1">
                <!-- Header institucional -->
                <table class="header-table">
                    <tr>
                        <td class="header-logo">
                            @if(file_exists(public_path('img/Escudo.jpg')))
                                <img src="{{ asset('img/Escudo.jpg') }}" alt="Escudo Institucional">
                            @elseif(file_exists(public_path('images/logo-iejag.png')))
                                <img src="{{ asset('images/logo-iejag.png') }}" alt="Logo IEJAG">
                            @else
                                <div class="placeholder-logo">
                                    ESCUDO<br>IEJAG
                                </div>
                            @endif
                        </td>
                        <td class="header-title">
                            INSTITUCIÓN EDUCATIVA JOSÉ ANTONIO GALÁN<br>
                            ACTA DE ATENCIÓN A SITUACIÓN DE CONVIVENCIA
                        </td>
                        <td class="header-acta">
                            ACTA N°:<br>
                            <input type="text" name="numero_acta" value="{{ old('numero_acta', $caso->numero_acta ?? $caso->id) }}" class="form-control form-control-sm text-center fw-bold">
                        </td>
                    </tr>
                </table>

                <!-- Información básica: Grupo, Fecha, Hora -->
                <table class="info-table">
                    <tr>
                        <td class="info-label" style="width: 12%;">Grupo:</td>
                        <td style="width: 38%;">
                            <strong>{{ $caso->estudiante->matriculaActual->grupo->nombre ?? 'N/A' }}</strong>
                        </td>
                        <td class="info-label" style="width: 12%;">Fecha:</td>
                        <td style="width: 18%;">
                            <input type="date" name="fecha_reporte" value="{{ old('fecha_reporte', $caso->fecha_reporte->format('Y-m-d')) }}" class="form-control form-control-sm" required>
                        </td>
                        <td class="info-label" style="width: 8%;">Hora:</td>
                        <td style="width: 12%;">
                            <input type="time" name="hora_reporte" value="{{ old('hora_reporte', $caso->hora_reporte ? \Carbon\Carbon::parse($caso->hora_reporte)->format('H:i') : $caso->created_at->format('H:i')) }}" class="form-control form-control-sm">
                        </td>
                    </tr>
                </table>

                <!-- Estudiantes y Acudientes -->
                <table class="info-table">
                    <tr>
                        <td class="info-label" style="width: 18%; vertical-align: top;">Estudiante(s):</td>
                        <td style="width: 32%; vertical-align: top;">
                            <div class="estudiante-info">
                                <strong>● {{ strtoupper($caso->estudiante->nombre_completo) }}</strong><br>
                                <small>{{ $caso->estudiante->tipo_documento }}. {{ $caso->estudiante->numero_documento }}</small>
                            </div>
                            @if($caso->estudiantesInvolucrados && $caso->estudiantesInvolucrados->count() > 0)
                                @foreach($caso->estudiantesInvolucrados as $involucrado)
                                    <div class="estudiante-info mt-1">
                                        <strong>● {{ strtoupper($involucrado->nombre_completo) }}</strong>
                                        <span class="badge badge-sm bg-secondary">{{ ucfirst($involucrado->pivot->rol) }}</span><br>
                                        <small>{{ $involucrado->tipo_documento }}. {{ $involucrado->numero_documento }}</small>
                                    </div>
                                @endforeach
                            @endif
                        </td>
                        <td class="info-label" style="width: 18%; vertical-align: top;">Acudiente(s):</td>
                        <td style="width: 32%; vertical-align: top;">
                            @if($caso->estudiante->nombre_acudiente)
                                <div class="acudiente-info">
                                    <strong>● {{ strtoupper($caso->estudiante->nombre_acudiente) }}</strong><br>
                                    @if($caso->estudiante->telefono_acudiente)
                                        <small>Cel: {{ $caso->estudiante->telefono_acudiente }}</small><br>
                                    @endif
                                    @if($caso->estudiante->email_acudiente)
                                        <small>Email: {{ $caso->estudiante->email_acudiente }}</small>
                                    @endif
                                </div>
                            @else
                                <strong>● SIN REGISTRO</strong>
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- Director de grupo y Coordinador -->
                <table class="info-table">
                    <tr>
                        <td class="info-label" style="width: 22%;">Director(a) de grupo:</td>
                        <td style="width: 78%;">
                            {{ $caso->estudiante->matriculaActual->grupo->directorGrupo->name ?? 'Sin asignar' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Coordinador(a):</td>
                        <td>{{ $caso->reportadoPor->name ?? 'Sin asignar' }}</td>
                    </tr>
                </table>

                <!-- Desarrollo de la reunión -->
                <div class="content-box">
                    <h3 class="content-title">DESARROLLO DE LA REUNIÓN</h3>

                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1">Descripción de los hechos:</label>
                        <textarea name="descripcion_hechos" rows="5" class="form-control form-control-sm" required>{{ old('descripcion_hechos', $caso->descripcion_hechos) }}</textarea>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-1">Lugar:</label>
                            <input type="text" name="lugar" value="{{ old('lugar', $caso->lugar) }}" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1">Testigos:</label>
                        <textarea name="testigos" rows="2" class="form-control form-control-sm">{{ old('testigos', $caso->testigos) }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1">Evidencias:</label>
                        <textarea name="evidencias" rows="2" class="form-control form-control-sm">{{ old('evidencias', $caso->evidencias) }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1">Contexto de la situación:</label>
                        <textarea name="contexto_situacion" rows="3" class="form-control form-control-sm">{{ old('contexto_situacion', $caso->contexto_situacion) }}</textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold mb-1">Análisis institucional:</label>
                        <textarea name="analisis_institucional" rows="3" class="form-control form-control-sm">{{ old('analisis_institucional', $caso->analisis_institucional) }}</textarea>
                    </div>
                </div>

                <div class="page-footer">Página 1 de 3</div>
            </div>

            <!-- PÁGINA 2: Tipificación y Acciones -->
            <div class="hoja-carta page-2 page-break">
                <!-- Header institucional -->
                <table class="header-table">
                    <tr>
                        <td class="header-logo">
                            @if(file_exists(public_path('img/Escudo.jpg')))
                                <img src="{{ asset('img/Escudo.jpg') }}" alt="Escudo Institucional">
                            @elseif(file_exists(public_path('images/logo-iejag.png')))
                                <img src="{{ asset('images/logo-iejag.png') }}" alt="Logo IEJAG">
                            @else
                                <div class="placeholder-logo">
                                    ESCUDO<br>IEJAG
                                </div>
                            @endif
                        </td>
                        <td class="header-title">
                            INSTITUCIÓN EDUCATIVA JOSÉ ANTONIO GALÁN<br>
                            ACTA DE ATENCIÓN A SITUACIÓN DE CONVIVENCIA
                        </td>
                        <td class="header-acta">
                            ACTA N°:<br>
                            <strong>{{ $caso->numero_acta ?? $caso->id }}</strong>
                        </td>
                    </tr>
                </table>

                <!-- Continuación del contenido -->
                <div class="content-box">
                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1">Conclusiones:</label>
                        <textarea name="conclusiones" rows="3" class="form-control form-control-sm">{{ old('conclusiones', $caso->conclusiones) }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1">Acciones inmediatas tomadas:</label>
                        <textarea name="acciones_tomadas" rows="3" class="form-control form-control-sm">{{ old('acciones_tomadas', $caso->acciones_tomadas) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1">Acciones pedagógicas y restaurativas:</label>
                        <textarea name="acciones_pedagogicas" rows="3" class="form-control form-control-sm">{{ old('acciones_pedagogicas', $caso->acciones_pedagogicas) }}</textarea>
                    </div>

                    <div class="tipificacion-section">
                        <p class="fw-bold mb-1">Tipificación según Manual de Convivencia Escolar:</p>

                        <div class="mb-2">
                            <label class="form-label mb-1">Tipo de situación:</label>
                            <select name="tipo_anotacion_id" class="form-control form-control-sm" required>
                                @foreach(\App\Models\TipoAnotacion::orderBy('nombre')->get() as $tipo)
                                    <option value="{{ $tipo->id }}"
                                        {{ old('tipo_anotacion_id', $caso->tipo_anotacion_id) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                        ({{ $tipo->tipo_situacion == 'tipo_i' ? 'Tipo I' : ($tipo->tipo_situacion == 'tipo_ii' ? 'Tipo II' : 'Tipo III') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($caso->tipoAnotacion)
                            <div class="alert alert-info mb-2">
                                <p class="mb-0"><strong>{{ $caso->tipoAnotacion->nombre }}</strong></p>
                                <p class="mb-0 small">{{ $caso->tipoAnotacion->descripcion }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Suspensión -->
                    <div class="suspension-section mt-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="requirio_suspension" id="requirio_suspension" value="1"
                                {{ old('requirio_suspension', $caso->requirio_suspension) ? 'checked' : '' }}
                                onchange="document.getElementById('suspension-details').style.display = this.checked ? 'block' : 'none'">
                            <label class="form-check-label fw-bold" for="requirio_suspension">
                                SUSPENSIÓN APLICADA
                            </label>
                        </div>

                        <div id="suspension-details" style="display: {{ $caso->requirio_suspension ? 'block' : 'none' }}">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small mb-1">Días de suspensión:</label>
                                    <input type="number" name="dias_suspension" value="{{ old('dias_suspension', $caso->dias_suspension) }}" class="form-control form-control-sm" min="0">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small mb-1">Fecha inicio:</label>
                                    <input type="date" name="fecha_inicio_suspension" value="{{ old('fecha_inicio_suspension', $caso->fecha_inicio_suspension?->format('Y-m-d')) }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small mb-1">Fecha fin:</label>
                                    <input type="date" name="fecha_fin_suspension" value="{{ old('fecha_fin_suspension', $caso->fecha_fin_suspension?->format('Y-m-d')) }}" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remisión a Psicología -->
                    <div class="psicologia-section mt-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="remitido_psicologia" id="remitido_psicologia" value="1"
                                {{ old('remitido_psicologia', $caso->remitido_psicologia) ? 'checked' : '' }}
                                onchange="document.getElementById('psicologia-details').style.display = this.checked ? 'block' : 'none'">
                            <label class="form-check-label fw-bold" for="remitido_psicologia">
                                REMISIÓN A PSICOLOGÍA
                            </label>
                        </div>

                        <div id="psicologia-details" style="display: {{ $caso->remitido_psicologia ? 'block' : 'none' }}">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small mb-1">Fecha de remisión:</label>
                                    <input type="date" name="fecha_remision_psicologia" value="{{ old('fecha_remision_psicologia', $caso->fecha_remision_psicologia?->format('Y-m-d')) }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-8 mb-2">
                                    <label class="form-label small mb-1">Observaciones:</label>
                                    <textarea name="observaciones_psicologia" rows="2" class="form-control form-control-sm">{{ old('observaciones_psicologia', $caso->observaciones_psicologia) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notificación al Acudiente -->
                    <div class="notificacion-section mt-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="acudiente_notificado" id="acudiente_notificado" value="1"
                                {{ old('acudiente_notificado', $caso->acudiente_notificado) ? 'checked' : '' }}
                                onchange="document.getElementById('notificacion-details').style.display = this.checked ? 'block' : 'none'">
                            <label class="form-check-label fw-bold" for="acudiente_notificado">
                                NOTIFICACIÓN AL ACUDIENTE
                            </label>
                        </div>

                        <div id="notificacion-details" style="display: {{ $caso->acudiente_notificado ? 'block' : 'none' }}">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small mb-1">Fecha notificación:</label>
                                    <input type="date" name="fecha_notificacion_acudiente" value="{{ old('fecha_notificacion_acudiente', $caso->fecha_notificacion_acudiente?->format('Y-m-d')) }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small mb-1">Medio de notificación:</label>
                                    <input type="text" name="medio_notificacion" value="{{ old('medio_notificacion', $caso->medio_notificacion) }}" class="form-control form-control-sm" placeholder="Ej: Llamada telefónica">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label small mb-1">Respuesta del acudiente:</label>
                                    <textarea name="respuesta_acudiente" rows="2" class="form-control form-control-sm">{{ old('respuesta_acudiente', $caso->respuesta_acudiente) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-footer">Página 2 de 3</div>
            </div>

            <!-- PÁGINA 3: Compromisos y Firmas -->
            <div class="hoja-carta page-3 page-break">
                <!-- Header institucional -->
                <table class="header-table">
                    <tr>
                        <td class="header-logo">
                            @if(file_exists(public_path('img/Escudo.jpg')))
                                <img src="{{ asset('img/Escudo.jpg') }}" alt="Escudo Institucional">
                            @elseif(file_exists(public_path('images/logo-iejag.png')))
                                <img src="{{ asset('images/logo-iejag.png') }}" alt="Logo IEJAG">
                            @else
                                <div class="placeholder-logo">
                                    ESCUDO<br>IEJAG
                                </div>
                            @endif
                        </td>
                        <td class="header-title">
                            INSTITUCIÓN EDUCATIVA JOSÉ ANTONIO GALÁN<br>
                            ACTA DE ATENCIÓN A SITUACIÓN DE CONVIVENCIA
                        </td>
                        <td class="header-acta">
                            ACTA N°:<br>
                            <strong>{{ $caso->numero_acta ?? $caso->id }}</strong>
                        </td>
                    </tr>
                </table>

                <!-- Acuerdos y Compromisos -->
                <div class="compromises-section">
                    <h3 class="section-title">ACUERDOS Y COMPROMISOS</h3>

                    <div class="compromise-box">
                        <h4 class="compromise-title">DEL ACUDIENTE:</h4>
                        <textarea name="compromiso_acudiente" rows="4" class="form-control form-control-sm" placeholder="Escribir compromisos del acudiente...">{{ old('compromiso_acudiente', $caso->compromiso_acudiente ?? $caso->compromiso) }}</textarea>
                    </div>

                    <div class="compromise-box">
                        <h4 class="compromise-title">DEL ESTUDIANTE:</h4>
                        <textarea name="compromiso_estudiante" rows="5" class="form-control form-control-sm" placeholder="Escribir compromisos del estudiante...">{{ old('compromiso_estudiante', $caso->compromiso_estudiante) }}</textarea>
                    </div>

                    <div class="compromise-box">
                        <h4 class="compromise-title">DE LA INSTITUCIÓN:</h4>
                        <textarea name="compromiso_institucion" rows="3" class="form-control form-control-sm" placeholder="Escribir compromisos de la institución...">{{ old('compromiso_institucion', $caso->compromiso_institucion) }}</textarea>
                    </div>
                </div>

                <!-- Firmas -->
                <div class="signatures-section">
                    <h3 class="section-title">FIRMAS</h3>

                    <table class="signatures-table">
                        <tr>
                            <td>
                                <div class="signature-space"></div>
                                <div class="signature-label">
                                    <strong>ESTUDIANTE</strong><br>
                                    {{ strtoupper($caso->estudiante->nombre_completo) }}<br>
                                    <small>{{ $caso->estudiante->tipo_documento }}. {{ $caso->estudiante->numero_documento }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="signature-space"></div>
                                <div class="signature-label">
                                    <strong>PADRE O ACUDIENTE</strong><br>
                                    {{ strtoupper($caso->estudiante->nombre_acudiente ?? 'SIN REGISTRO') }}<br>
                                    @if($caso->estudiante->numero_documento_acudiente)
                                        <small>C.C. {{ $caso->estudiante->numero_documento_acudiente }}</small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="signature-space"></div>
                                <div class="signature-label">
                                    <strong>COORDINADOR(A)</strong><br>
                                    {{ strtoupper($caso->reportadoPor->name ?? 'SIN ASIGNAR') }}
                                </div>
                            </td>
                            <td>
                                <div class="signature-space"></div>
                                <div class="signature-label">
                                    <strong>DIRECTOR(A) DE GRUPO</strong><br>
                                    {{ strtoupper($caso->estudiante->matriculaActual->grupo->directorGrupo->name ?? 'SIN ASIGNAR') }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="signature-space"></div>
                                <div class="signature-label">
                                    <strong>PROFESIONAL PEEP / ORIENTACIÓN</strong>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Observaciones Generales -->
                <div class="observaciones-section">
                    <label class="form-label fw-bold mb-1">OBSERVACIONES GENERALES:</label>
                    <textarea name="observaciones_generales" rows="3" class="form-control form-control-sm">{{ old('observaciones_generales', $caso->observaciones_generales) }}</textarea>
                </div>

                <!-- Estado del caso -->
                <div class="estado-section mt-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label fw-bold mb-1">Estado del caso:</label>
                            <select name="estado" class="form-control form-control-sm">
                                <option value="abierto" {{ old('estado', $caso->estado) == 'abierto' ? 'selected' : '' }}>Abierto</option>
                                <option value="en_seguimiento" {{ old('estado', $caso->estado) == 'en_seguimiento' ? 'selected' : '' }}>En seguimiento</option>
                                <option value="cerrado" {{ old('estado', $caso->estado) == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold mb-1">Fecha de cierre:</label>
                            <input type="date" name="fecha_cierre" value="{{ old('fecha_cierre', $caso->fecha_cierre?->format('Y-m-d')) }}" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>

                <div class="page-footer">Página 3 de 3</div>
            </div>
        </div>
    </form>
</div>

<style>
/**
 * Estilos para vista editable de Acta de Convivencia
 * Hoja tamaño carta: 21.59 cm x 27.94 cm (8.5" x 11")
 * Margen izquierdo: 2.0 cm
 * Encabezado a 0.5 cm del borde superior
 */

.documento-carta {
    background: #f8f9fa;
    padding: 20px;
    min-height: 100vh;
}

.hoja-carta {
    width: 21.59cm;
    min-height: 27.94cm;
    background: white;
    margin: 0 auto 20px;
    padding-top: 0.5cm;
    padding-bottom: 1.5cm;
    padding-left: 2cm;
    padding-right: 1.5cm;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative;
    font-size: 9pt;
    line-height: 1.3;
}

/* Header institucional */
.header-table {
    width: 100%;
    border: 2px solid #000;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.header-table td {
    border: 2px solid #000;
    padding: 8px;
    vertical-align: middle;
}

.header-logo {
    width: 90px;
    text-align: center;
}

.header-logo img {
    max-width: 70px;
    height: auto;
}

.placeholder-logo {
    width: 70px;
    height: 70px;
    border: 2px solid #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 8pt;
    text-align: center;
    font-weight: bold;
}

.header-title {
    text-align: center;
    font-weight: bold;
    font-size: 10pt;
    line-height: 1.4;
}

.header-acta {
    width: 130px;
    text-align: center;
    font-weight: bold;
    font-size: 9pt;
}

.header-acta input {
    max-width: 100px;
    margin: 0 auto;
    font-size: 9pt;
}

/* Tablas de información */
.info-table {
    width: 100%;
    border: 2px solid #000;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.info-table td {
    border: 1px solid #000;
    padding: 4px 6px;
    font-size: 9pt;
}

.info-label {
    font-weight: bold;
    background-color: #f5f5f5;
}

.info-table input,
.info-table select,
.info-table textarea {
    font-size: 9pt;
    border: 1px solid #ced4da;
}

/* Content box */
.content-box {
    border: 2px solid #000;
    padding: 10px;
    margin-bottom: 8px;
    min-height: 350px;
}

.content-title {
    font-size: 9pt;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 10px;
    text-align: center;
}

.content-box label {
    font-size: 9pt;
}

.content-box textarea,
.content-box input {
    font-size: 9pt;
}

/* Secciones especiales */
.tipificacion-section {
    border-top: 1px dashed #999;
    padding-top: 10px;
    margin-top: 10px;
}

.suspension-section,
.psicologia-section,
.notificacion-section {
    border: 1px solid #dee2e6;
    padding: 8px;
    margin-bottom: 8px;
    background-color: #f8f9fa;
}

/* Compromisos */
.compromises-section {
    border: 2px solid #000;
    padding: 10px;
    margin-bottom: 8px;
}

.section-title {
    font-size: 9pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.compromise-box {
    margin-bottom: 10px;
}

.compromise-title {
    font-weight: bold;
    font-size: 9pt;
    margin-bottom: 4px;
}

.compromise-box textarea {
    font-size: 9pt;
}

/* Firmas */
.signatures-section {
    border: 2px solid #000;
    padding: 10px;
    margin-bottom: 8px;
}

.signatures-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.signatures-table td {
    border: 1px solid #000;
    padding: 8px;
    text-align: center;
    width: 50%;
    vertical-align: top;
}

.signature-space {
    height: 60px;
    border-bottom: 1px solid #999;
    margin-bottom: 5px;
}

.signature-label {
    font-size: 9pt;
    line-height: 1.3;
}

.signature-label strong {
    font-weight: bold;
}

.signature-label small {
    font-size: 8pt;
}

/* Observaciones generales */
.observaciones-section {
    border: 2px solid #000;
    padding: 8px;
    margin-bottom: 8px;
}

.observaciones-section textarea {
    font-size: 9pt;
}

/* Estado del caso */
.estado-section {
    padding: 8px;
    background-color: #fff3cd;
    border: 1px solid #ffc107;
}

/* Footer de página */
.page-footer {
    position: absolute;
    bottom: 0.5cm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 8pt;
    color: #666;
}

/* Saltos de página para impresión */
@media print {
    .no-print {
        display: none !important;
    }

    .documento-carta {
        background: white;
        padding: 0;
    }

    .hoja-carta {
        box-shadow: none;
        margin: 0;
        page-break-after: always;
    }

    .page-break {
        page-break-before: always;
    }

    /* Ocultar controles de formulario al imprimir */
    input[type="text"],
    input[type="date"],
    input[type="time"],
    input[type="number"],
    select,
    textarea {
        border: none !important;
        background: transparent !important;
        resize: none;
    }

    .form-check-input {
        display: none;
    }
}

/* Ajustes responsivos */
@media (max-width: 900px) {
    .hoja-carta {
        width: 100%;
        min-height: auto;
    }
}
</style>

<script>
// Toggle para mostrar/ocultar secciones condicionales
document.addEventListener('DOMContentLoaded', function() {
    // Ya están implementados los onchange en los checkboxes
    console.log('Acta editable cargada correctamente');
});
</script>
@endsection
