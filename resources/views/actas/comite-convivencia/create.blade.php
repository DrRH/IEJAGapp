@extends('layouts.tabler')

@section('title', 'Nueva Acta - Comité de Convivencia')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('actas.comite-convivencia.index') }}" class="text-muted">
                        <i class="ti ti-arrow-left me-2"></i>Comité de Convivencia
                    </a>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-file-plus me-2"></i>
                    Nueva Acta del Comité
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('actas.comite-convivencia.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                    <!-- Información Básica -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-info-circle me-2"></i>
                                Información de la Reunión
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Número de Acta</label>
                                    <input type="text" name="numero_acta"
                                           class="form-control @error('numero_acta') is-invalid @enderror"
                                           value="{{ old('numero_acta', $numeroActa) }}" readonly>
                                    <small class="form-hint">Generado automáticamente</small>
                                    @error('numero_acta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Fecha de Reunión</label>
                                    <input type="date" name="fecha_reunion"
                                           class="form-control @error('fecha_reunion') is-invalid @enderror"
                                           value="{{ old('fecha_reunion', date('Y-m-d')) }}" required>
                                    @error('fecha_reunion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Hora Inicio</label>
                                    <input type="time" name="hora_inicio"
                                           class="form-control @error('hora_inicio') is-invalid @enderror"
                                           value="{{ old('hora_inicio', '08:00') }}" required>
                                    @error('hora_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Hora Fin</label>
                                    <input type="time" name="hora_fin"
                                           class="form-control @error('hora_fin') is-invalid @enderror"
                                           value="{{ old('hora_fin') }}">
                                    @error('hora_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Lugar</label>
                                    <input type="text" name="lugar"
                                           class="form-control @error('lugar') is-invalid @enderror"
                                           placeholder="Ej: Sala de reuniones, Rectoría..."
                                           value="{{ old('lugar') }}" required>
                                    @error('lugar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label required">Resumen Ejecutivo</label>
                                    <textarea name="resumen_ejecutivo" rows="3"
                                              class="form-control @error('resumen_ejecutivo') is-invalid @enderror"
                                              placeholder="Breve resumen de la reunión (máximo 500 caracteres)..."
                                              maxlength="500" required>{{ old('resumen_ejecutivo') }}</textarea>
                                    <small class="form-hint">Este resumen aparecerá en la tabla de actas</small>
                                    @error('resumen_ejecutivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asistentes -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-users me-2"></i>
                                Asistentes
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label required">Asistentes (un nombre por línea)</label>
                                    <textarea name="asistentes" rows="6"
                                              class="form-control @error('asistentes') is-invalid @enderror"
                                              placeholder="Nombre Completo - Cargo&#10;Nombre Completo - Cargo&#10;..." required>{{ old('asistentes') }}</textarea>
                                    @error('asistentes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Invitados Especiales</label>
                                    <textarea name="invitados" rows="4"
                                              class="form-control @error('invitados') is-invalid @enderror"
                                              placeholder="Nombre Completo - Institución/Motivo (opcional)">{{ old('invitados') }}</textarea>
                                    @error('invitados')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orden del Día -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-list-check me-2"></i>
                                Orden del Día
                            </h3>
                        </div>
                        <div class="card-body">
                            <textarea name="orden_dia" rows="6"
                                      class="form-control @error('orden_dia') is-invalid @enderror"
                                      placeholder="1. Verificación del quórum&#10;2. Lectura y aprobación del acta anterior&#10;3. Revisión de casos..." required>{{ old('orden_dia') }}</textarea>
                            @error('orden_dia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Desarrollo -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-file-text me-2"></i>
                                Desarrollo de la Reunión
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Sugerencias en tiempo real -->
                            @include('components.ai-realtime-suggestions', [
                                'targetTextarea' => '#desarrollo-textarea',
                                'enabled' => true
                            ])

                            <textarea name="desarrollo" id="desarrollo-textarea" rows="12"
                                      class="form-control @error('desarrollo') is-invalid @enderror"
                                      placeholder="Descripción detallada del desarrollo de la reunión, discusiones, intervenciones..." required>{{ old('desarrollo') }}</textarea>
                            @error('desarrollo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Asistente de IA -->
                    @include('components.ai-assistant', [
                        'targetTextarea' => '#desarrollo-textarea',
                        'showContext' => true
                    ])

                    <!-- Decisiones y Compromisos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-checkbox me-2"></i>
                                Decisiones y Compromisos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Decisiones Tomadas</label>
                                    <textarea name="decisiones" rows="5"
                                              class="form-control @error('decisiones') is-invalid @enderror"
                                              placeholder="Decisiones acordadas por el comité...">{{ old('decisiones') }}</textarea>
                                    @error('decisiones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Compromisos Adquiridos</label>
                                    <textarea name="compromisos" rows="5"
                                              class="form-control @error('compromisos') is-invalid @enderror"
                                              placeholder="Compromisos establecidos por los miembros...">{{ old('compromisos') }}</textarea>
                                    @error('compromisos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Seguimiento a Compromisos Anteriores</label>
                                    <textarea name="seguimiento_compromisos_anteriores" rows="4"
                                              class="form-control @error('seguimiento_compromisos_anteriores') is-invalid @enderror"
                                              placeholder="Estado de compromisos de reuniones anteriores...">{{ old('seguimiento_compromisos_anteriores') }}</textarea>
                                    @error('seguimiento_compromisos_anteriores')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-notes me-2"></i>
                                Observaciones Generales
                            </h3>
                        </div>
                        <div class="card-body">
                            <textarea name="observaciones" rows="4"
                                      class="form-control @error('observaciones') is-invalid @enderror"
                                      placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Columna Lateral -->
                <div class="col-lg-4">
                    <!-- Casos Revisados -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-alert-triangle me-2"></i>
                                Casos Revisados
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($casosAbiertos->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">Seleccione los casos que se revisaron en esta reunión:</small>
                                </div>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    @foreach($casosAbiertos as $caso)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                   name="casos_revisados[]"
                                                   value="{{ $caso->id }}"
                                                   id="caso_{{ $caso->id }}"
                                                   @checked(is_array(old('casos_revisados')) && in_array($caso->id, old('casos_revisados')))>
                                            <label class="form-check-label" for="caso_{{ $caso->id }}">
                                                <strong>Caso #{{ $caso->id }}</strong> - {{ $caso->estudiante->nombre_completo }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $caso->tipoAnotacion->nombre }} - {{ $caso->fecha_reporte->format('d/m/Y') }}
                                                </small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-muted text-center py-3">
                                    <i class="ti ti-info-circle mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">No hay casos abiertos o en seguimiento</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Próxima Reunión -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-calendar-event me-2"></i>
                                Próxima Reunión
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Fecha Tentativa</label>
                                <input type="date" name="proxima_reunion"
                                       class="form-control @error('proxima_reunion') is-invalid @enderror"
                                       value="{{ old('proxima_reunion') }}">
                                @error('proxima_reunion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Temas a Tratar</label>
                                <textarea name="temas_proxima_reunion" rows="4"
                                          class="form-control @error('temas_proxima_reunion') is-invalid @enderror"
                                          placeholder="Temas propuestos para la próxima reunión...">{{ old('temas_proxima_reunion') }}</textarea>
                                @error('temas_proxima_reunion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Estado del Acta -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-status-change me-2"></i>
                                Estado del Acta
                            </h3>
                        </div>
                        <div class="card-body">
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="borrador" @selected(old('estado', 'borrador') == 'borrador')>Borrador</option>
                                <option value="aprobada" @selected(old('estado') == 'aprobada')>Aprobada</option>
                                <option value="publicada" @selected(old('estado') == 'publicada')>Publicada</option>
                            </select>
                            <small class="form-hint">Solo las actas aprobadas pueden ser publicadas</small>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Guardar Acta
                                </button>
                                <a href="{{ route('actas.comite-convivencia.index') }}" class="btn btn-outline-secondary">
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
