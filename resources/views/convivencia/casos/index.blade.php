@extends('layouts.tabler')

@section('title', 'Atención a Situaciones de Convivencia')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Atención a Situaciones de Convivencia
                </h2>
                <div class="text-muted mt-1">Gestión de situaciones de convivencia escolar</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    <a href="{{ route('convivencia.casos.export', request()->query()) }}" class="btn btn-outline-primary">
                        <i class="ti ti-download me-2"></i>
                        Exportar CSV
                    </a>
                    <a href="{{ route('convivencia.casos.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Nuevo Caso
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('convivencia.casos.index') }}" id="filtros-form">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Nombre o documento del estudiante..."
                                   value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Estado (Ctrl + clic para múltiple)</label>
                            <select name="estado[]" id="filtro-estado" class="form-select" multiple size="3">
                                <option value="abierto" @selected(is_array(request('estado')) && in_array('abierto', request('estado')))>Abierto</option>
                                <option value="en_seguimiento" @selected(is_array(request('estado')) && in_array('en_seguimiento', request('estado')))>En Seguimiento</option>
                                <option value="cerrado" @selected(is_array(request('estado')) && in_array('cerrado', request('estado')))>Cerrado</option>
                            </select>
                            <small class="text-muted">Mantén Ctrl (Cmd en Mac) para seleccionar varios</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tipo (Ctrl + clic para múltiple)</label>
                            <select name="tipo_anotacion_id[]" id="filtro-tipo" class="form-select" multiple size="3">
                                @foreach($tiposAnotacion as $tipo)
                                    <option value="{{ $tipo->id }}"
                                            @selected(is_array(request('tipo_anotacion_id')) && in_array($tipo->id, request('tipo_anotacion_id')))>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mantén Ctrl (Cmd en Mac) para seleccionar varios</small>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" name="fecha_desde" class="form-control"
                                   value="{{ request('fecha_desde') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control"
                                   value="{{ request('fecha_hasta') }}">
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search me-2"></i>Filtrar
                                </button>
                                <a href="{{ route('convivencia.casos.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-2"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de casos -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    Lista de Casos ({{ $casos->total() }})
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
                <div class="table-responsive" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                    <table class="table table-vcenter table-hover" id="tabla-casos">
                        <thead style="position: sticky; top: 0; background: white; z-index: 100;">
                            <tr>
                                <th class="text-center w-1">#</th>
                                <th class="text-center">Acta N°</th>
                                <th class="text-center sortable" data-sort="fecha_reporte">
                                    Fecha
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-start sortable" data-sort="estudiante_id">
                                    Estudiante
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-center">Documento</th>
                                <th class="text-center sortable" data-sort="tipo_anotacion_id">
                                    Tipo Anotación
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center sortable" data-sort="reportado_por">
                                    Reportado Por
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-center sortable" data-sort="estado">
                                    Estado
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-center w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($casos as $index => $caso)
                                <tr class="caso-row"
                                    data-caso-id="{{ $caso->id }}"
                                    data-caso-fecha="{{ $caso->fecha_reporte->format('d/m/Y') }}"
                                    data-caso-estudiante="{{ $caso->estudiante->nombre_completo }}"
                                    data-caso-documento="{{ $caso->estudiante->numero_documento }}"
                                    data-caso-tipo="{{ $caso->tipoAnotacion->nombre }}"
                                    data-caso-descripcion="{{ Str::limit($caso->descripcion_hechos, 100) }}"
                                    data-caso-lugar="{{ $caso->lugar ?? 'N/A' }}"
                                    data-caso-reportado="{{ $caso->reportadoPor->name }}"
                                    data-caso-estado="{{ ucfirst(str_replace('_', ' ', $caso->estado)) }}"
                                    data-caso-estado-class="{{ $caso->color_estado }}"
                                    data-caso-acudiente="{{ $caso->acudiente_notificado ? 'Sí' : 'No' }}"
                                    data-caso-suspension="{{ $caso->requirio_suspension ? 'Sí' : 'No' }}"
                                    data-caso-psicologia="{{ $caso->remitido_psicologia ? 'Sí' : 'No' }}"
                                    data-caso-compromiso="{{ $caso->requirio_compromiso ? 'Sí' : 'No' }}"
                                    data-caso-acciones="{{ $caso->acciones_tomadas ?? 'Sin acciones registradas' }}"
                                    data-caso-observaciones="{{ $caso->observaciones_generales ?? 'Sin observaciones' }}"
                                    style="cursor: pointer; transition: all 0.3s ease;">
                                    <td class="text-center text-muted">
                                        {{ ($casos->currentPage() - 1) * $casos->perPage() + $index + 1 }}
                                    </td>
                                    <td class="text-center">
                                        @if($caso->numero_acta)
                                            <span class="badge bg-blue-lt">{{ $caso->numero_acta }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $caso->fecha_reporte->format('d/m/Y') }}</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold">{{ $caso->estudiante->nombre_completo }}</div>
                                                <div class="text-muted small">{{ $caso->estudiante->sede->nombre ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $caso->estudiante->numero_documento }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $caso->tipoAnotacion->categoria === 'grave' ? 'danger' : ($caso->tipoAnotacion->categoria === 'leve' ? 'warning' : 'info') }}">
                                            {{ $caso->tipoAnotacion->nombre }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-truncate" style="max-width: 250px;" title="{{ $caso->descripcion_hechos }}">
                                            {{ Str::limit($caso->descripcion_hechos, 50) }}
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $caso->reportadoPor->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $caso->color_estado }}">
                                            {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('convivencia.casos.show', $caso) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver detalles"
                                               onclick="event.stopPropagation();">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('convivencia.casos.edit', $caso) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Editar"
                                               onclick="event.stopPropagation();">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('convivencia.casos.destroy', $caso) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onclick="event.stopPropagation();"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este caso?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="ti ti-alert-circle" style="font-size: 3rem;"></i>
                                        <div class="mt-2">No se encontraron casos disciplinarios</div>
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

<!-- Overlay oscuro -->
<div id="caso-overlay" style="display: none;"></div>

<!-- Tarjeta flotante 3D -->
<div id="caso-card-3d" style="display: none;">
    <button id="close-card-3d" class="close-card-btn" title="Cerrar (Esc)">
        <i class="ti ti-x"></i>
    </button>

    <div class="caso-card-inner">
        <div class="caso-card-header">
            <h3 id="card-titulo">Caso Disciplinario</h3>
        </div>

        <div class="caso-card-grid">
            <!-- Columna 1: Datos del Caso -->
            <div class="info-section">
                <div class="section-title">DATOS DEL CASO</div>
                <div class="section-details">
                    <div class="detail-item">
                        <small>Caso #</small>
                        <div id="card-caso-id" class="fw-bold"></div>
                    </div>
                    <div class="detail-item">
                        <small>Fecha</small>
                        <div id="card-fecha"></div>
                    </div>
                    <div class="detail-item">
                        <small>Tipo</small>
                        <div id="card-tipo"></div>
                    </div>
                    <div class="detail-item">
                        <small>Estado</small>
                        <div id="card-estado"></div>
                    </div>
                    <div class="detail-item">
                        <small>Lugar</small>
                        <div id="card-lugar"></div>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Datos del Estudiante -->
            <div class="info-section">
                <div class="section-title">ESTUDIANTE</div>
                <div class="section-details">
                    <div class="detail-item">
                        <small>Nombre</small>
                        <div id="card-estudiante"></div>
                    </div>
                    <div class="detail-item">
                        <small>Documento</small>
                        <div id="card-documento"></div>
                    </div>
                    <div class="detail-item">
                        <small>Reportado Por</small>
                        <div id="card-reportado"></div>
                    </div>
                </div>
            </div>

            <!-- Columna 3: Seguimiento -->
            <div class="info-section">
                <div class="section-title">SEGUIMIENTO</div>
                <div class="section-details">
                    <div class="detail-item">
                        <small>Acudiente Notificado</small>
                        <div id="card-acudiente"></div>
                    </div>
                    <div class="detail-item">
                        <small>Requirió Suspensión</small>
                        <div id="card-suspension"></div>
                    </div>
                    <div class="detail-item">
                        <small>Remitido Psicología</small>
                        <div id="card-psicologia"></div>
                    </div>
                    <div class="detail-item">
                        <small>Requirió Compromiso</small>
                        <div id="card-compromiso"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripción completa -->
        <div class="caso-card-descripcion">
            <div class="section-title">DESCRIPCIÓN DE LOS HECHOS</div>
            <div id="card-descripcion" class="descripcion-text"></div>
        </div>

        <!-- Acciones tomadas -->
        <div class="caso-card-descripcion">
            <div class="section-title">ACCIONES TOMADAS</div>
            <div id="card-acciones" class="descripcion-text"></div>
        </div>

        <!-- Observaciones -->
        <div class="caso-card-descripcion">
            <div class="section-title">OBSERVACIONES GENERALES</div>
            <div id="card-observaciones" class="descripcion-text"></div>
        </div>

        <!-- Botón Ver más -->
        <a href="#" id="caso-card-ver-mas" class="caso-card-ver-mas" title="Ver información completa del caso">
            <i class="ti ti-eye me-2"></i>Ver más
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Overlay oscuro */
#caso-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    z-index: 1040;
    transition: opacity 0.3s ease;
}

