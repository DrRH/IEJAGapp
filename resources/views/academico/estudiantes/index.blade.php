@extends('layouts.tabler')

@section('title', 'Gestión de Estudiantes')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Gestión de Estudiantes</h2>
            <div class="text-secondary mt-1">Administra los estudiantes de la institución</div>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <a href="{{ route('academico.estudiantes.export') }}" class="btn btn-outline-primary">
                <i class="ti ti-download me-1"></i>Exportar CSV
            </a>
            <a href="{{ route('academico.estudiantes.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Nuevo Estudiante
            </a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Filtros --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('academico.estudiantes.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Búsqueda</label>
                        <input type="text" name="search" class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Buscar por nombre, documento o matrícula...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                            <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                            <option value="retirado" @selected(request('estado') === 'retirado')>Retirado</option>
                            <option value="graduado" @selected(request('estado') === 'graduado')>Graduado</option>
                            <option value="trasladado" @selected(request('estado') === 'trasladado')>Trasladado</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sede</label>
                        <select name="sede_id" class="form-select">
                            <option value="">Todas las sedes</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" @selected(request('sede_id') == $sede->id)>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Grado (Ctrl + clic para múltiple)</label>
                        <select name="grado_id[]" id="filtro-grado" class="form-select" multiple size="5">
                            @foreach($grados as $grado)
                                <option value="{{ $grado->id }}"
                                        @selected(is_array(request('grado_id')) && in_array($grado->id, request('grado_id')))
                                        data-codigo="{{ $grado->codigo }}">
                                    {{ $grado->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mantén Ctrl (Cmd en Mac) para seleccionar varios</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Grupo (Ctrl + clic para múltiple)</label>
                        <select name="grupo_id[]" id="filtro-grupo" class="form-select" multiple size="5">
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id }}"
                                        @selected(is_array(request('grupo_id')) && in_array($grupo->id, request('grupo_id')))
                                        data-grado="{{ $grupo->grado_id }}">
                                    {{ $grupo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mantén Ctrl (Cmd en Mac) para seleccionar varios</small>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('academico.estudiantes.index') }}" class="btn btn-secondary">
                            <i class="ti ti-x me-1"></i>Limpiar filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de estudiantes --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Lista de Estudiantes</h3>
                <div class="card-subtitle">Total: {{ $estudiantes->total() }} estudiantes</div>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                <label class="form-label mb-0">Mostrar:</label>
                <select id="per-page-selector" class="form-select form-select-sm" style="width: auto;">
                    <option value="10" @selected(request('per_page', 20) == 10)>10</option>
                    <option value="15" @selected(request('per_page', 20) == 15)>15</option>
                    <option value="20" @selected(request('per_page', 20) == 20)>20</option>
                    <option value="25" @selected(request('per_page', 20) == 25)>25</option>
                    <option value="50" @selected(request('per_page', 20) == 50)>50</option>
                    <option value="100" @selected(request('per_page', 20) == 100)>100</option>
                    <option value="250" @selected(request('per_page', 20) == 250)>250</option>
                    <option value="500" @selected(request('per_page', 20) == 500)>500</option>
                    <option value="custom">Personalizado...</option>
                </select>
                <span class="text-muted">filas</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th class="text-center w-1">#</th>
                        <th class="text-center sortable" data-sort="codigo_estudiante">
                            Código
                            <i class="ti ti-selector ms-1"></i>
                        </th>
                        <th class="text-center sortable" data-sort="nombre_completo">
                            Nombre Completo
                            <i class="ti ti-selector ms-1"></i>
                        </th>
                        <th class="text-center sortable" data-sort="numero_documento">
                            Documento
                            <i class="ti ti-selector ms-1"></i>
                        </th>
                        <th class="text-center sortable" data-sort="sede_id">
                            Sede
                            <i class="ti ti-selector ms-1"></i>
                        </th>
                        <th class="text-center">Grado Actual</th>
                        <th class="text-center sortable" data-sort="estado">
                            Estado
                            <i class="ti ti-selector ms-1"></i>
                        </th>
                        <th class="text-center w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-estudiantes">
                    @forelse($estudiantes as $index => $estudiante)
                        @php
                            $fotoPath = '/storage/fotos_2025/' . $estudiante->codigo_estudiante . '.png';
                            $fotoUrl = file_exists(public_path($fotoPath))
                                ? asset($fotoPath)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($estudiante->nombre_completo) . '&background=206bc4&color=fff&size=400';
                        @endphp
                        <tr class="estudiante-row"
                            data-estudiante-id="{{ $estudiante->id }}"
                            data-estudiante-nombre="{{ $estudiante->nombre_completo }}"
                            data-estudiante-codigo="{{ $estudiante->codigo_estudiante }}"
                            data-estudiante-matricula="{{ $estudiante->matriculaActual?->numero_matricula ?? $estudiante->codigo_estudiante }}"
                            data-estudiante-documento="{{ $estudiante->tipo_documento }}: {{ $estudiante->numero_documento }}"
                            data-estudiante-grado="{{ $estudiante->matriculaActual?->grupo->grado->nombre ?? 'Sin grado' }}"
                            data-estudiante-grupo="{{ $estudiante->matriculaActual?->grupo->nombre ?? '-' }}"
                            data-estudiante-sede="{{ $estudiante->sede?->nombre ?? 'Sin sede' }}"
                            data-estudiante-estado="{{ ucfirst($estudiante->estado) }}"
                            data-estudiante-estado-color="{{ match($estudiante->estado) {
                                'activo' => 'success',
                                'inactivo' => 'secondary',
                                'retirado' => 'danger',
                                'graduado' => 'info',
                                'trasladado' => 'warning',
                                default => 'secondary',
                            } }}"
                            data-estudiante-email="{{ $estudiante->email ?? 'Sin email' }}"
                            data-estudiante-foto="{{ $fotoUrl }}"
                            data-acudiente-nombre="{{ $estudiante->nombre_acudiente ?? 'No registrado' }}"
                            data-acudiente-telefono="{{ $estudiante->telefono_acudiente ?? 'No registrado' }}"
                            data-acudiente-email="{{ $estudiante->email_acudiente ?? 'No registrado' }}"
                            data-madre-nombre="{{ $estudiante->nombre_madre ?? 'No registrado' }}"
                            data-madre-telefono="{{ $estudiante->telefono_madre ?? 'No registrado' }}"
                            data-madre-email="{{ $estudiante->email_madre ?? 'No registrado' }}"
                            style="cursor: pointer;">
                            <td class="text-center">
                                <span class="text-secondary">{{ $estudiantes->firstItem() + $index }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-primary fw-bold">{{ $estudiante->codigo_estudiante }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2"
                                          style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($estudiante->nombre_completo) }}&background=206bc4&color=fff&size=40)"></span>
                                    <div>
                                        <div class="fw-bold">{{ $estudiante->nombre_completo }}</div>
                                        <small class="text-secondary">{{ $estudiante->email ?? 'Sin email' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div>{{ $estudiante->tipo_documento }}: {{ $estudiante->numero_documento }}</div>
                            </td>
                            <td class="text-center">
                                @if($estudiante->sede)
                                    <span class="badge bg-blue-lt">{{ $estudiante->sede->nombre }}</span>
                                @else
                                    <span class="text-secondary">Sin sede</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($estudiante->matriculaActual)
                                    <span class="badge bg-cyan-lt">{{ $estudiante->matriculaActual->grupo->grado->nombre }}</span>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeColor = match($estudiante->estado) {
                                        'activo' => 'success',
                                        'inactivo' => 'secondary',
                                        'retirado' => 'danger',
                                        'graduado' => 'info',
                                        'trasladado' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}-lt">{{ ucfirst($estudiante->estado) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('academico.estudiantes.show', $estudiante) }}"
                                       class="btn btn-sm btn-ghost-secondary" title="Ver detalles">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('academico.estudiantes.edit', $estudiante) }}"
                                       class="btn btn-sm btn-ghost-secondary" title="Editar">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-ghost-danger"
                                            title="Eliminar" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $estudiante->id }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>

                                {{-- Modal de confirmación de eliminación --}}
                                <div class="modal fade" id="deleteModal{{ $estudiante->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <i class="ti ti-alert-triangle icon text-warning" style="font-size: 3rem;"></i>
                                                </div>
                                                <h3 class="text-center mb-2">¿Eliminar estudiante?</h3>
                                                <p class="text-secondary text-center">
                                                    Esta acción no se puede deshacer.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancelar</button>
                                                <form method="POST" action="{{ route('academico.estudiantes.destroy', $estudiante) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ti ti-users icon text-secondary mb-3" style="font-size: 3rem;"></i>
                                <p class="text-secondary mb-0">No hay estudiantes registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($estudiantes->hasPages())
            <div class="card-footer">
                {{ $estudiantes->links() }}
            </div>
        @endif
    </div>

    {{-- Tarjeta flotante 3D con información del estudiante --}}
    <div id="estudiante-card-3d" class="estudiante-card-3d">
        <div class="estudiante-card-inner">
            <button class="estudiante-card-close" id="close-card-3d">
                <i class="ti ti-x"></i>
            </button>

            <a href="#" id="estudiante-card-ver-mas" class="estudiante-card-ver-mas" title="Ver información completa del estudiante">
                <i class="ti ti-eye me-2"></i>Ver más
            </a>

            <div class="estudiante-card-photo-container">
                <div class="estudiante-card-photo">
                    <img id="card-foto" src="" alt="Foto del estudiante">
                </div>
                <div class="estudiante-card-photo-info">
                    <div class="photo-info-item">
                        <small>Matrícula</small>
                        <div id="card-matricula" class="fw-bold"></div>
                    </div>
                    <div class="photo-info-item">
                        <small>Documento</small>
                        <div id="card-documento"></div>
                    </div>
                </div>
            </div>

            <div class="estudiante-card-info">
                <h3 id="card-nombre" class="mb-3"></h3>

                <div class="estudiante-card-grid">
                    {{-- Columna 1: Datos del Estudiante --}}
                    <div class="info-section">
                        <h4 class="section-title"><i class="ti ti-user me-2"></i>Datos del Estudiante</h4>
                        <div class="section-details">

                            <div class="detail-item">
                                <i class="ti ti-school me-2"></i>
                                <div>
                                    <small>Grado / Grupo</small>
                                    <div><span id="card-grado"></span> / <span id="card-grupo"></span></div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="ti ti-building me-2"></i>
                                <div>
                                    <small>Sede</small>
                                    <div id="card-sede"></div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="ti ti-mail me-2"></i>
                                <div>
                                    <small>Email</small>
                                    <div id="card-email"></div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="ti ti-info-circle me-2"></i>
                                <div>
                                    <small>Estado</small>
                                    <div><span id="card-estado" class="badge"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Columna 2: Acudiente --}}
                    <div class="info-section">
                        <h4 class="section-title"><i class="ti ti-user-check me-2"></i>Acudiente</h4>
                        <div class="section-details">
                            <div class="detail-item">
                                <i class="ti ti-user me-2"></i>
                                <div>
                                    <small>Nombre</small>
                                    <div id="card-acudiente-nombre"></div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="ti ti-phone me-2"></i>
                                <div>
                                    <small>Teléfono</small>
                                    <div id="card-acudiente-telefono"></div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="ti ti-mail me-2"></i>
                                <div>
                                    <small>Email</small>
                                    <div id="card-acudiente-email"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Columna 3: Madre --}}
                    <div class="info-section">
                        <h4 class="section-title"><i class="ti ti-heart me-2"></i>Madre</h4>
                        <div class="section-details">
                            <div class="detail-item">
                                <i class="ti ti-user me-2"></i>
                                <div>
                                    <small>Nombre</small>
                                    <div id="card-madre-nombre"></div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="ti ti-phone me-2"></i>
                                <div>
                                    <small>Teléfono</small>
                                    <div id="card-madre-telefono"></div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="ti ti-mail me-2"></i>
                                <div>
                                    <small>Email</small>
                                    <div id="card-madre-email"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="small mt-3 text-center" style="color: #ffff00; text-shadow: 0 0 8px rgba(255, 255, 0, 0.6);">
                    <i class="ti ti-mouse"></i> Usa el scroll o las flechas del teclado para cambiar de estudiante
                </div>
            </div>
        </div>
    </div>

    {{-- Overlay de fondo --}}
    <div id="estudiante-overlay" class="estudiante-overlay"></div>
