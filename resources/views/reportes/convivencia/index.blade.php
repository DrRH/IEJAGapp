@extends('layouts.tabler')

@section('title', 'Reportes de Convivencia')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-report me-2"></i>
                    Reportes de Convivencia
                </h2>
                <div class="text-muted mt-1">Estadísticas e indicadores de convivencia escolar</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    <a href="{{ route('reportes.convivencia.export-csv', request()->query()) }}" class="btn btn-outline-primary">
                        <i class="ti ti-file-spreadsheet me-2"></i>
                        Exportar CSV
                    </a>
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
        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-filter me-2"></i>
                    Filtros de Búsqueda
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reportes.convivencia.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" name="fecha_desde" class="form-control"
                                   value="{{ $fechaDesde }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control"
                                   value="{{ $fechaHasta }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Sede (Ctrl + clic)</label>
                            <select name="sede_id[]" class="form-select" multiple size="3">
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}"
                                            @selected(is_array(request('sede_id')) && in_array($sede->id, request('sede_id')))>
                                        {{ $sede->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mantén Ctrl para seleccionar varios</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Grado (Ctrl + clic)</label>
                            <select name="grado_id[]" class="form-select" multiple size="3">
                                @foreach($grados as $grado)
                                    <option value="{{ $grado->id }}"
                                            @selected(is_array(request('grado_id')) && in_array($grado->id, request('grado_id')))>
                                        {{ $grado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mantén Ctrl para seleccionar varios</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tipo (Ctrl + clic)</label>
                            <select name="tipo_anotacion_id[]" class="form-select" multiple size="3">
                                @foreach($tiposAnotacion as $tipo)
                                    <option value="{{ $tipo->id }}"
                                            @selected(is_array(request('tipo_anotacion_id')) && in_array($tipo->id, request('tipo_anotacion_id')))>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mantén Ctrl para seleccionar varios</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado (Ctrl + clic)</label>
                            <select name="estado[]" class="form-select" multiple size="3">
                                <option value="abierto" @selected(is_array(request('estado')) && in_array('abierto', request('estado')))>Abierto</option>
                                <option value="en_seguimiento" @selected(is_array(request('estado')) && in_array('en_seguimiento', request('estado')))>En Seguimiento</option>
                                <option value="cerrado" @selected(is_array(request('estado')) && in_array('cerrado', request('estado')))>Cerrado</option>
                            </select>
                            <small class="text-muted">Mantén Ctrl para seleccionar varios</small>
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search me-2"></i>Aplicar Filtros
                                </button>
                                <a href="{{ route('reportes.convivencia.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-2"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Casos</div>
                        </div>
                        <div class="h1 mb-0">{{ $totalCasos }}</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Casos Abiertos</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $casosAbiertos }}</div>
                            @if($totalCasos > 0)
                                <div class="me-auto">
                                    <span class="badge bg-danger-lt">{{ round(($casosAbiertos / $totalCasos) * 100, 1) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Casos Cerrados</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $casosCerrados }}</div>
                            @if($totalCasos > 0)
                                <div class="me-auto">
                                    <span class="badge bg-success-lt">{{ round(($casosCerrados / $totalCasos) * 100, 1) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Promedio Días Cierre</div>
                        </div>
                        <div class="h1 mb-0">{{ round($promedioDiasCierre ?? 0, 1) }}</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Con Suspensión</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $casosConSuspension }}</div>
                            @if($totalCasos > 0)
                                <div class="me-auto">
                                    <span class="badge bg-warning-lt">{{ round(($casosConSuspension / $totalCasos) * 100, 1) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Remitidos Psicología</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $casosRemitidosPsicologia }}</div>
                            @if($totalCasos > 0)
                                <div class="me-auto">
                                    <span class="badge bg-info-lt">{{ round(($casosRemitidosPsicologia / $totalCasos) * 100, 1) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Con Compromiso</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $casosConCompromiso }}</div>
                            @if($totalCasos > 0)
                                <div class="me-auto">
                                    <span class="badge bg-primary-lt">{{ round(($casosConCompromiso / $totalCasos) * 100, 1) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Casos por Tipo de Anotación</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPorTipo" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Casos por Grado</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPorGrado" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Casos por Sede</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPorSede" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Evolución Mensual (Últimos 6 meses)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPorMes" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 10 Estudiantes -->
        @if($estudiantesConMasReportes->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-trophy me-2"></i>
                    Top 10 Estudiantes con Más Reportes
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Estudiante</th>
                                <th class="text-center">Documento</th>
                                <th class="text-center">Grado</th>
                                <th class="text-center">Total Reportes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantesConMasReportes as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $item['estudiante'] }}</td>
                                    <td class="text-center">{{ $item['documento'] }}</td>
                                    <td class="text-center">{{ $item['grado'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $item['total'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Listado Detallado -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    Listado Detallado de Casos ({{ $casos->total() }})
                </h3>
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 me-2">Filas por página:</label>
                    <select class="form-select form-select-sm" id="per-page-selector" style="width: auto;">
                        <option value="10" @selected(request('per_page') == 10)>10</option>
                        <option value="20" @selected(request('per_page', 20) == 20)>20</option>
                        <option value="50" @selected(request('per_page') == 50)>50</option>
                        <option value="100" @selected(request('per_page') == 100)>100</option>
                        <option value="custom">Personalizado...</option>
                    </select>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover">
                        <thead style="position: sticky; top: 0; background: white; z-index: 100;">
                            <tr>
                                <th class="text-center w-1">#</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-start">Estudiante</th>
                                <th class="text-center">Grado</th>
                                <th class="text-center">Sede</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Reportado Por</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center w-1">Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($casos as $index => $caso)
                                <tr>
                                    <td class="text-center text-muted">
                                        {{ ($casos->currentPage() - 1) * $casos->perPage() + $index + 1 }}
                                    </td>
                                    <td class="text-center">{{ $caso->fecha_reporte->format('d/m/Y') }}</td>
                                    <td class="text-start">
                                        <div class="fw-bold">{{ $caso->estudiante->nombre_completo }}</div>
                                        <div class="text-muted small">{{ $caso->estudiante->numero_documento }}</div>
                                    </td>
                                    <td class="text-center">{{ $caso->estudiante->matriculaActual?->grupo?->grado?->nombre ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $caso->estudiante->sede?->nombre ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $caso->tipoAnotacion->categoria === 'grave' ? 'danger' : ($caso->tipoAnotacion->categoria === 'leve' ? 'warning' : 'info') }}">
                                            {{ $caso->tipoAnotacion->nombre }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $caso->reportadoPor->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $caso->color_estado }}">
                                            {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('convivencia.casos.show', $caso) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver detalles">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="ti ti-alert-circle" style="font-size: 3rem;"></i>
                                        <div class="mt-2">No se encontraron casos en el rango de fechas seleccionado</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($casos->hasPages())
            <div class="card-footer d-flex align-items-center">
                <div class="me-auto">
                    <p class="m-0 text-muted">
                        Mostrando <strong>{{ $casos->firstItem() }}</strong> a <strong>{{ $casos->lastItem() }}</strong> de <strong>{{ $casos->total() }}</strong> casos
                    </p>
                </div>
                {{ $casos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selector de filas por página
    const perPageSelector = document.getElementById('per-page-selector');
    if (perPageSelector) {
        perPageSelector.addEventListener('change', function() {
            if (this.value === 'custom') {
                const customValue = prompt('Ingrese la cantidad de filas a mostrar:', '100');
                if (customValue && !isNaN(customValue) && customValue > 0) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('per_page', customValue);
                    window.location.href = url.toString();
                } else {
                    this.value = '{{ request('per_page', 20) }}';
                }
            } else {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            }
        });
    }

    // Gráfico: Casos por Tipo
    const ctxTipo = document.getElementById('chartPorTipo');
    if (ctxTipo) {
        new Chart(ctxTipo, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($casosPorTipo->pluck('tipo')) !!},
                datasets: [{
                    data: {!! json_encode($casosPorTipo->pluck('total')) !!},
                    backgroundColor: [
                        '#dc3545', '#ffc107', '#0d6efd', '#20c997', '#6610f2'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Gráfico: Casos por Grado
    const ctxGrado = document.getElementById('chartPorGrado');
    if (ctxGrado) {
        new Chart(ctxGrado, {
            type: 'bar',
            data: {
                labels: {!! json_encode($casosPorGrado->pluck('grado')) !!},
                datasets: [{
                    label: 'Casos',
                    data: {!! json_encode($casosPorGrado->pluck('total')) !!},
                    backgroundColor: '#206bc4'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Gráfico: Casos por Sede
    const ctxSede = document.getElementById('chartPorSede');
    if (ctxSede) {
        new Chart(ctxSede, {
            type: 'pie',
            data: {
                labels: {!! json_encode($casosPorSede->pluck('sede')) !!},
                datasets: [{
                    data: {!! json_encode($casosPorSede->pluck('total')) !!},
                    backgroundColor: [
                        '#206bc4', '#4299e1', '#63b3ed', '#90cdf4', '#bee3f8'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Gráfico: Evolución Mensual
    const ctxMes = document.getElementById('chartPorMes');
    if (ctxMes) {
        new Chart(ctxMes, {
            type: 'line',
            data: {
                labels: {!! json_encode($casosPorMes->pluck('mes')) !!},
                datasets: [{
                    label: 'Casos',
                    data: {!! json_encode($casosPorMes->pluck('total')) !!},
                    borderColor: '#206bc4',
                    backgroundColor: 'rgba(32, 107, 196, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .page-header .d-print-none,
    .btn,
    .card-footer {
        display: none !important;
    }

    .card {
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
    }

    canvas {
        max-height: 200px !important;
    }
}
</style>
@endpush
