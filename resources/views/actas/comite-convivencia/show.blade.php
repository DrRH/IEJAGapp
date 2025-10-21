@extends('layouts.tabler')

@section('title', 'Acta ' . $acta->numero_acta)

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
                    <i class="ti ti-file-text me-2"></i>
                    Acta {{ $acta->numero_acta }}
                </h2>
                <div class="text-muted mt-1">
                    {{ $acta->fecha_reunion->format('d/m/Y') }} - {{ $acta->lugar }}
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    @if($acta->es_borrador)
                        <form action="{{ route('actas.comite-convivencia.aprobar', $acta) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar esta acta?')">
                                <i class="ti ti-check me-2"></i>
                                Aprobar
                            </button>
                        </form>
                        <a href="{{ route('actas.comite-convivencia.edit', $acta) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-2"></i>
                            Editar
                        </a>
                    @endif

                    @if($acta->esta_aprobada && !$acta->esta_publicada)
                        <form action="{{ route('actas.comite-convivencia.publicar', $acta) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info" onclick="return confirm('¿Publicar esta acta?')">
                                <i class="ti ti-upload me-2"></i>
                                Publicar
                            </button>
                        </form>
                    @endif

                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="ti ti-printer me-2"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-3">
            <!-- Columna Principal -->
            <div class="col-lg-9">
                <!-- Encabezado del Acta -->
                <div class="card mb-3">
                    <div class="card-body text-center" style="border-bottom: 3px solid #206bc4;">
                        <h3 class="mb-1">INSTITUCIÓN EDUCATIVA JOSÉ ANTONIO GALÁN</h3>
                        <h4 class="text-muted mb-2">COMITÉ ESCOLAR DE CONVIVENCIA</h4>
                        <h4 class="mb-0">ACTA {{ $acta->numero_acta }}</h4>
                    </div>
                </div>

                <!-- Información de la Reunión -->
                <div class="card mb-3">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">
                            <i class="ti ti-info-circle me-2"></i>
                            Información de la Reunión
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="text-muted small">Fecha</div>
                                <div class="fw-bold">{{ $acta->fecha_reunion->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-muted small">Hora Inicio</div>
                                <div class="fw-bold">{{ $acta->hora_inicio->format('H:i') }}</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-muted small">Hora Fin</div>
                                <div class="fw-bold">{{ $acta->hora_fin?->format('H:i') ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-muted small">Lugar</div>
                                <div class="fw-bold">{{ $acta->lugar }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen Ejecutivo -->
                <div class="card mb-3">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">
                            <i class="ti ti-file-description me-2"></i>
                            Resumen Ejecutivo
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $acta->resumen_ejecutivo }}</p>
                    </div>
                </div>

                <!-- Asistentes -->
                <div class="card mb-3">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">
                            <i class="ti ti-users me-2"></i>
                            Asistentes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-{{ $acta->lista_invitados ? '6' : '12' }}">
                                <h4 class="h5 mb-3">Miembros del Comité</h4>
                                <ul class="list-unstyled">
                                    @foreach($acta->lista_asistentes as $asistente)
                                        <li class="mb-1">
                                            <i class="ti ti-check text-success me-1"></i>
                                            {{ $asistente }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if($acta->lista_invitados)
                            <div class="col-md-6">
                                <h4 class="h5 mb-3">Invitados Especiales</h4>
                                <ul class="list-unstyled">
                                    @foreach($acta->lista_invitados as $invitado)
                                        <li class="mb-1">
                                            <i class="ti ti-user-plus text-info me-1"></i>
                                            {{ $invitado }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Orden del Día -->
                <div class="card mb-3">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">
                            <i class="ti ti-list-check me-2"></i>
                            Orden del Día
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line;">{{ $acta->orden_dia }}</div>
                    </div>
                </div>

                <!-- Desarrollo -->
                <div class="card mb-3">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">
                            <i class="ti ti-file-text me-2"></i>
                            Desarrollo de la Reunión
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line;">{{ $acta->desarrollo }}</div>
                    </div>
                </div>

                <!-- Casos Revisados -->
                @if($casosRevisados->count() > 0)
                <div class="card mb-3">
                    <div class="card-header bg-warning-lt">
                        <h3 class="card-title">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Casos Revisados ({{ $casosRevisados->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter mb-0">
                                <thead>
                                    <tr>
                                        <th>Caso #</th>
                                        <th>Estudiante</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($casosRevisados as $caso)
                                        <tr>
                                            <td>
                                                <a href="{{ route('convivencia.casos.show', $caso) }}" class="text-primary">
                                                    #{{ $caso->id }}
                                                </a>
                                            </td>
                                            <td>{{ $caso->estudiante->nombre_completo }}</td>
                                            <td>
                                                <span class="badge bg-{{ $caso->tipoAnotacion->categoria === 'grave' ? 'danger' : 'warning' }}">
                                                    {{ $caso->tipoAnotacion->nombre }}
                                                </span>
                                            </td>
                                            <td>{{ $caso->fecha_reporte->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $caso->color_estado }}">
                                                    {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Decisiones -->
                @if($acta->decisiones)
                <div class="card mb-3">
                    <div class="card-header bg-success-lt">
                        <h3 class="card-title">
                            <i class="ti ti-checkbox me-2"></i>
                            Decisiones Tomadas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line;">{{ $acta->decisiones }}</div>
                    </div>
                </div>
                @endif

                <!-- Compromisos -->
                @if($acta->compromisos)
                <div class="card mb-3">
                    <div class="card-header bg-info-lt">
                        <h3 class="card-title">
                            <i class="ti ti-checklist me-2"></i>
                            Compromisos Adquiridos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line;">{{ $acta->compromisos }}</div>
                    </div>
                </div>
                @endif

                <!-- Seguimiento Compromisos Anteriores -->
                @if($acta->seguimiento_compromisos_anteriores)
                <div class="card mb-3">
                    <div class="card-header bg-secondary-lt">
                        <h3 class="card-title">
                            <i class="ti ti-history me-2"></i>
                            Seguimiento a Compromisos Anteriores
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line;">{{ $acta->seguimiento_compromisos_anteriores }}</div>
                    </div>
                </div>
                @endif

                <!-- Próxima Reunión -->
                @if($acta->proxima_reunion)
                <div class="card mb-3">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">
                            <i class="ti ti-calendar-event me-2"></i>
                            Próxima Reunión
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="text-muted small">Fecha Tentativa</div>
                                <div class="fw-bold">{{ $acta->proxima_reunion->format('d/m/Y') }}</div>
                            </div>
                            @if($acta->temas_proxima_reunion)
                            <div class="col-md-12">
                                <div class="text-muted small mb-2">Temas a Tratar</div>
                                <div style="white-space: pre-line;">{{ $acta->temas_proxima_reunion }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Observaciones -->
                @if($acta->observaciones)
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-notes me-2"></i>
                            Observaciones Generales
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-line;">{{ $acta->observaciones }}</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-3">
                <!-- Estado -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-status-change me-2"></i>
                            Estado
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="mb-0">
                            <span class="badge bg-{{ $acta->color_estado }}" style="font-size: 1.2rem;">
                                {{ ucfirst($acta->estado) }}
                            </span>
                        </h2>
                    </div>
                </div>

                <!-- Información de Creación -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-user me-2"></i>
                            Creación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Creado por</div>
                            <div class="fw-bold">{{ $acta->creadoPor->name }}</div>
                        </div>
                        <div>
                            <div class="text-muted small">Fecha</div>
                            <div>{{ $acta->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Información de Aprobación -->
                @if($acta->esta_aprobada || $acta->esta_publicada)
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success-lt">
                        <h3 class="card-title">
                            <i class="ti ti-check me-2"></i>
                            Aprobación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Aprobado por</div>
                            <div class="fw-bold">{{ $acta->aprobadoPor?->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-muted small">Fecha</div>
                            <div>{{ $acta->fecha_aprobacion?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Estadísticas -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-chart-bar me-2"></i>
                            Estadísticas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Asistentes</div>
                            <div class="h3 mb-0">{{ count($acta->lista_asistentes) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Invitados</div>
                            <div class="h3 mb-0">{{ count($acta->lista_invitados) }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Casos Revisados</div>
                            <div class="h3 mb-0">{{ $acta->cantidad_casos }}</div>
                        </div>
                        @if($acta->duracion_reunion)
                        <div>
                            <div class="text-muted small">Duración</div>
                            <div class="h3 mb-0">{{ $acta->duracion_reunion }} min</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($acta->es_borrador)
                            <a href="{{ route('actas.comite-convivencia.edit', $acta) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-2"></i>
                                Editar Acta
                            </a>
                            @endif

                            <a href="{{ route('actas.comite-convivencia.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-2"></i>
                                Volver al Listado
                            </a>

                            @if($acta->es_borrador)
                            <form action="{{ route('actas.comite-convivencia.destroy', $acta) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Está seguro de eliminar esta acta? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="ti ti-trash me-2"></i>
                                    Eliminar Acta
                                </button>
                            </form>
                            @endif
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
    .card:last-child,
    .col-lg-3 {
        display: none !important;
    }

    .col-lg-9 {
        width: 100% !important;
        max-width: 100% !important;
    }

    .card {
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
        margin-bottom: 1rem !important;
    }

    body {
        font-size: 12pt;
    }
}
</style>
@endpush