@endsection

@push('styles')
<style>
/* Overlay de fondo - sin oscurecer */
.estudiante-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: transparent;
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: all 0.4s ease;
    pointer-events: none;
}

.estudiante-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* Tarjeta flotante 3D - Franja horizontal */
.estudiante-card-3d {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9) translateZ(0);
    width: 90%;
    max-width: 1400px;
    z-index: 1050;
    opacity: 0;
    visibility: hidden;
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    perspective: 1500px;
    pointer-events: none;
}

.estudiante-card-3d.active {
    pointer-events: auto;
}

.estudiante-card-3d.active {
    transform: translate(-50%, -50%) scale(1) translateZ(100px);
    opacity: 1;
    visibility: visible;
}

.estudiante-card-inner {
    background: rgba(0, 102, 255, 0.8);
    backdrop-filter: blur(20px) saturate(180%);
    border-radius: 20px;
    box-shadow: 0 25px 80px rgba(0, 102, 255, 0.5),
                0 0 30px rgba(0, 204, 255, 0.6),
                inset 0 0 60px rgba(0, 204, 255, 0.2);
    padding: 35px 40px;
    position: relative;
    transform-style: preserve-3d;
    display: flex;
    align-items: flex-start;
    gap: 40px;
    border: 2px solid rgba(0, 204, 255, 0.8);
}

