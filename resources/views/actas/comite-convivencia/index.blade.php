@extends('layouts.tabler')

@section('title', 'Comité Escolar de Convivencia')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="ti ti-shield-check me-2"></i>
                    Comité Escolar de Convivencia
                </h2>
                <div class="text-muted mt-1">Actas de reuniones del comité</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    <a href="{{ route('actas.comite-convivencia.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Nueva Acta
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
                <form method="GET" action="{{ route('actas.comite-convivencia.index') }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Año</label>
                            <select name="anio" class="form-select">
                                @foreach($anios as $anio)
                                    <option value="{{ $anio }}" @selected(request('anio', date('Y')) == $anio)>
                                        {{ $anio }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado (Ctrl + clic)</label>
                            <select name="estado[]" class="form-select" multiple size="3">
                                <option value="borrador" @selected(is_array(request('estado')) && in_array('borrador', request('estado')))>Borrador</option>
                                <option value="aprobada" @selected(is_array(request('estado')) && in_array('aprobada', request('estado')))>Aprobada</option>
                                <option value="publicada" @selected(is_array(request('estado')) && in_array('publicada', request('estado')))>Publicada</option>
                            </select>
                            <small class="text-muted">Mantén Ctrl para seleccionar varios</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Número de acta, resumen o lugar..."
                                   value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="ti ti-search"></i>
                                </button>
                                <a href="{{ route('actas.comite-convivencia.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    Actas del Comité ({{ $actas->total() }})
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
                    <table class="table table-vcenter table-hover" id="tabla-actas">
                        <thead style="position: sticky; top: 0; background: white; z-index: 100;">
                            <tr>
                                <th class="text-center w-1">#</th>
                                <th class="text-center sortable" data-sort="numero_acta">
                                    Número Acta
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-center sortable" data-sort="fecha_reunion">
                                    Fecha Reunión
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-center">Lugar</th>
                                <th class="text-start">Resumen Ejecutivo</th>
                                <th class="text-center sortable" data-sort="estado">
                                    Estado
                                    <i class="ti ti-selector ms-1"></i>
                                </th>
                                <th class="text-center w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actas as $index => $acta)
                                <tr class="acta-row"
                                    data-acta-id="{{ $acta->id }}"
                                    data-acta-numero="{{ $acta->numero_acta }}"
                                    data-acta-fecha="{{ $acta->fecha_reunion->format('d/m/Y') }}"
                                    data-acta-hora-inicio="{{ $acta->hora_inicio->format('H:i') }}"
                                    data-acta-hora-fin="{{ $acta->hora_fin?->format('H:i') ?? 'N/A' }}"
                                    data-acta-lugar="{{ $acta->lugar }}"
                                    data-acta-resumen="{{ $acta->resumen_ejecutivo }}"
                                    data-acta-estado="{{ ucfirst($acta->estado) }}"
                                    data-acta-estado-class="{{ $acta->color_estado }}"
                                    data-acta-creado="{{ $acta->creadoPor->name }}"
                                    data-acta-fecha-creacion="{{ $acta->created_at->format('d/m/Y H:i') }}"
                                    data-acta-casos="{{ $acta->cantidad_casos }}"
                                    data-acta-asistentes="{{ count($acta->lista_asistentes) }}"
                                    style="cursor: pointer; transition: all 0.3s ease;">
                                    <td class="text-center text-muted">
                                        {{ ($actas->currentPage() - 1) * $actas->perPage() + $index + 1 }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-lt">{{ $acta->numero_acta }}</span>
                                    </td>
                                    <td class="text-center">{{ $acta->fecha_reunion->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $acta->lugar }}</td>
                                    <td class="text-start">
                                        <div class="text-truncate" style="max-width: 400px;" title="{{ $acta->resumen_ejecutivo }}">
                                            {{ Str::limit($acta->resumen_ejecutivo, 80) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $acta->color_estado }}">
                                            {{ ucfirst($acta->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('actas.comite-convivencia.show', $acta) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver acta"
                                               onclick="event.stopPropagation();">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if($acta->es_borrador)
                                            <a href="{{ route('actas.comite-convivencia.edit', $acta) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Editar"
                                               onclick="event.stopPropagation();">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('actas.comite-convivencia.destroy', $acta) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onclick="event.stopPropagation();"
                                                  onsubmit="return confirm('¿Está seguro de eliminar esta acta?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ti ti-clipboard-off" style="font-size: 3rem;"></i>
                                        <div class="mt-2">No se encontraron actas</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($actas->hasPages())
            <div class="card-footer d-flex align-items-center">
                <div class="me-auto">
                    <p class="m-0 text-muted">
                        Mostrando <strong>{{ $actas->firstItem() }}</strong> a <strong>{{ $actas->lastItem() }}</strong> de <strong>{{ $actas->total() }}</strong> actas
                    </p>
                </div>
                {{ $actas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Overlay oscuro -->
<div id="acta-overlay" style="display: none;"></div>

<!-- Tarjeta flotante 3D -->
<div id="acta-card-3d" style="display: none;">
    <button id="close-card-3d" class="close-card-btn" title="Cerrar (Esc)">
        <i class="ti ti-x"></i>
    </button>

    <div class="acta-card-inner">
        <div class="acta-card-header">
            <h3 id="card-titulo">Acta Comité de Convivencia</h3>
        </div>

        <div class="acta-card-grid">
            <!-- Columna 1: Información del Acta -->
            <div class="info-section">
                <div class="section-title">INFORMACIÓN DEL ACTA</div>
                <div class="section-details">
                    <div class="detail-item">
                        <small>Número Acta</small>
                        <div id="card-numero" class="fw-bold"></div>
                    </div>
                    <div class="detail-item">
                        <small>Fecha Reunión</small>
                        <div id="card-fecha"></div>
                    </div>
                    <div class="detail-item">
                        <small>Hora Inicio</small>
                        <div id="card-hora-inicio"></div>
                    </div>
                    <div class="detail-item">
                        <small>Hora Fin</small>
                        <div id="card-hora-fin"></div>
                    </div>
                    <div class="detail-item">
                        <small>Lugar</small>
                        <div id="card-lugar"></div>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Estado y Participación -->
            <div class="info-section">
                <div class="section-title">ESTADO Y PARTICIPACIÓN</div>
                <div class="section-details">
                    <div class="detail-item">
                        <small>Estado</small>
                        <div id="card-estado"></div>
                    </div>
                    <div class="detail-item">
                        <small>Asistentes</small>
                        <div id="card-asistentes"></div>
                    </div>
                    <div class="detail-item">
                        <small>Casos Revisados</small>
                        <div id="card-casos"></div>
                    </div>
                </div>
            </div>

            <!-- Columna 3: Auditoría -->
            <div class="info-section">
                <div class="section-title">AUDITORÍA</div>
                <div class="section-details">
                    <div class="detail-item">
                        <small>Creado Por</small>
                        <div id="card-creado"></div>
                    </div>
                    <div class="detail-item">
                        <small>Fecha Creación</small>
                        <div id="card-fecha-creacion"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Ejecutivo -->
        <div class="acta-card-descripcion">
            <div class="section-title">RESUMEN EJECUTIVO</div>
            <div id="card-resumen" class="descripcion-text"></div>
        </div>

        <!-- Botón Ver más -->
        <a href="#" id="acta-card-ver-mas" class="acta-card-ver-mas" title="Ver acta completa">
            <i class="ti ti-eye me-2"></i>Ver Acta Completa
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Reutilizamos los estilos de la tarjeta 3D de casos */
#acta-overlay {
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

#acta-card-3d {
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

#acta-card-3d.show {
    opacity: 1;
    transform: translate(-50%, -50%) perspective(1500px) rotateX(0deg) scale(1);
}

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

.acta-card-inner {
    padding: 30px;
    position: relative;
}

.acta-card-header {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 3px solid rgba(0, 204, 255, 0.3);
}

.acta-card-header h3 {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.acta-card-grid {
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

.acta-card-descripcion {
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

.acta-card-ver-mas {
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

.acta-card-ver-mas:hover {
    background: linear-gradient(135deg, #1a56a0 0%, #14477f 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(32, 107, 196, 0.4);
    color: white;
}

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

#tabla-actas tbody {
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
    content: "\eb25";
    opacity: 1;
    color: #206bc4;
}

.table thead th.sortable.sorted-desc i::before {
    content: "\eb26";
    opacity: 1;
    color: #206bc4;
}

@media (max-width: 992px) {
    .acta-card-grid {
        grid-template-columns: 1fr;
    }

    #acta-card-3d {
        width: 95%;
        max-height: 90vh;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tarjeta flotante 3D (similar a casos y estudiantes)
    const card3d = document.getElementById('acta-card-3d');
    const overlay = document.getElementById('acta-overlay');
    const closeCard = document.getElementById('close-card-3d');
    const actaRows = document.querySelectorAll('.acta-row');

    let currentIndex = -1;
    let actasData = [];

    // Recopilar datos
    actaRows.forEach((row, index) => {
        actasData.push({
            index: index,
            id: row.dataset.actaId,
            numero: row.dataset.actaNumero,
            fecha: row.dataset.actaFecha,
            horaInicio: row.dataset.actaHoraInicio,
            horaFin: row.dataset.actaHoraFin,
            lugar: row.dataset.actaLugar,
            resumen: row.dataset.actaResumen,
            estado: row.dataset.actaEstado,
            estadoClass: row.dataset.actaEstadoClass,
            creado: row.dataset.actaCreado,
            fechaCreacion: row.dataset.actaFechaCreacion,
            casos: row.dataset.actaCasos,
            asistentes: row.dataset.actaAsistentes
        });
    });

    function capitalize(text) {
        if (!text) return '';
        return text.toLowerCase().split(' ').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    function mostrarCard(index) {
        if (index < 0 || index >= actasData.length) return;

        currentIndex = index;
        const acta = actasData[index];

        document.getElementById('card-numero').textContent = acta.numero;
        document.getElementById('card-fecha').textContent = acta.fecha;
        document.getElementById('card-hora-inicio').textContent = acta.horaInicio;
        document.getElementById('card-hora-fin').textContent = acta.horaFin;
        document.getElementById('card-lugar').textContent = capitalize(acta.lugar);
        document.getElementById('card-estado').textContent = capitalize(acta.estado);
        document.getElementById('card-asistentes').textContent = acta.asistentes;
        document.getElementById('card-casos').textContent = acta.casos;
        document.getElementById('card-creado').textContent = capitalize(acta.creado);
        document.getElementById('card-fecha-creacion').textContent = acta.fechaCreacion;
        document.getElementById('card-resumen').textContent = capitalize(acta.resumen);

        const verMasBtn = document.getElementById('acta-card-ver-mas');
        verMasBtn.href = `/actas/comite-convivencia/${acta.id}`;

        overlay.style.display = 'block';
        card3d.style.display = 'block';
        setTimeout(() => {
            card3d.classList.add('show');
        }, 10);

        actaRows.forEach((row, idx) => {
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

        actaRows.forEach(row => {
            row.classList.remove('row-above-selected', 'row-below-selected', 'row-selected');
        });

        currentIndex = -1;
    }

    actaRows.forEach((row, index) => {
        row.addEventListener('click', function() {
            mostrarCard(index);
        });
    });

    closeCard.addEventListener('click', ocultarCard);
    overlay.addEventListener('click', ocultarCard);

    // Control con rueda del mouse
    let scrollTimeout;
    let scrollAccumulator = 0;
    let isScrolling = false;

    window.addEventListener('wheel', function(e) {
        if (currentIndex === -1) return;
        if (isScrolling) return;

        scrollAccumulator += e.deltaY;

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            if (Math.abs(scrollAccumulator) >= 100) {
                if (scrollAccumulator > 0) {
                    if (currentIndex < actasData.length - 1) {
                        isScrolling = true;
                        mostrarCard(currentIndex + 1);
                        setTimeout(() => { isScrolling = false; }, 500);
                    }
                } else {
                    if (currentIndex > 0) {
                        isScrolling = true;
                        mostrarCard(currentIndex - 1);
                        setTimeout(() => { isScrolling = false; }, 500);
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
            if (currentIndex < actasData.length - 1) {
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

    // Ordenamiento
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