/* Contenedor de la tarjeta 3D */
#caso-card-3d {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) perspective(1500px) rotateX(0deg) scale(0.9);
    width: 90%;
    max-width: 1200px;
    max-height: 85vh;
    overflow-y: auto;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    box-shadow:
        0 25px 50px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(0, 204, 255, 0.3),
        0 0 30px rgba(0, 204, 255, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    z-index: 1050;
    opacity: 0;
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border: 2px solid rgba(0, 204, 255, 0.5);
}

#caso-card-3d.show {
    opacity: 1;
    transform: translate(-50%, -50%) perspective(1500px) rotateX(0deg) scale(1);
}

/* Botón cerrar */
.close-card-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.9);
    border: 2px solid rgba(255, 255, 255, 0.8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.close-card-btn:hover {
    background: rgba(220, 53, 69, 1);
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 6px 12px rgba(220, 53, 69, 0.4);
}

/* Interior de la tarjeta */
.caso-card-inner {
    padding: 30px;
    position: relative;
}

.caso-card-header {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 3px solid rgba(0, 204, 255, 0.3);
}

.caso-card-header h3 {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Grid de 3 columnas */
.caso-card-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(280px, 1fr));
    gap: 25px;
    width: 100%;
    margin-bottom: 20px;
}

.info-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-width: 0;
}