/* Botón de cerrar */
.estudiante-card-close {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: none;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.estudiante-card-close:hover {
    background: rgba(220, 53, 69, 1);
    transform: rotate(90deg) scale(1.1);
}

/* Botón Ver más */
.estudiante-card-ver-mas {
    position: absolute;
    top: 15px;
    left: 15px;
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    background: rgba(32, 107, 196, 0.9);
    color: white;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(32, 107, 196, 0.4);
    text-decoration: none;
}

.estudiante-card-ver-mas:hover {
    background: rgba(32, 107, 196, 1);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(32, 107, 196, 0.6);
    color: white;
}

.estudiante-card-ver-mas i {
    font-size: 16px;
}

/* Foto del estudiante y contenedor */
.estudiante-card-photo-container {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.estudiante-card-photo {
    width: 200px;
    height: 267px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    position: relative;
    transform: translateZ(60px);
    border: 3px solid rgba(255, 255, 255, 0.6);
}

.estudiante-card-photo::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(32, 107, 196, 0.15), rgba(32, 107, 196, 0));
    z-index: 1;
}

.estudiante-card-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.estudiante-card-photo:hover img {
    transform: scale(1.05);
}

/* Información debajo de la foto */
.estudiante-card-photo-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 200px;
}

.photo-info-item {
    background: rgba(0, 51, 153, 0.4);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    border: 1px solid rgba(0, 204, 255, 0.5);
    padding: 10px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3),
                0 0 10px rgba(0, 204, 255, 0.3);
}

