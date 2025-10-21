@extends('layouts.tabler')

@section('title', 'Detalle Situación de Convivencia #' . $caso->id)

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
                    Situación de Convivencia #{{ $caso->id }}
                </h2>
                <div class="text-muted mt-1">
                    {{ $caso->estudiante->nombre_completo }} | {{ $caso->fecha_reporte->format('d/m/Y') }}
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    <a href="{{ route('convivencia.casos.acta', $caso) }}" class="btn btn-primary">
                        <i class="ti ti-file-text me-2"></i>
                        Acta Editable
                    </a>
                    <a href="{{ route('convivencia.casos.pdf', $caso) }}" class="btn btn-success">
                        <i class="ti ti-file-download me-2"></i>
                        Descargar PDF
                    </a>
                    <a href="{{ route('convivencia.casos.print', $caso) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="ti ti-printer me-2"></i>
                        Vista Previa
                    </a>
                    <a href="{{ route('convivencia.casos.edit', $caso) }}" class="btn btn-outline-primary">
                        <i class="ti ti-edit me-2"></i>
                        Editar
                    </a>
                </div>
            </div>        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-3">
            <!-- Columna principal -->
            <div class="col-lg-8">
                <!-- Estado y Alertas -->
                <div class="card mb-3">
                    <div class="card-header bg-{{ $caso->color_estado }}-lt">
                        <h3 class="card-title">
                            <span class="badge bg-{{ $caso->color_estado }} me-2">
                                {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
                            </span>
                            Estado del Caso
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Días transcurridos</div>
                                <div class="h3 mb-0">{{ $caso->dias_transcurridos }} días</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Tipo de anotación</div>
                                <div class="h3 mb-0">
                                    <span class="badge bg-{{ $caso->tipoAnotacion->categoria === 'grave' ? 'danger' : ($caso->tipoAnotacion->categoria === 'leve' ? 'warning' : 'info') }}">
                                        {{ $caso->tipoAnotacion->nombre }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($caso->requiere_atencion_urgente)
                        <div class="alert alert-danger mt-3 mb-0">
                            <h4 class="alert-title">
                                <i class="ti ti-alert-triangle me-2"></i>
                                Requiere Atención Urgente
                            </h4>
                            <div class="text-secondary">
                                Este caso requiere atención inmediata debido a su gravedad o tiempo transcurrido.
                            </div>
                        </div>
                        @endif

                        @if($caso->en_suspension_activa)
                        <div class="alert alert-warning mt-3 mb-0">
                            <h4 class="alert-title">
                                <i class="ti ti-ban me-2"></i>
                                Suspensión Activa
                            </h4>
                            <div class="text-secondary">
                                El estudiante está actualmente suspendido hasta el {{ $caso->fecha_fin_suspension->format('d/m/Y') }}.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Información del Estudiante -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-user me-2"></i>
                            Información del Estudiante
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Nombre Completo</div>
                                <div class="fw-bold">{{ $caso->estudiante->nombre_completo }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Documento</div>
                                <div>{{ $caso->estudiante->tipo_documento }}: {{ $caso->estudiante->numero_documento }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Grado y Grupo</div>
                                <div>
                                    @if($caso->estudiante->matriculaActual)
                                        {{ $caso->estudiante->matriculaActual->grupo->grado->nombre }} - {{ $caso->estudiante->matriculaActual->grupo->nombre }}
                                    @else
                                        <span class="text-muted">Sin matrícula activa</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Sede</div>
                                <div>{{ $caso->estudiante->sede->nombre ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Acudiente</div>
                                <div>{{ $caso->estudiante->nombre_acudiente }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Teléfono Acudiente</div>
                                <div>{{ $caso->estudiante->telefono_acudiente }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estudiantes Involucrados -->
                @if($caso->numero_acta && ($caso->victimarios->count() > 0 || $caso->victimas->count() > 0))
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-users me-2"></i>
                            Estudiantes Involucrados - Acta N° {{ $caso->numero_acta }}
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($caso->victimarios->count() > 0)
                        <div class="mb-3">
                            <h4 class="text-muted mb-2">Presuntos Victimarios:</h4>
                            <div class="list-group">
                                @foreach($caso->victimarios as $victimario)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill">
                                            <div class="fw-bold">{{ $victimario->nombre_completo }}</div>
                                            <div class="text-muted small">
                                                {{ $victimario->tipo_documento }}: {{ $victimario->numero_documento }}
                                                @if($victimario->matriculaActual)
                                                    - {{ $victimario->matriculaActual->grupo->grado->nombre }} {{ $victimario->matriculaActual->grupo->nombre }}
                                                @endif
                                            </div>
                                        </div>
                                        <span class="badge bg-danger-lt">Victimario</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($caso->victimas->count() > 0)
                        <div>
                            <h4 class="text-muted mb-2">Presuntas Víctimas:</h4>
                            <div class="list-group">
                                @foreach($caso->victimas as $victima)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill">
                                            <div class="fw-bold">{{ $victima->nombre_completo }}</div>
                                            <div class="text-muted small">
                                                {{ $victima->tipo_documento }}: {{ $victima->numero_documento }}
                                                @if($victima->matriculaActual)
                                                    - {{ $victima->matriculaActual->grupo->grado->nombre }} {{ $victima->matriculaActual->grupo->nombre }}
                                                @endif
                                            </div>
                                        </div>
                                        <span class="badge bg-info-lt">Víctima</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @php
                            $reportesRelacionados = \App\Models\ReporteConvivencia::where('numero_acta', $caso->numero_acta)
                                ->where('id', '!=', $caso->id)
                                ->with('estudiante')
                                ->get();
                        @endphp

                        @if($reportesRelacionados->count() > 0)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            Este caso está relacionado con {{ $reportesRelacionados->count() }}
                            {{ $reportesRelacionados->count() === 1 ? 'otro caso' : 'otros casos' }}
                            bajo la misma acta:
                            <ul class="mb-0 mt-2">
                                @foreach($reportesRelacionados as $relacionado)
                                <li>
                                    <a href="{{ route('convivencia.casos.show', $relacionado) }}" class="text-primary">
                                        Caso #{{ $relacionado->id }} - {{ $relacionado->estudiante->nombre_completo }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Descripción del Caso -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-file-description me-2"></i>
                            Descripción de los Hechos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Fecha</div>
                                <div class="fw-bold">{{ $caso->fecha_reporte->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Hora</div>
                                <div>{{ $caso->hora_reporte?->format('H:i') ?? 'No especificada' }}</div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-muted small">Lugar</div>
                                <div>{{ $caso->lugar ?? 'No especificado' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small mb-2">Descripción Detallada</div>
                                <div class="p-3 bg-light rounded">
                                    {{ $caso->descripcion_hechos }}
                                </div>
                            </div>
                            @if($caso->testigos)
                            <div class="col-12">
                                <div class="text-muted small mb-2">Testigos</div>
                                <div class="p-3 bg-light rounded">
                                    {{ $caso->testigos }}
                                </div>
                            </div>
                            @endif
                            @if($caso->evidencias)
                            <div class="col-12">
                                <div class="text-muted small mb-2">Evidencias</div>
                                <div class="p-3 bg-light rounded">
                                    {{ $caso->evidencias }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Seguimiento -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-checklist me-2"></i>
                            Seguimiento y Acciones
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($caso->acciones_tomadas)
                        <div class="mb-3">
                            <div class="text-muted small mb-2">Acciones Tomadas</div>
                            <div class="p-3 bg-light rounded">
                                {{ $caso->acciones_tomadas }}
                            </div>
                        </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small">Acudiente Notificado</div>
                                <div>
                                    @if($caso->acudiente_notificado)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </div>
                            </div>

                            @if($caso->acudiente_notificado)
                            <div class="col-md-4">
                                <div class="text-muted small">Fecha Notificación</div>
                                <div>{{ $caso->fecha_notificacion_acudiente?->format('d/m/Y') ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Medio</div>
                                <div>{{ ucfirst($caso->medio_notificacion ?? 'N/A') }}</div>
                            </div>
                            @endif

                            @if($caso->respuesta_acudiente)
                            <div class="col-12">
                                <div class="text-muted small mb-2">Respuesta del Acudiente</div>
                                <div class="p-3 bg-light rounded">
                                    {{ $caso->respuesta_acudiente }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Observaciones Generales -->
                @if($caso->observaciones_generales)
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-notes me-2"></i>
                            Observaciones Generales
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded">
                            {{ $caso->observaciones_generales }}
                        </div>
                    </div>
                </div>
                @endif

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
                                <div class="text-muted small">Fecha de Cierre</div>
                                <div class="fw-bold">{{ $caso->fecha_cierre?->format('d/m/Y') ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Cerrado Por</div>
                                <div>{{ $caso->cerradoPor?->name ?? 'N/A' }}</div>
                            </div>
                            @if($caso->observaciones_cierre)
                            <div class="col-12">
                                <div class="text-muted small mb-2">Observaciones de Cierre</div>
                                <div class="p-3 bg-light rounded">
                                    {{ $caso->observaciones_cierre }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Columna lateral -->
            <div class="col-lg-4">
                <!-- Información del Reporte -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-info-circle me-2"></i>
                            Información del Reporte
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Reportado Por</div>
                            <div class="fw-bold">{{ $caso->reportadoPor->name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Fecha de Registro</div>
                            <div>{{ $caso->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div>
                            <div class="text-muted small">Última Actualización</div>
                            <div>{{ $caso->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Compromisos -->
                <div class="card mb-3 {{ $caso->requirio_compromiso ? 'border-primary' : '' }}">
                    <div class="card-header {{ $caso->requirio_compromiso ? 'bg-primary-lt' : '' }}">
                        <h3 class="card-title">
                            <i class="ti ti-file-check me-2"></i>
                            Compromisos
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($caso->requirio_compromiso)
                            <div class="mb-3">
                                <div class="text-muted small">Estado</div>
                                <div>
                                    @if($caso->compromiso_cumplido === null)
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @elseif($caso->compromiso_cumplido)
                                        <span class="badge bg-success">Cumplido</span>
                                    @else
                                        <span class="badge bg-danger">Incumplido</span>
                                    @endif
                                </div>
                            </div>
                            @if($caso->fecha_compromiso)
                            <div class="mb-3">
                                <div class="text-muted small">Fecha del Compromiso</div>
                                <div>{{ $caso->fecha_compromiso->format('d/m/Y') }}</div>
                            </div>
                            @endif
                            @if($caso->compromiso)
                            <div>
                                <div class="text-muted small mb-2">Descripción</div>
                                <div class="p-3 bg-light rounded">
                                    {{ $caso->compromiso }}
                                </div>
                            </div>
                            @endif
                        @else
                            <div class="text-muted text-center py-3">
                                No requirió compromiso
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Suspensión -->
                <div class="card mb-3 {{ $caso->requirio_suspension ? 'border-warning' : '' }}">
                    <div class="card-header {{ $caso->requirio_suspension ? 'bg-warning-lt' : '' }}">
                        <h3 class="card-title">
                            <i class="ti ti-ban me-2"></i>
                            Suspensión
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($caso->requirio_suspension)
                            <div class="mb-3">
                                <div class="text-muted small">Días de Suspensión</div>
                                <div class="h3 mb-0">{{ $caso->dias_suspension }}</div>
                            </div>
                            @if($caso->fecha_inicio_suspension && $caso->fecha_fin_suspension)
                            <div class="mb-3">
                                <div class="text-muted small">Período</div>
                                <div>
                                    {{ $caso->fecha_inicio_suspension->format('d/m/Y') }} - {{ $caso->fecha_fin_suspension->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif
                            @if($caso->en_suspension_activa)
                            <div class="alert alert-warning mb-0">
                                <i class="ti ti-alert-triangle me-2"></i>
                                Suspensión activa
                            </div>
                            @endif
                        @else
                            <div class="text-muted text-center py-3">
                                No requirió suspensión
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Remisión Psicología -->
                <div class="card mb-3 {{ $caso->remitido_psicologia ? 'border-info' : '' }}">
                    <div class="card-header {{ $caso->remitido_psicologia ? 'bg-info-lt' : '' }}">
                        <h3 class="card-title">
                            <i class="ti ti-brain me-2"></i>
                            Remisión Psicología
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($caso->remitido_psicologia)
                            @if($caso->fecha_remision_psicologia)
                            <div class="mb-3">
                                <div class="text-muted small">Fecha de Remisión</div>
                                <div>{{ $caso->fecha_remision_psicologia->format('d/m/Y') }}</div>
                            </div>
                            @endif
                            @if($caso->observaciones_psicologia)
                            <div>
                                <div class="text-muted small mb-2">Observaciones</div>
                                <div class="p-3 bg-light rounded">
                                    {{ $caso->observaciones_psicologia }}
                                </div>
                            </div>
                            @endif
                        @else
                            <div class="text-muted text-center py-3">
                                No remitido a psicología
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('convivencia.casos.edit', $caso) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-2"></i>
                                Editar Caso
                            </a>
                            <a href="{{ route('convivencia.casos.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-2"></i>
                                Volver al Listado
                            </a>
                            <form action="{{ route('convivencia.casos.destroy', $caso) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Está seguro de eliminar este caso? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="ti ti-trash me-2"></i>
                                    Eliminar Caso
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .page-header .d-print-none,
    .btn,
    .card:last-child {
        display: none !important;
    }

    .card {
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
    }
}
</style>
@endpush