.section-title {
    font-size: 14px;
    font-weight: 700;
    color: #0033cc;
    margin: 0 0 8px 0;
    padding-bottom: 8px;
    border-bottom: 2px solid rgba(0, 51, 204, 0.5);
    text-shadow: 0 0 8px rgba(0, 51, 204, 0.9),
                 0 0 15px rgba(0, 51, 204, 0.7),
                 0 0 20px rgba(0, 51, 204, 0.5);
}

.section-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item small {
    display: block;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    font-weight: 700;
    color: #ff6600;
    text-shadow: 0 0 8px rgba(255, 102, 0, 0.8),
                 0 2px 4px rgba(0, 0, 0, 0.5);
}

.detail-item div {
    font-size: 15px;
    color: #ffff00;
    line-height: 1.4;
    word-wrap: break-word;
    text-shadow: 0 0 6px rgba(255, 255, 0, 0.8),
                 0 2px 4px rgba(0, 0, 0, 0.5);
    text-transform: capitalize;
}

/* Área de descripción */
.caso-card-descripcion {
    margin-top: 20px;
    padding: 15px;
    background: rgba(0, 0, 0, 0.03);
    border-radius: 10px;
    border-left: 4px solid rgba(0, 204, 255, 0.5);
}

.descripcion-text {
    font-size: 14px;
    color: #ffff00;
    line-height: 1.6;
    text-shadow: 0 0 6px rgba(255, 255, 0, 0.8),
                 0 2px 4px rgba(0, 0, 0, 0.5);
}

/* Botón Ver más */
.caso-card-ver-mas {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #206bc4 0%, #1a56a0 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(32, 107, 196, 0.3);
}

