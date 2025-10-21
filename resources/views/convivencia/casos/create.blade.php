@extends('layouts.tabler')

@section('title', 'Registrar Situación de Convivencia')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('convivencia.casos.index') }}" class="text-muted">
                        <i class="ti ti-arrow-left me-2"></i>Atención a Situaciones de Convivencia
                    </a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Registrar Nueva Situación de Convivencia
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('convivencia.casos.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <!-- Columna principal -->
                <div class="col-lg-8">
                    <!-- Información básica del caso -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-info-circle me-2"></i>
                                Información del Caso
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="alert alert-info mb-3">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <strong>Importante:</strong> Seleccione uno o más estudiantes como presuntos victimarios.
                                        Se creará un acta individual para cada victimario con el mismo número de caso.
                                    </div>
                                </div>

                                <!-- Filtros de búsqueda -->
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="card-title mb-3">
                                                <i class="ti ti-filter me-2"></i>Filtros de Búsqueda
                                            </h4>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">Buscar por nombre o documento</label>
                                                    <input type="text" id="filtro-busqueda" class="form-control"
                                                           placeholder="Escriba para buscar...">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Filtrar por Grado</label>
                                                    <select id="filtro-grado" class="form-select">
                                                        <option value="">Todos los grados</option>
                                                        @foreach($grados as $grado)
                                                            <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Filtrar por Grupo</label>
                                                    <select id="filtro-grupo" class="form-select" disabled>
                                                        <option value="">Todos los grupos</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selector de estudiantes disponibles -->
                                <div class="col-12">
                                    <label class="form-label">Estudiantes Disponibles</label>
                                    <div class="card" style="max-height: 300px; overflow-y: auto;">
                                        <div class="list-group list-group-flush" id="lista-estudiantes-disponibles">
                                            @foreach($estudiantes as $estudiante)
                                                <div class="list-group-item estudiante-item"
                                                     data-estudiante-id="{{ $estudiante->id }}"
                                                     data-estudiante-nombre="{{ strtolower($estudiante->nombre_completo) }}"
                                                     data-estudiante-documento="{{ $estudiante->numero_documento }}"
                                                     data-estudiante-grado="{{ $estudiante->matriculaActual->grupo->grado_id ?? '' }}"
                                                     data-estudiante-grupo="{{ $estudiante->matriculaActual->grupo_id ?? '' }}">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="flex-fill">
                                                            <div class="fw-bold">{{ $estudiante->nombre_completo }}</div>
                                                            <div class="text-muted small">
                                                                Doc: {{ $estudiante->numero_documento }}
                                                                @if($estudiante->matriculaActual)
                                                                    | {{ $estudiante->matriculaActual->grupo->grado->nombre }} - {{ $estudiante->matriculaActual->grupo->nombre }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-danger btn-agregar-victimario"
                                                                    data-id="{{ $estudiante->id }}"
                                                                    data-nombre="{{ $estudiante->nombre_completo }}"
                                                                    data-documento="{{ $estudiante->numero_documento }}"
                                                                    data-info="{{ $estudiante->matriculaActual ? $estudiante->matriculaActual->grupo->grado->nombre . ' - ' . $estudiante->matriculaActual->grupo->nombre : 'Sin matrícula' }}">
                                                                <i class="ti ti-user-x me-1"></i>Victimario
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-info btn-agregar-victima"
                                                                    data-id="{{ $estudiante->id }}"
                                                                    data-nombre="{{ $estudiante->nombre_completo }}"
                                                                    data-documento="{{ $estudiante->numero_documento }}"
                                                                    data-info="{{ $estudiante->matriculaActual ? $estudiante->matriculaActual->grupo->grado->nombre . ' - ' . $estudiante->matriculaActual->grupo->nombre : 'Sin matrícula' }}">
                                                                <i class="ti ti-user-check me-1"></i>Víctima
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div id="no-resultados" class="alert alert-warning mt-2" style="display: none;">
                                        No se encontraron estudiantes con los criterios de búsqueda.
                                    </div>
                                </div>

                                <!-- Listas de estudiantes seleccionados -->
                                <div class="col-md-6">
                                    <label class="form-label required">
                                        Presuntos Victimarios Seleccionados
                                        <span class="badge bg-danger ms-2" id="contador-victimarios">0</span>
                                    </label>
                                    <div class="card">
                                        <div class="list-group list-group-flush" id="lista-victimarios-seleccionados">
                                            <div class="list-group-item text-muted text-center" id="victimarios-vacio">
                                                <i class="ti ti-users me-2"></i>No hay victimarios seleccionados
                                            </div>
                                        </div>
                                    </div>
                                    @error('victimarios')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Presuntas Víctimas Seleccionadas (opcional)
                                        <span class="badge bg-info ms-2" id="contador-victimas">0</span>
                                    </label>
                                    <div class="card">
                                        <div class="list-group list-group-flush" id="lista-victimas-seleccionadas">
                                            <div class="list-group-item text-muted text-center" id="victimas-vacio">
                                                <i class="ti ti-users me-2"></i>No hay víctimas seleccionadas
                                            </div>
                                        </div>
                                    </div>
                                    @error('victimas')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Campos hidden para enviar los datos -->
                                <div id="victimarios-hidden-container"></div>
                                <div id="victimas-hidden-container"></div>

                                <!-- Tipo de Situación -->
                                <div class="col-md-6">
                                    <label class="form-label required">Tipo de Situación</label>
                                    <select name="tipo_situacion" id="tipo-situacion-select"
                                            class="form-select @error('tipo_situacion') is-invalid @enderror" required>
                                        <option value="">Seleccione un tipo...</option>
                                        <option value="tipo_i" @selected(old('tipo_situacion') == 'tipo_i')>Tipo I - Situaciones que afectan la convivencia</option>
                                        <option value="tipo_ii" @selected(old('tipo_situacion') == 'tipo_ii')>Tipo II - Situaciones de alto riesgo</option>
                                        <option value="tipo_iii" @selected(old('tipo_situacion') == 'tipo_iii')>Tipo III - Situaciones que constituyen delito</option>
                                    </select>
                                    @error('tipo_situacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Númerales del Manual de Convivencia -->
                                <div class="col-12" id="numerales-container" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <label class="form-label required">
                                                <i class="ti ti-list-numbers me-2"></i>
                                                Númerales del Manual de Convivencia
                                            </label>
                                            <div id="numerales-lista" class="row g-2">
                                                <!-- Se llenará dinámicamente con JavaScript -->
                                            </div>
                                            <div id="no-numerales" class="alert alert-info mt-3" style="display: none;">
                                                <i class="ti ti-info-circle me-2"></i>
                                                No hay númerales registrados para este tipo de situación.
                                            </div>
                                        </div>
                                    </div>
                                    @error('numerales')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Númerales seleccionados -->
                                <div class="col-12" id="numerales-seleccionados-container" style="display: none;">
                                    <label class="form-label">
                                        Númerales Seleccionados
                                        <span class="badge bg-primary ms-2" id="contador-numerales">0</span>
                                    </label>
                                    <div class="card">
                                        <div class="list-group list-group-flush" id="lista-numerales-seleccionados">
                                            <div class="list-group-item text-muted text-center" id="numerales-vacio">
                                                <i class="ti ti-list-check me-2"></i>No hay númerales seleccionados
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos hidden para númerales -->
                                <div id="numerales-hidden-container"></div>

                                <div class="col-md-6">
                                    <label class="form-label">Número de Acta</label>
                                    <input type="text" name="numero_acta"
                                           class="form-control @error('numero_acta') is-invalid @enderror"
                                           placeholder="Ej: 001-2025"
                                           value="{{ old('numero_acta') }}">
                                    <small class="form-hint">Si se deja vacío, se generará automáticamente</small>
                                    @error('numero_acta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Fecha del Reporte</label>
                                    <input type="date" name="fecha_reporte"
                                           class="form-control @error('fecha_reporte') is-invalid @enderror"
                                           value="{{ old('fecha_reporte', date('Y-m-d')) }}" required>
                                    @error('fecha_reporte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Hora del Reporte</label>
                                    <input type="time" name="hora_reporte"
                                           class="form-control @error('hora_reporte') is-invalid @enderror"
                                           value="{{ old('hora_reporte') }}">
                                    @error('hora_reporte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Lugar</label>
                                    <input type="text" name="lugar"
                                           class="form-control @error('lugar') is-invalid @enderror"
                                           placeholder="Ej: Salón 201, patio, cafetería..."
                                           value="{{ old('lugar') }}">
                                    @error('lugar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label required">Descripción de los Hechos</label>
                                    <textarea name="descripcion_hechos" rows="5"
                                              class="form-control @error('descripcion_hechos') is-invalid @enderror"
                                              placeholder="Describa detalladamente lo sucedido..." required>{{ old('descripcion_hechos') }}</textarea>
                                    @error('descripcion_hechos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Testigos</label>
                                    <textarea name="testigos" rows="3"
                                              class="form-control @error('testigos') is-invalid @enderror"
                                              placeholder="Nombres de testigos presenciales...">{{ old('testigos') }}</textarea>
                                    @error('testigos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Evidencias</label>
                                    <textarea name="evidencias" rows="3"
                                              class="form-control @error('evidencias') is-invalid @enderror"
                                              placeholder="Descripción de evidencias físicas, fotográficas, etc...">{{ old('evidencias') }}</textarea>
                                    @error('evidencias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seguimiento y Acciones -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-checklist me-2"></i>
                                Seguimiento y Acciones
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Acciones Tomadas</label>
                                    <textarea name="acciones_tomadas" rows="4"
                                              class="form-control @error('acciones_tomadas') is-invalid @enderror"
                                              placeholder="Describa las acciones inmediatas tomadas...">{{ old('acciones_tomadas') }}</textarea>
                                    @error('acciones_tomadas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <input type="checkbox" name="acudiente_notificado" value="1"
                                               class="form-check-input me-2" @checked(old('acudiente_notificado'))>
                                        Acudiente Notificado
                                    </label>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Fecha Notificación</label>
                                    <input type="date" name="fecha_notificacion_acudiente"
                                           class="form-control @error('fecha_notificacion_acudiente') is-invalid @enderror"
                                           value="{{ old('fecha_notificacion_acudiente') }}">
                                    @error('fecha_notificacion_acudiente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Medio de Notificación</label>
                                    <select name="medio_notificacion" class="form-select @error('medio_notificacion') is-invalid @enderror">
                                        <option value="">Seleccione...</option>
                                        <option value="presencial" @selected(old('medio_notificacion') == 'presencial')>Presencial</option>
                                        <option value="llamada" @selected(old('medio_notificacion') == 'llamada')>Llamada telefónica</option>
                                        <option value="email" @selected(old('medio_notificacion') == 'email')>Email</option>
                                        <option value="whatsapp" @selected(old('medio_notificacion') == 'whatsapp')>WhatsApp</option>
                                        <option value="otro" @selected(old('medio_notificacion') == 'otro')>Otro</option>
                                    </select>
                                    @error('medio_notificacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Respuesta del Acudiente</label>
                                    <textarea name="respuesta_acudiente" rows="3"
                                              class="form-control @error('respuesta_acudiente') is-invalid @enderror"
                                              placeholder="Respuesta o comentarios del acudiente...">{{ old('respuesta_acudiente') }}</textarea>
                                    @error('respuesta_acudiente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones Generales -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-notes me-2"></i>
                                Observaciones Generales
                            </h3>
                        </div>
                        <div class="card-body">
                            <textarea name="observaciones_generales" rows="4"
                                      class="form-control @error('observaciones_generales') is-invalid @enderror"
                                      placeholder="Observaciones adicionales o contexto relevante...">{{ old('observaciones_generales') }}</textarea>
                            @error('observaciones_generales')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Columna lateral -->
                <div class="col-lg-4">
                    <!-- Compromisos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-file-check me-2"></i>
                                Compromisos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">
                                    <input type="checkbox" name="requirio_compromiso" value="1"
                                           class="form-check-input me-2" @checked(old('requirio_compromiso'))>
                                    Requirió Compromiso
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha del Compromiso</label>
                                <input type="date" name="fecha_compromiso"
                                       class="form-control @error('fecha_compromiso') is-invalid @enderror"
                                       value="{{ old('fecha_compromiso') }}">
                                @error('fecha_compromiso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción del Compromiso</label>
                                <textarea name="compromiso" rows="4"
                                          class="form-control @error('compromiso') is-invalid @enderror"
                                          placeholder="Describa el compromiso adquirido...">{{ old('compromiso') }}</textarea>
                                @error('compromiso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Estado del Compromiso</label>
                                <select name="compromiso_cumplido" class="form-select @error('compromiso_cumplido') is-invalid @enderror">
                                    <option value="">Pendiente</option>
                                    <option value="1" @selected(old('compromiso_cumplido') == '1')>Cumplido</option>
                                    <option value="0" @selected(old('compromiso_cumplido') == '0')>Incumplido</option>
                                </select>
                                @error('compromiso_cumplido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Suspensión -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-ban me-2"></i>
                                Suspensión
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">
                                    <input type="checkbox" name="requirio_suspension" value="1"
                                           class="form-check-input me-2" @checked(old('requirio_suspension'))>
                                    Requirió Suspensión
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Días de Suspensión</label>
                                <input type="number" name="dias_suspension" min="0"
                                       class="form-control @error('dias_suspension') is-invalid @enderror"
                                       value="{{ old('dias_suspension', 0) }}">
                                @error('dias_suspension')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio_suspension"
                                       class="form-control @error('fecha_inicio_suspension') is-invalid @enderror"
                                       value="{{ old('fecha_inicio_suspension') }}">
                                @error('fecha_inicio_suspension')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin_suspension"
                                       class="form-control @error('fecha_fin_suspension') is-invalid @enderror"
                                       value="{{ old('fecha_fin_suspension') }}">
                                @error('fecha_fin_suspension')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Remisión Psicología -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-brain me-2"></i>
                                Remisión Psicología
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">
                                    <input type="checkbox" name="remitido_psicologia" value="1"
                                           class="form-check-input me-2" @checked(old('remitido_psicologia'))>
                                    Remitido a Psicología
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha de Remisión</label>
                                <input type="date" name="fecha_remision_psicologia"
                                       class="form-control @error('fecha_remision_psicologia') is-invalid @enderror"
                                       value="{{ old('fecha_remision_psicologia') }}">
                                @error('fecha_remision_psicologia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Observaciones Psicología</label>
                                <textarea name="observaciones_psicologia" rows="4"
                                          class="form-control @error('observaciones_psicologia') is-invalid @enderror"
                                          placeholder="Observaciones del departamento de psicología...">{{ old('observaciones_psicologia') }}</textarea>
                                @error('observaciones_psicologia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Estado del Caso -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-status-change me-2"></i>
                                Estado del Caso
                            </h3>
                        </div>
                        <div class="card-body">
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                                <option value="abierto" @selected(old('estado', 'abierto') == 'abierto')>Abierto</option>
                                <option value="en_seguimiento" @selected(old('estado') == 'en_seguimiento')>En Seguimiento</option>
                                <option value="cerrado" @selected(old('estado') == 'cerrado')>Cerrado</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Guardar Caso
                                </button>
                                <a href="{{ route('convivencia.casos.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-2"></i>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Arrays para almacenar estudiantes seleccionados
    let victimarios = [];
    let victimas = [];
    let numeralesSeleccionados = [];

    // Datos de grupos por grado (se cargarán dinámicamente)
    const gruposPorGrado = @json($gruposPorGrado ?? []);

    // Númerales por tipo de situación
    const numeralesPorTipo = @json($numeralesPorTipo ?? []);

    // Elementos del DOM
    const filtroBusqueda = document.getElementById('filtro-busqueda');
    const filtroGrado = document.getElementById('filtro-grado');
    const filtroGrupo = document.getElementById('filtro-grupo');
    const listaDisponibles = document.getElementById('lista-estudiantes-disponibles');
    const noResultados = document.getElementById('no-resultados');

    // Filtro por grado - cargar grupos
    filtroGrado.addEventListener('change', function() {
        const gradoId = this.value;
        filtroGrupo.innerHTML = '<option value="">Todos los grupos</option>';

        if (gradoId && gruposPorGrado[gradoId]) {
            filtroGrupo.disabled = false;
            gruposPorGrado[gradoId].forEach(grupo => {
                const option = document.createElement('option');
                option.value = grupo.id;
                option.textContent = grupo.nombre;
                filtroGrupo.appendChild(option);
            });
        } else {
            filtroGrupo.disabled = true;
        }

        aplicarFiltros();
    });

    // Filtro por grupo
    filtroGrupo.addEventListener('change', aplicarFiltros);

    // Filtro por búsqueda de texto
    filtroBusqueda.addEventListener('input', aplicarFiltros);

    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const textoBusqueda = filtroBusqueda.value.toLowerCase();
        const gradoSeleccionado = filtroGrado.value;
        const grupoSeleccionado = filtroGrupo.value;

        const items = listaDisponibles.querySelectorAll('.estudiante-item');
        let visibles = 0;

        items.forEach(item => {
            const nombre = item.dataset.estudianteNombre;
            const documento = item.dataset.estudianteDocumento;
            const grado = item.dataset.estudianteGrado;
            const grupo = item.dataset.estudianteGrupo;

            let mostrar = true;

            // Filtro de texto
            if (textoBusqueda && !nombre.includes(textoBusqueda) && !documento.includes(textoBusqueda)) {
                mostrar = false;
            }

            // Filtro de grado
            if (gradoSeleccionado && grado !== gradoSeleccionado) {
                mostrar = false;
            }

            // Filtro de grupo
            if (grupoSeleccionado && grupo !== grupoSeleccionado) {
                mostrar = false;
            }

            item.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        noResultados.style.display = visibles === 0 ? 'block' : 'none';
    }

    // Agregar victimario
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-agregar-victimario')) {
            const btn = e.target.closest('.btn-agregar-victimario');
            const id = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const documento = btn.dataset.documento;
            const info = btn.dataset.info;

            // Verificar si ya está agregado
            if (!victimarios.find(v => v.id === id)) {
                victimarios.push({ id, nombre, documento, info });
                actualizarListaVictimarios();
                ocultarEstudiante(id);
            }
        }
    });

    // Agregar víctima
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-agregar-victima')) {
            const btn = e.target.closest('.btn-agregar-victima');
            const id = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const documento = btn.dataset.documento;
            const info = btn.dataset.info;

            // Verificar si ya está agregado
            if (!victimas.find(v => v.id === id)) {
                victimas.push({ id, nombre, documento, info });
                actualizarListaVictimas();
                ocultarEstudiante(id);
            }
        }
    });

    // Remover victimario
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remover-victimario')) {
            const btn = e.target.closest('.btn-remover-victimario');
            const id = btn.dataset.id;

            victimarios = victimarios.filter(v => v.id !== id);
            actualizarListaVictimarios();
            mostrarEstudiante(id);
        }
    });

    // Remover víctima
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remover-victima')) {
            const btn = e.target.closest('.btn-remover-victima');
            const id = btn.dataset.id;

            victimas = victimas.filter(v => v.id !== id);
            actualizarListaVictimas();
            mostrarEstudiante(id);
        }
    });

    // Ocultar estudiante de la lista de disponibles
    function ocultarEstudiante(id) {
        const item = listaDisponibles.querySelector(`[data-estudiante-id="${id}"]`);
        if (item) {
            item.style.display = 'none';
        }
    }

    // Mostrar estudiante en la lista de disponibles
    function mostrarEstudiante(id) {
        const item = listaDisponibles.querySelector(`[data-estudiante-id="${id}"]`);
        if (item) {
            item.style.display = '';
            aplicarFiltros(); // Reaplicar filtros por si el estudiante no cumple con los criterios actuales
        }
    }

    // Actualizar lista de victimarios
    function actualizarListaVictimarios() {
        const lista = document.getElementById('lista-victimarios-seleccionados');
        const vacio = document.getElementById('victimarios-vacio');
        const contador = document.getElementById('contador-victimarios');
        const container = document.getElementById('victimarios-hidden-container');

        if (victimarios.length === 0) {
            vacio.style.display = '';
            contador.textContent = '0';
            container.innerHTML = '';
            return;
        }

        vacio.style.display = 'none';
        contador.textContent = victimarios.length;

        // Generar HTML de la lista
        let html = '';
        victimarios.forEach((estudiante, index) => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-fill">
                            <div class="fw-bold">${estudiante.nombre}</div>
                            <div class="text-muted small">Doc: ${estudiante.documento} | ${estudiante.info}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remover-victimario" data-id="${estudiante.id}">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        // Actualizar la lista visual (sin el elemento vacio)
        const items = lista.querySelectorAll('.list-group-item:not(#victimarios-vacio)');
        items.forEach(item => item.remove());
        lista.insertAdjacentHTML('beforeend', html);

        // Actualizar campos hidden
        container.innerHTML = '';
        victimarios.forEach(estudiante => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'victimarios[]';
            input.value = estudiante.id;
            container.appendChild(input);
        });
    }

    // Actualizar lista de víctimas
    function actualizarListaVictimas() {
        const lista = document.getElementById('lista-victimas-seleccionadas');
        const vacio = document.getElementById('victimas-vacio');
        const contador = document.getElementById('contador-victimas');
        const container = document.getElementById('victimas-hidden-container');

        if (victimas.length === 0) {
            vacio.style.display = '';
            contador.textContent = '0';
            container.innerHTML = '';
            return;
        }

        vacio.style.display = 'none';
        contador.textContent = victimas.length;

        // Generar HTML de la lista
        let html = '';
        victimas.forEach((estudiante, index) => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-fill">
                            <div class="fw-bold">${estudiante.nombre}</div>
                            <div class="text-muted small">Doc: ${estudiante.documento} | ${estudiante.info}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remover-victima" data-id="${estudiante.id}">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        // Actualizar la lista visual (sin el elemento vacio)
        const items = lista.querySelectorAll('.list-group-item:not(#victimas-vacio)');
        items.forEach(item => item.remove());
        lista.insertAdjacentHTML('beforeend', html);

        // Actualizar campos hidden
        container.innerHTML = '';
        victimas.forEach(estudiante => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'victimas[]';
            input.value = estudiante.id;
            container.appendChild(input);
        });
    }

    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        if (victimarios.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos un estudiante como presunto victimario.');
            return false;
        }
    });

    // ===========================================
    // MANEJO DE TIPO DE SITUACIÓN Y NÚMERALES
    // ===========================================

    const tipoSituacionSelect = document.getElementById('tipo-situacion-select');
    const numeralesContainer = document.getElementById('numerales-container');
    const numeralesLista = document.getElementById('numerales-lista');
    const noNumerales = document.getElementById('no-numerales');
    const numeralesSeleccionadosContainer = document.getElementById('numerales-seleccionados-container');

    // Cuando cambia el tipo de situación
    tipoSituacionSelect.addEventListener('change', function() {
        const tipoSituacion = this.value;

        if (!tipoSituacion) {
            numeralesContainer.style.display = 'none';
            numeralesSeleccionadosContainer.style.display = 'none';
            numeralesLista.innerHTML = '';
            numeralesSeleccionados = [];
            actualizarListaNumerales();
            return;
        }

        // Mostrar contenedor de númerales
        numeralesContainer.style.display = 'block';
        numeralesSeleccionadosContainer.style.display = 'block';

        // Limpiar selección anterior
        numeralesSeleccionados = [];
        actualizarListaNumerales();

        // Cargar númerales del tipo seleccionado
        if (numeralesPorTipo[tipoSituacion] && numeralesPorTipo[tipoSituacion].length > 0) {
            noNumerales.style.display = 'none';
            numeralesLista.innerHTML = '';

            numeralesPorTipo[tipoSituacion].forEach(numeral => {
                const col = document.createElement('div');
                col.className = 'col-md-6';

                col.innerHTML = `
                    <div class="card card-sm cursor-pointer numeral-item" data-numeral-id="${numeral.id}">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-fill">
                                    <div class="fw-bold text-primary">${numeral.numeral || 'N/A'}</div>
                                    <div class="text-muted small mt-1">${numeral.nombre}</div>
                                    ${numeral.descripcion ? '<div class="text-muted small mt-1">' + numeral.descripcion.substring(0, 100) + (numeral.descripcion.length > 100 ? '...' : '') + '</div>' : ''}
                                </div>
                                <button type="button" class="btn btn-sm btn-primary btn-agregar-numeral ms-2"
                                        data-numeral-id="${numeral.id}"
                                        data-numeral-codigo="${numeral.numeral || 'N/A'}"
                                        data-numeral-nombre="${numeral.nombre}">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                numeralesLista.appendChild(col);
            });
        } else {
            numeralesLista.innerHTML = '';
            noNumerales.style.display = 'block';
        }
    });

    // Agregar numeral
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-agregar-numeral')) {
            const btn = e.target.closest('.btn-agregar-numeral');
            const id = btn.dataset.numeralId;
            const codigo = btn.dataset.numeralCodigo;
            const nombre = btn.dataset.numeralNombre;

            // Verificar si ya está agregado
            if (!numeralesSeleccionados.find(n => n.id === id)) {
                numeralesSeleccionados.push({ id, codigo, nombre });
                actualizarListaNumerales();

                // Deshabilitar el botón
                btn.disabled = true;
                btn.innerHTML = '<i class="ti ti-check"></i>';
            }
        }
    });

    // Remover numeral
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remover-numeral')) {
            const btn = e.target.closest('.btn-remover-numeral');
            const id = btn.dataset.numeralId;

            numeralesSeleccionados = numeralesSeleccionados.filter(n => n.id !== id);
            actualizarListaNumerales();

            // Habilitar el botón en la lista
            const agregarBtn = document.querySelector(`.btn-agregar-numeral[data-numeral-id="${id}"]`);
            if (agregarBtn) {
                agregarBtn.disabled = false;
                agregarBtn.innerHTML = '<i class="ti ti-plus"></i>';
            }
        }
    });

    // Actualizar lista de númerales seleccionados
    function actualizarListaNumerales() {
        const lista = document.getElementById('lista-numerales-seleccionados');
        const vacio = document.getElementById('numerales-vacio');
        const contador = document.getElementById('contador-numerales');
        const container = document.getElementById('numerales-hidden-container');

        if (numeralesSeleccionados.length === 0) {
            vacio.style.display = '';
            contador.textContent = '0';
            container.innerHTML = '';
            return;
        }

        vacio.style.display = 'none';
        contador.textContent = numeralesSeleccionados.length;

        // Generar HTML de la lista
        let html = '';
        numeralesSeleccionados.forEach(numeral => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-fill">
                            <span class="badge bg-primary me-2">${numeral.codigo}</span>
                            <span class="fw-bold">${numeral.nombre}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remover-numeral"
                                data-numeral-id="${numeral.id}">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        // Actualizar la lista visual
        const items = lista.querySelectorAll('.list-group-item:not(#numerales-vacio)');
        items.forEach(item => item.remove());
        lista.insertAdjacentHTML('beforeend', html);

        // Actualizar campos hidden
        container.innerHTML = '';
        numeralesSeleccionados.forEach(numeral => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'numerales[]';
            input.value = numeral.id;
            container.appendChild(input);
        });
    }
});
</script>
@endpush

@endsection
