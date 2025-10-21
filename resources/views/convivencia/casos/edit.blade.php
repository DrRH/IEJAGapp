@extends('layouts.tabler')

@section('title', 'Editar Situación de Convivencia')

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
                    <i class="ti ti-edit me-2"></i>
                    Editar Situación de Convivencia #{{ $caso->id }}
                </h2>
                <div class="text-muted mt-1">
                    Estudiante: {{ $caso->estudiante->nombre_completo }} | Fecha: {{ $caso->fecha_reporte->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('convivencia.casos.update', $caso) }}" method="POST">
            @csrf
            @method('PUT')

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
                                <div class="col-md-6">
                                    <label class="form-label required">Estudiante</label>
                                    <select name="estudiante_id" class="form-select @error('estudiante_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un estudiante...</option>
                                        @foreach($estudiantes as $estudiante)
                                            <option value="{{ $estudiante->id }}"
                                                    @selected(old('estudiante_id', $caso->estudiante_id) == $estudiante->id)>
                                                {{ $estudiante->nombre_completo }} - {{ $estudiante->numero_documento }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('estudiante_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Tipo de Anotación</label>
                                    <select name="tipo_anotacion_id" class="form-select @error('tipo_anotacion_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un tipo...</option>
                                        @foreach($tiposAnotacion as $tipo)
                                            <option value="{{ $tipo->id }}"
                                                    @selected(old('tipo_anotacion_id', $caso->tipo_anotacion_id) == $tipo->id)>
                                                {{ $tipo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipo_anotacion_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Fecha del Reporte</label>
                                    <input type="date" name="fecha_reporte"
                                           class="form-control @error('fecha_reporte') is-invalid @enderror"
                                           value="{{ old('fecha_reporte', $caso->fecha_reporte->format('Y-m-d')) }}" required>
                                    @error('fecha_reporte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Hora del Reporte</label>
                                    <input type="time" name="hora_reporte"
                                           class="form-control @error('hora_reporte') is-invalid @enderror"
                                           value="{{ old('hora_reporte', $caso->hora_reporte?->format('H:i')) }}">
                                    @error('hora_reporte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Lugar</label>
                                    <input type="text" name="lugar"
                                           class="form-control @error('lugar') is-invalid @enderror"
                                           placeholder="Ej: Salón 201, patio, cafetería..."
                                           value="{{ old('lugar', $caso->lugar) }}">
                                    @error('lugar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label required">Descripción de los Hechos</label>
                                    <textarea name="descripcion_hechos" rows="5"
                                              class="form-control @error('descripcion_hechos') is-invalid @enderror"
                                              placeholder="Describa detalladamente lo sucedido..." required>{{ old('descripcion_hechos', $caso->descripcion_hechos) }}</textarea>
                                    @error('descripcion_hechos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Testigos</label>
                                    <textarea name="testigos" rows="3"
                                              class="form-control @error('testigos') is-invalid @enderror"
                                              placeholder="Nombres de testigos presenciales...">{{ old('testigos', $caso->testigos) }}</textarea>
                                    @error('testigos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Evidencias</label>
                                    <textarea name="evidencias" rows="3"
                                              class="form-control @error('evidencias') is-invalid @enderror"
                                              placeholder="Descripción de evidencias físicas, fotográficas, etc...">{{ old('evidencias', $caso->evidencias) }}</textarea>
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
                                              placeholder="Describa las acciones inmediatas tomadas...">{{ old('acciones_tomadas', $caso->acciones_tomadas) }}</textarea>
                                    @error('acciones_tomadas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <input type="checkbox" name="acudiente_notificado" value="1"
                                               class="form-check-input me-2"
                                               @checked(old('acudiente_notificado', $caso->acudiente_notificado))>
                                        Acudiente Notificado
                                    </label>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Fecha Notificación</label>
                                    <input type="date" name="fecha_notificacion_acudiente"
                                           class="form-control @error('fecha_notificacion_acudiente') is-invalid @enderror"
                                           value="{{ old('fecha_notificacion_acudiente', $caso->fecha_notificacion_acudiente?->format('Y-m-d')) }}">
                                    @error('fecha_notificacion_acudiente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Medio de Notificación</label>
                                    <select name="medio_notificacion" class="form-select @error('medio_notificacion') is-invalid @enderror">
                                        <option value="">Seleccione...</option>
                                        <option value="presencial" @selected(old('medio_notificacion', $caso->medio_notificacion) == 'presencial')>Presencial</option>
                                        <option value="llamada" @selected(old('medio_notificacion', $caso->medio_notificacion) == 'llamada')>Llamada telefónica</option>
                                        <option value="email" @selected(old('medio_notificacion', $caso->medio_notificacion) == 'email')>Email</option>
                                        <option value="whatsapp" @selected(old('medio_notificacion', $caso->medio_notificacion) == 'whatsapp')>WhatsApp</option>
                                        <option value="otro" @selected(old('medio_notificacion', $caso->medio_notificacion) == 'otro')>Otro</option>
                                    </select>
                                    @error('medio_notificacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Respuesta del Acudiente</label>
                                    <textarea name="respuesta_acudiente" rows="3"
                                              class="form-control @error('respuesta_acudiente') is-invalid @enderror"
                                              placeholder="Respuesta o comentarios del acudiente...">{{ old('respuesta_acudiente', $caso->respuesta_acudiente) }}</textarea>
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
                                      placeholder="Observaciones adicionales o contexto relevante...">{{ old('observaciones_generales', $caso->observaciones_generales) }}</textarea>
                            @error('observaciones_generales')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Cierre del Caso -->
                    @if($caso->estado === 'cerrado')
                    <div class="card mb-3 border-success">
                        <div class="card-header bg-success-lt">
                            <h3 class="card-title">
                                <i class="ti ti-check me-2"></i>
                                Cierre del Caso
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Cierre</label>
                                    <input type="date" name="fecha_cierre"
                                           class="form-control @error('fecha_cierre') is-invalid @enderror"
                                           value="{{ old('fecha_cierre', $caso->fecha_cierre?->format('Y-m-d')) }}">
                                    @error('fecha_cierre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Cerrado Por</label>
                                    <input type="text" class="form-control" value="{{ $caso->cerradoPor?->name ?? 'N/A' }}" disabled>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Observaciones de Cierre</label>
                                    <textarea name="observaciones_cierre" rows="4"
                                              class="form-control @error('observaciones_cierre') is-invalid @enderror"
                                              placeholder="Observaciones finales del caso...">{{ old('observaciones_cierre', $caso->observaciones_cierre) }}</textarea>
                                    @error('observaciones_cierre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
                                           class="form-check-input me-2"
                                           @checked(old('requirio_compromiso', $caso->requirio_compromiso))>
                                    Requirió Compromiso
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha del Compromiso</label>
                                <input type="date" name="fecha_compromiso"
                                       class="form-control @error('fecha_compromiso') is-invalid @enderror"
                                       value="{{ old('fecha_compromiso', $caso->fecha_compromiso?->format('Y-m-d')) }}">
                                @error('fecha_compromiso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción del Compromiso</label>
                                <textarea name="compromiso" rows="4"
                                          class="form-control @error('compromiso') is-invalid @enderror"
                                          placeholder="Describa el compromiso adquirido...">{{ old('compromiso', $caso->compromiso) }}</textarea>
                                @error('compromiso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Estado del Compromiso</label>
                                <select name="compromiso_cumplido" class="form-select @error('compromiso_cumplido') is-invalid @enderror">
                                    <option value="">Pendiente</option>
                                    <option value="1" @selected(old('compromiso_cumplido', $caso->compromiso_cumplido) == '1')>Cumplido</option>
                                    <option value="0" @selected(old('compromiso_cumplido', $caso->compromiso_cumplido) == '0')>Incumplido</option>
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
                                           class="form-check-input me-2"
                                           @checked(old('requirio_suspension', $caso->requirio_suspension))>
                                    Requirió Suspensión
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Días de Suspensión</label>
                                <input type="number" name="dias_suspension" min="0"
                                       class="form-control @error('dias_suspension') is-invalid @enderror"
                                       value="{{ old('dias_suspension', $caso->dias_suspension) }}">
                                @error('dias_suspension')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio_suspension"
                                       class="form-control @error('fecha_inicio_suspension') is-invalid @enderror"
                                       value="{{ old('fecha_inicio_suspension', $caso->fecha_inicio_suspension?->format('Y-m-d')) }}">
                                @error('fecha_inicio_suspension')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin_suspension"
                                       class="form-control @error('fecha_fin_suspension') is-invalid @enderror"
                                       value="{{ old('fecha_fin_suspension', $caso->fecha_fin_suspension?->format('Y-m-d')) }}">
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
                                           class="form-check-input me-2"
                                           @checked(old('remitido_psicologia', $caso->remitido_psicologia))>
                                    Remitido a Psicología
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha de Remisión</label>
                                <input type="date" name="fecha_remision_psicologia"
                                       class="form-control @error('fecha_remision_psicologia') is-invalid @enderror"
                                       value="{{ old('fecha_remision_psicologia', $caso->fecha_remision_psicologia?->format('Y-m-d')) }}">
                                @error('fecha_remision_psicologia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Observaciones Psicología</label>
                                <textarea name="observaciones_psicologia" rows="4"
                                          class="form-control @error('observaciones_psicologia') is-invalid @enderror"
                                          placeholder="Observaciones del departamento de psicología...">{{ old('observaciones_psicologia', $caso->observaciones_psicologia) }}</textarea>
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
                                <option value="abierto" @selected(old('estado', $caso->estado) == 'abierto')>Abierto</option>
                                <option value="en_seguimiento" @selected(old('estado', $caso->estado) == 'en_seguimiento')>En Seguimiento</option>
                                <option value="cerrado" @selected(old('estado', $caso->estado) == 'cerrado')>Cerrado</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Si cambia a "Cerrado", asegúrese de completar las observaciones de cierre.</small>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="card mb-3 bg-blue-lt">
                        <div class="card-body">
                            <div class="text-muted small">
                                <div class="mb-2">
                                    <strong>Creado:</strong> {{ $caso->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="mb-2">
                                    <strong>Última actualización:</strong> {{ $caso->updated_at->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <strong>Reportado por:</strong> {{ $caso->reportadoPor->name }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Actualizar Caso
                                </button>
                                <a href="{{ route('convivencia.casos.show', $caso) }}" class="btn btn-outline-info">
                                    <i class="ti ti-eye me-2"></i>
                                    Ver Detalles
                                </a>
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
@endsection