.caso-card-ver-mas:hover {
    background: linear-gradient(135deg, #1a56a0 0%, #14477f 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(32, 107, 196, 0.4);
    color: white;
}

/* Separación de filas */
.row-above-selected {
    transform: translateY(-300px);
    transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 10;
}

.row-below-selected {
    transform: translateY(300px);
    transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 10;
}

.row-selected {
    background: rgba(32, 107, 196, 0.1);
    border-left: 4px solid #206bc4;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(32, 107, 196, 0.2);
    z-index: 50;
}

#tabla-casos tbody {
    position: relative;
    padding-top: 320px;
    padding-bottom: 320px;
}

/* Columnas ordenables */
.table thead th.sortable {
    cursor: pointer;
    user-select: none;
    transition: background-color 0.2s ease;
}

.table thead th.sortable:hover {
    background-color: rgba(32, 107, 196, 0.05);
}

.table thead th.sortable i {
    font-size: 14px;
    opacity: 0.5;
    transition: all 0.2s ease;
}

.table thead th.sortable:hover i {
    opacity: 1;
}

.table thead th.sortable.sorted-asc i::before {
    content: "\eb25"; /* ti-sort-ascending */
    opacity: 1;
    color: #206bc4;
}

.table thead th.sortable.sorted-desc i::before {
    content: "\eb26"; /* ti-sort-descending */
    opacity: 1;
    color: #206bc4;
}

/* Responsive */
@media (max-width: 992px) {
    .caso-card-grid {
        grid-template-columns: 1fr;
    }

    #caso-card-3d {
        width: 95%;
        max-height: 90vh;
    }
}