.photo-info-item small {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    font-weight: 700;
    color: #ff6600;
    text-shadow: 0 0 8px rgba(255, 102, 0, 0.8),
                 0 2px 4px rgba(0, 0, 0, 0.5);
}

.photo-info-item div {
    font-size: 14px;
    font-weight: 700;
    color: #ffff00;
    text-shadow: 0 0 10px rgba(255, 255, 0, 0.8),
                 0 2px 4px rgba(0, 0, 0, 0.6);
}

/* Información del estudiante */
.estudiante-card-info {
    flex: 1;
    text-align: left;
}

.estudiante-card-info h3 {
    font-size: 28px;
    font-weight: 700;
    color: #ffff00;
    margin-bottom: 25px;
    text-shadow: 0 0 10px rgba(255, 255, 0, 0.8),
                 0 0 20px rgba(255, 255, 0, 0.6),
                 0 0 30px rgba(255, 255, 0, 0.4);
    text-transform: capitalize;
}

/* Grid de 3 columnas */
.estudiante-card-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(280px, 1fr));
    gap: 25px;
    width: 100%;
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
    align-items: flex-start;
    gap: 10px;
    padding: 12px;
    background: rgba(0, 51, 153, 0.4);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    border: 1px solid rgba(0, 204, 255, 0.5);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3),
                0 0 10px rgba(0, 204, 255, 0.3);
}