@media (max-width: 480px) {
    .caso-card-inner {
        padding: 20px;
    }

    .detail-item {
        font-size: 13px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // Funcionalidad de tarjeta 3D flotante
    // ============================================
    const card3d = document.getElementById('caso-card-3d');
    const overlay = document.getElementById('caso-overlay');
    const closeCard = document.getElementById('close-card-3d');
    const tablaCasos = document.getElementById('tabla-casos');
    const casoRows = document.querySelectorAll('.caso-row');
    const tableElement = document.querySelector('.table');

    let currentIndex = -1;
    let casosData = [];

    // Recopilar datos de todos los casos
    casoRows.forEach((row, index) => {
        casosData.push({
            index: index,
            id: row.dataset.casoId,
            fecha: row.dataset.casoFecha,
            estudiante: row.dataset.casoEstudiante,
            documento: row.dataset.casoDocumento,
            tipo: row.dataset.casoTipo,
            descripcion: row.dataset.casoDescripcion,
            lugar: row.dataset.casoLugar,
            reportado: row.dataset.casoReportado,
            estado: row.dataset.casoEstado,
            estadoClass: row.dataset.casoEstadoClass,
            acudiente: row.dataset.casoAcudiente,
            suspension: row.dataset.casoSuspension,
            psicologia: row.dataset.casoPsicologia,
            compromiso: row.dataset.casoCompromiso,
            acciones: row.dataset.casoAcciones,
            observaciones: row.dataset.casoObservaciones
        });
    });

    function capitalize(text) {
        if (!text) return '';
        return text.toLowerCase().split(' ').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    function mostrarCard(index) {
        if (index < 0 || index >= casosData.length) return;

        currentIndex = index;
        const caso = casosData[index];

        // Actualizar contenido de la tarjeta
        document.getElementById('card-caso-id').textContent = caso.id;
        document.getElementById('card-fecha').textContent = caso.fecha;
        document.getElementById('card-tipo').textContent = capitalize(caso.tipo);
        document.getElementById('card-estado').textContent = capitalize(caso.estado);
        document.getElementById('card-lugar').textContent = capitalize(caso.lugar);
        document.getElementById('card-estudiante').textContent = capitalize(caso.estudiante);
        document.getElementById('card-documento').textContent = caso.documento;
        document.getElementById('card-reportado').textContent = capitalize(caso.reportado);
        document.getElementById('card-acudiente').textContent = caso.acudiente;
        document.getElementById('card-suspension').textContent = caso.suspension;
        document.getElementById('card-psicologia').textContent = caso.psicologia;
        document.getElementById('card-compromiso').textContent = caso.compromiso;
        document.getElementById('card-descripcion').textContent = capitalize(caso.descripcion);
        document.getElementById('card-acciones').textContent = capitalize(caso.acciones);
        document.getElementById('card-observaciones').textContent = capitalize(caso.observaciones);

        // Actualizar enlace "Ver más"
        const verMasBtn = document.getElementById('caso-card-ver-mas');
        verMasBtn.href = `/convivencia/casos/${caso.id}`;

        // Mostrar overlay y tarjeta
        overlay.style.display = 'block';
        card3d.style.display = 'block';
        setTimeout(() => {
            card3d.classList.add('show');
        }, 10);

        // Separar filas
        casoRows.forEach((row, idx) => {
            row.classList.remove('row-above-selected', 'row-below-selected', 'row-selected');
            if (idx < index) {
                row.classList.add('row-above-selected');
            } else if (idx > index) {
                row.classList.add('row-below-selected');
            } else {
                row.classList.add('row-selected');
            }
        });
    }

    function ocultarCard() {
        card3d.classList.remove('show');
        setTimeout(() => {
            card3d.style.display = 'none';
            overlay.style.display = 'none';
        }, 300);

        // Restaurar filas
        casoRows.forEach(row => {
            row.classList.remove('row-above-selected', 'row-below-selected', 'row-selected');
        });

        currentIndex = -1;
    }

    // Eventos de clic en filas
    casoRows.forEach((row, index) => {
        row.addEventListener('click', function() {
            mostrarCard(index);
        });
    });

    // Cerrar tarjeta
    closeCard.addEventListener('click', ocultarCard);
    overlay.addEventListener('click', ocultarCard);

    // Control con scroll de rueda del mouse
    let scrollTimeout;
    let scrollAccumulator = 0;
    let isScrolling = false;
    const scrollThreshold = 100;
    const scrollDelay = 500;

    window.addEventListener('wheel', function(e) {
        if (currentIndex === -1) return;
        if (isScrolling) return;

        scrollAccumulator += e.deltaY;

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            if (Math.abs(scrollAccumulator) >= scrollThreshold) {
                if (scrollAccumulator > 0) {
                    if (currentIndex < casosData.length - 1) {
                        isScrolling = true;
                        mostrarCard(currentIndex + 1);
                        setTimeout(() => { isScrolling = false; }, scrollDelay);
                    }
                } else {
                    if (currentIndex > 0) {
                        isScrolling = true;
                        mostrarCard(currentIndex - 1);
                        setTimeout(() => { isScrolling = false; }, scrollDelay);
                    }
                }
            }
            scrollAccumulator = 0;
        }, 150);
    }, { passive: true });

    // Navegación con teclado
    document.addEventListener('keydown', function(e) {
        if (currentIndex === -1) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (currentIndex < casosData.length - 1) {
                mostrarCard(currentIndex + 1);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (currentIndex > 0) {
                mostrarCard(currentIndex - 1);
            }
        } else if (e.key === 'Escape') {
            ocultarCard();
        }
    });

    // ============================================
    // Selector de cantidad de filas por página
    // ============================================
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

    // ============================================
    // Ordenamiento de columnas
    // ============================================
    const sortableHeaders = document.querySelectorAll('.sortable');

    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortColumn = this.dataset.sort;
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentDirection = url.searchParams.get('direction');

            let newDirection = 'asc';
            if (currentSort === sortColumn && currentDirection === 'asc') {
                newDirection = 'desc';
            }

            url.searchParams.set('sort', sortColumn);
            url.searchParams.set('direction', newDirection);
            url.searchParams.set('page', '1');

            window.location.href = url.toString();
        });
    });

    // Marcar columna actualmente ordenada
    const currentSort = new URLSearchParams(window.location.search).get('sort');
    const currentDirection = new URLSearchParams(window.location.search).get('direction');

    if (currentSort) {
        const activeHeader = document.querySelector(`[data-sort="${currentSort}"]`);
        if (activeHeader) {
            activeHeader.classList.add(currentDirection === 'desc' ? 'sorted-desc' : 'sorted-asc');
        }
    }
});
</script>
@endpush