.detail-item:hover {
    background: rgba(0, 102, 255, 0.5);
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(0, 204, 255, 0.5),
                0 0 20px rgba(0, 204, 255, 0.4);
}

.detail-item i {
    font-size: 18px;
    margin-top: 2px;
    color: #ff6600;
    filter: drop-shadow(0 0 3px rgba(255, 102, 0, 0.8));
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
    flex: 1;
}

.detail-item div > div {
    font-size: 15px;
    font-weight: 700;
    color: #ffff00;
    text-shadow: 0 0 10px rgba(255, 255, 0, 0.8),
                 0 2px 4px rgba(0, 0, 0, 0.6);
    text-transform: capitalize;
}

/* Encabezado fijo de la tabla */
.table-responsive {
    position: relative;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
    overflow-x: auto;
}

.table thead {
    position: sticky;
    top: 0;
    z-index: 100;
    background: white;
}

.table thead th {
    background: white;
    border-bottom: 2px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

/* Fila seleccionada en la tabla */
.estudiante-row {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                background-color 0.2s ease;
}

.estudiante-row:hover {
    background-color: rgba(32, 107, 196, 0.08) !important;
}

.estudiante-row.row-selected {
    background-color: rgba(32, 107, 196, 0.15) !important;
    box-shadow: inset 4px 0 0 #206bc4;
}

/* Separación de filas arriba y abajo */

.estudiante-row.row-above-selected {
    transform: translateY(-300px);
    position: relative;
    z-index: 10;
}

.estudiante-row.row-below-selected {
    transform: translateY(300px);
    position: relative;
    z-index: 10;
}

.estudiante-row.row-selected {
    position: relative;
    z-index: 5;
    background-color: rgba(32, 107, 196, 0.15) !important;
    box-shadow: inset 4px 0 0 #206bc4;
}

/* Espaciado cuando hay selección */
.table-has-selection {
    padding-top: 320px;
    padding-bottom: 320px;
}

/* Ajuste para dispositivos móviles */
@media (max-width: 992px) {
    .estudiante-row.row-above-selected {
        transform: translateY(-350px);
    }

    .estudiante-row.row-below-selected {
        transform: translateY(350px);
    }

    .table-has-selection {
        padding-top: 270px;
        padding-bottom: 270px;
    }
}

/* Responsive */
@media (max-width: 1200px) {
    .estudiante-card-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-section:last-child {
        grid-column: 1 / -1;
    }
}

@media (max-width: 992px) {
    .estudiante-card-inner {
        flex-direction: column;
        padding: 30px;
        text-align: center;
    }

    .estudiante-card-photo {
        width: 180px;
        height: 240px;
    }

    .estudiante-card-info {
        text-align: center;
    }

    .estudiante-card-info h3 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .estudiante-card-grid {
        grid-template-columns: 1fr;
    }

    .info-section:last-child {
        grid-column: auto;
    }
}

@media (max-width: 768px) {
    .estudiante-card-3d {
        width: 95%;
    }

    .estudiante-card-inner {
        padding: 25px;
    }

    .estudiante-card-photo {
        width: 150px;
        height: 200px;
    }

    .estudiante-card-info h3 {
        font-size: 20px;
    }

    .detail-item {
        padding: 10px;
    }

    .section-title {
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .estudiante-card-inner {
        padding: 20px;
    }

    .estudiante-card-photo {
        width: 120px;
        height: 160px;
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
    const filtroGrado = document.getElementById('filtro-grado');
    const filtroGrupo = document.getElementById('filtro-grupo');

    if (filtroGrado && filtroGrupo) {
        function filtrarGrupos() {
            // Obtener todos los grados seleccionados
            const gradosSeleccionados = Array.from(filtroGrado.selectedOptions).map(opt => opt.value);
            const opcionesGrupo = filtroGrupo.querySelectorAll('option');

            // Mostrar/ocultar opciones según los grados seleccionados
            opcionesGrupo.forEach(function(opcion) {
                if (gradosSeleccionados.length === 0) {
                    // Si no hay grados seleccionados, mostrar todos los grupos
                    opcion.style.display = '';
                } else {
                    // Mostrar solo grupos de los grados seleccionados
                    const gradoGrupo = opcion.getAttribute('data-grado');
                    if (gradosSeleccionados.includes(gradoGrupo)) {
                        opcion.style.display = '';
                    } else {
                        opcion.style.display = 'none';
                        // Deseleccionar si está oculto
                        opcion.selected = false;
                    }
                }
            });
        }

        // Evento change del grado
        filtroGrado.addEventListener('change', function() {
            filtrarGrupos();
        });

        // Ejecutar al cargar
        filtrarGrupos();
    }

    // ============================================
    // Funcionalidad de tarjeta 3D flotante
    // ============================================
    const card3d = document.getElementById('estudiante-card-3d');
    const overlay = document.getElementById('estudiante-overlay');
    const closeCard = document.getElementById('close-card-3d');
    const tablaEstudiantes = document.getElementById('tabla-estudiantes');
    const estudianteRows = document.querySelectorAll('.estudiante-row');
    const tableElement = document.querySelector('.table');

    let currentIndex = -1;
    let estudiantesData = [];

    // Recopilar datos de todos los estudiantes
    estudianteRows.forEach((row, index) => {
        estudiantesData.push({
            index: index,
            id: row.dataset.estudianteId,
            nombre: row.dataset.estudianteNombre,
            codigo: row.dataset.estudianteCodigo,
            matricula: row.dataset.estudianteMatricula,
            documento: row.dataset.estudianteDocumento,
            grado: row.dataset.estudianteGrado,
            grupo: row.dataset.estudianteGrupo,
            sede: row.dataset.estudianteSede,
            estado: row.dataset.estudianteEstado,
            estadoColor: row.dataset.estudianteEstadoColor,
            email: row.dataset.estudianteEmail,
            foto: row.dataset.estudianteFoto,
            acudienteNombre: row.dataset.acudienteNombre,
            acudienteTelefono: row.dataset.acudienteTelefono,
            acudienteEmail: row.dataset.acudienteEmail,
            madreNombre: row.dataset.madreNombre,
            madreTelefono: row.dataset.madreTelefono,
            madreEmail: row.dataset.madreEmail,
            element: row
        });
    });

    // Función para mostrar la tarjeta con la información del estudiante
    function mostrarCard(index) {
        if (index < 0 || index >= estudiantesData.length) return;

        currentIndex = index;
        const estudiante = estudiantesData[index];

        // Actualizar contenido de la tarjeta - Datos del estudiante
        document.getElementById('card-foto').src = estudiante.foto;
        document.getElementById('card-nombre').textContent = estudiante.nombre.toLowerCase();
        document.getElementById('card-matricula').textContent = estudiante.matricula;
        document.getElementById('card-documento').textContent = estudiante.documento;
        document.getElementById('card-grado').textContent = estudiante.grado;
        document.getElementById('card-grupo').textContent = estudiante.grupo;
        document.getElementById('card-sede').textContent = estudiante.sede;
        document.getElementById('card-email').textContent = estudiante.email.toLowerCase();

        const estadoBadge = document.getElementById('card-estado');
        estadoBadge.textContent = estudiante.estado;
        estadoBadge.className = 'badge bg-' + estudiante.estadoColor + '-lt';

        // Actualizar información del acudiente
        document.getElementById('card-acudiente-nombre').textContent = estudiante.acudienteNombre.toLowerCase();
        document.getElementById('card-acudiente-telefono').textContent = estudiante.acudienteTelefono;
        document.getElementById('card-acudiente-email').textContent = estudiante.acudienteEmail.toLowerCase();

        // Actualizar información de la madre
        document.getElementById('card-madre-nombre').textContent = estudiante.madreNombre.toLowerCase();
        document.getElementById('card-madre-telefono').textContent = estudiante.madreTelefono;
        document.getElementById('card-madre-email').textContent = estudiante.madreEmail.toLowerCase();

        // Actualizar enlace del botón "Ver más"
        const verMasBtn = document.getElementById('estudiante-card-ver-mas');
        verMasBtn.href = '{{ route("academico.estudiantes.show", ":id") }}'.replace(':id', estudiante.id);

        // Aplicar clases para separación de filas
        estudianteRows.forEach((row, idx) => {
            row.classList.remove('row-selected', 'row-above-selected', 'row-below-selected');

            if (idx === index) {
                row.classList.add('row-selected');
            } else if (idx < index) {
                row.classList.add('row-above-selected');
            } else {
                row.classList.add('row-below-selected');
            }
        });

        // Agregar clase a la tabla para el padding
        tableElement.classList.add('table-has-selection');

        // Mostrar tarjeta y overlay
        overlay.classList.add('active');
        card3d.classList.add('active');

        // Hacer scroll a la fila si está fuera de vista
        setTimeout(() => {
            estudiante.element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    // Función para ocultar la tarjeta
    function ocultarCard() {
        overlay.classList.remove('active');
        card3d.classList.remove('active');
        tableElement.classList.remove('table-has-selection');
        estudianteRows.forEach(row => {
            row.classList.remove('row-selected', 'row-above-selected', 'row-below-selected');
        });

        currentIndex = -1;
    }

    // Click en fila de estudiante
    estudianteRows.forEach((row, index) => {
        row.addEventListener('click', function(e) {
            // No activar si se hace clic en los botones de acción
            if (e.target.closest('.btn-group') || e.target.closest('.modal')) {
                return;
            }
            mostrarCard(index);
        });
    });

    // Cerrar tarjeta
    closeCard.addEventListener('click', ocultarCard);
    overlay.addEventListener('click', ocultarCard);

    // Scroll para cambiar de estudiante (cuando la tarjeta está abierta)
    let scrollTimeout;
    let scrollAccumulator = 0;
    let isScrolling = false;
    const scrollThreshold = 100; // Umbral mínimo para cambiar de estudiante
    const scrollDelay = 500; // Tiempo de espera después de un cambio

    window.addEventListener('wheel', function(e) {
        if (currentIndex === -1) return; // Solo funciona si la tarjeta está abierta
        if (isScrolling) return; // Evitar múltiples cambios

        scrollAccumulator += e.deltaY;

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            if (Math.abs(scrollAccumulator) >= scrollThreshold) {
                if (scrollAccumulator > 0) {
                    // Scroll hacia abajo - siguiente estudiante
                    if (currentIndex < estudiantesData.length - 1) {
                        isScrolling = true;
                        mostrarCard(currentIndex + 1);
                        setTimeout(() => { isScrolling = false; }, scrollDelay);
                    }
                } else {
                    // Scroll hacia arriba - estudiante anterior
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

    // Teclas de navegación
    document.addEventListener('keydown', function(e) {
        if (currentIndex === -1) return;

        if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
            e.preventDefault();
            if (currentIndex < estudiantesData.length - 1) {
                mostrarCard(currentIndex + 1);
            }
        } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
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
                    // Restaurar el valor anterior si cancela o ingresa valor inválido
                    this.value = '{{ request('per_page', 20) }}';
                }
            } else {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('page', '1'); // Reset a la primera página
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
            url.searchParams.set('page', '1'); // Reset a la primera página

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
