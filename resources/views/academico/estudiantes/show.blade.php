@extends('layouts.tabler')

@section('title', 'Ver Estudiante')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('academico.estudiantes.index') }}">Estudiantes</a>
            </div>
            <h2 class="page-title">{{ $estudiante->nombre_completo }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('academico.estudiantes.edit', $estudiante) }}" class="btn btn-primary">
                <i class="ti ti-edit me-1"></i>Editar Estudiante
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- Columna izquierda: Información básica --}}
        <div class="col-lg-4">
            {{-- Card de perfil con foto --}}
            <div class="card">
                <div class="card-body text-center">
                    @php
                        $fotoPath = '/storage/fotos_2025/' . $estudiante->codigo_estudiante . '.png';
                        $fotoUrl = file_exists(public_path($fotoPath))
                            ? asset($fotoPath)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($estudiante->nombre_completo) . '&background=206bc4&color=fff&size=400';
                    @endphp

                    {{-- Controles de tamaño de foto --}}
                    <div class="btn-group mb-2" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-size="small">
                            <i class="ti ti-photo-down"></i> Pequeña
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-size="medium">
                            <i class="ti ti-photo"></i> Mediana
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-size="large">
                            <i class="ti ti-photo-up"></i> Grande
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-size="xlarge">
                            <i class="ti ti-zoom-in"></i> Extra Grande
                        </button>
                    </div>
                    <div class="text-secondary small mb-3">
                        <i class="ti ti-info-circle"></i> Usa la rueda del mouse para zoom, arrastra para mover, doble clic para resetear
                    </div>

                    {{-- Contenedor de foto con relación de aspecto 3:4 --}}
                    <div class="foto-estudiante-container mb-3 size-medium" id="foto-container">
                        <div class="foto-estudiante-wrapper" id="foto-wrapper">
                            <img src="{{ $fotoUrl }}"
                                 alt="Foto de {{ $estudiante->nombre_completo }}"
                                 class="foto-estudiante"
                                 id="foto-estudiante"
                                 draggable="false">
                        </div>
                    </div>

                    <h3 class="mb-1">{{ $estudiante->nombres }}</h3>
                    <h3 class="mb-3">{{ $estudiante->apellidos }}</h3>

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
                    <div class="badge bg-{{ $badgeColor }}-lt mb-3">{{ ucfirst($estudiante->estado) }}</div>

                    <div class="mt-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-secondary small">Matrícula</div>
                                <div class="fw-bold">
                                    @if($estudiante->matriculaActual)
                                        {{ $estudiante->matriculaActual->numero_matricula }}
                                    @else
                                        {{ $estudiante->codigo_estudiante }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary small">Documento</div>
                                <div class="fw-bold">{{ $estudiante->tipo_documento }}: {{ $estudiante->numero_documento }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card de información rápida --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Información Rápida</h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary small">Fecha de Nacimiento</div>
                                <div>{{ $estudiante->fecha_nacimiento?->format('d/m/Y') ?? '-' }}</div>
                                @if($estudiante->fecha_nacimiento)
                                    <div class="text-secondary small">{{ $estudiante->fecha_nacimiento->age }} años</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary small">Género</div>
                                <div>{{ ucfirst($estudiante->genero ?? '-') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary small">Sede</div>
                                <div>
                                    @if($estudiante->sede)
                                        <span class="badge bg-blue-lt">{{ $estudiante->sede->nombre }}</span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary small">Grado Actual</div>
                                <div>
                                    @if($estudiante->matriculaActual)
                                        <span class="badge bg-cyan-lt">{{ $estudiante->matriculaActual->grupo->grado->nombre }}</span>
                                        <div class="text-secondary small mt-1">Grupo: {{ $estudiante->matriculaActual->grupo->nombre }}</div>
                                    @else
                                        <span class="text-secondary">Sin matrícula activa</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary small">Fecha de Ingreso</div>
                                <div>{{ $estudiante->fecha_ingreso?->format('d/m/Y') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    @if($estudiante->fecha_retiro)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-secondary small">Fecha de Retiro</div>
                                    <div>{{ $estudiante->fecha_retiro->format('d/m/Y') }}</div>
                                    @if($estudiante->motivo_retiro)
                                        <div class="text-secondary small">Motivo: {{ $estudiante->motivo_retiro }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna derecha: Información detallada con pestañas --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#tab-datos-personales" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                <i class="ti ti-user me-1"></i>Datos Personales
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tab-familia" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                <i class="ti ti-users me-1"></i>Familia
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tab-academico" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                <i class="ti ti-school me-1"></i>Académico
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tab-salud" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                <i class="ti ti-heartbeat me-1"></i>Salud
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tab-convivencia" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                <i class="ti ti-clipboard-text me-1"></i>Convivencia
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- Pestaña: Datos Personales --}}
                        <div class="tab-pane active show" id="tab-datos-personales" role="tabpanel">
                            <h3 class="mb-3">Información Personal</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Lugar de Nacimiento</div>
                                    <div>{{ $estudiante->lugar_nacimiento ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Grupo Sanguíneo</div>
                                    <div>
                                        @if($estudiante->grupo_sanguineo && $estudiante->rh)
                                            {{ $estudiante->grupo_sanguineo }}{{ $estudiante->rh }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Información de Contacto</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Dirección</div>
                                    <div>{{ $estudiante->direccion ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Barrio</div>
                                    <div>{{ $estudiante->barrio ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-secondary small mb-1">Municipio</div>
                                    <div>{{ $estudiante->municipio ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-secondary small mb-1">Teléfono</div>
                                    <div>{{ $estudiante->telefono ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-secondary small mb-1">Celular</div>
                                    <div>{{ $estudiante->celular ?? '-' }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-secondary small mb-1">Email</div>
                                    <div>{{ $estudiante->email ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Pestaña: Familia --}}
                        <div class="tab-pane" id="tab-familia" role="tabpanel">
                            <h3 class="mb-3">Acudiente</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Nombre</div>
                                    <div>{{ $estudiante->nombre_acudiente ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Parentesco</div>
                                    <div>{{ $estudiante->parentesco_acudiente ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Teléfono</div>
                                    <div>{{ $estudiante->telefono_acudiente ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Email</div>
                                    <div>{{ $estudiante->email_acudiente ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Ocupación</div>
                                    <div>{{ $estudiante->ocupacion_acudiente ?? '-' }}</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Madre</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Nombre</div>
                                    <div>{{ $estudiante->nombre_madre ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Teléfono</div>
                                    <div>{{ $estudiante->telefono_madre ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Ocupación</div>
                                    <div>{{ $estudiante->ocupacion_madre ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Email</div>
                                    <div>{{ $estudiante->email_madre ?? '-' }}</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Padre</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Nombre</div>
                                    <div>{{ $estudiante->nombre_padre ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Teléfono</div>
                                    <div>{{ $estudiante->telefono_padre ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Ocupación</div>
                                    <div>{{ $estudiante->ocupacion_padre ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">Email</div>
                                    <div>{{ $estudiante->email_padre ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Pestaña: Académico --}}
                        <div class="tab-pane" id="tab-academico" role="tabpanel">
                            <h3 class="mb-3">Información Académica</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="text-secondary small mb-1">Estrato</div>
                                    <div>{{ $estudiante->estrato ? 'Estrato ' . $estudiante->estrato : '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-secondary small mb-1">EPS</div>
                                    <div>{{ $estudiante->eps ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-secondary small mb-1">Institución de Procedencia</div>
                                    <div>{{ $estudiante->institucion_procedencia ?? '-' }}</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Historial de Matrículas</h3>
                            @if($estudiante->matriculas && $estudiante->matriculas->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Periodo</th>
                                                <th>Grado</th>
                                                <th>Grupo</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estudiante->matriculas->sortByDesc('created_at') as $matricula)
                                                <tr>
                                                    <td>{{ $matricula->periodoAcademico->nombre ?? '-' }}</td>
                                                    <td>{{ $matricula->grupo->grado->nombre ?? '-' }}</td>
                                                    <td>{{ $matricula->grupo->nombre ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $matricula->estado === 'activa' ? 'success' : 'secondary' }}-lt">
                                                            {{ ucfirst($matricula->estado) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $matricula->created_at->format('d/m/Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ti ti-clipboard-off icon text-secondary mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-secondary mb-0">No hay matrículas registradas</p>
                                </div>
                            @endif
                        </div>

                        {{-- Pestaña: Salud --}}
                        <div class="tab-pane" id="tab-salud" role="tabpanel">
                            <h3 class="mb-3">Información de Salud</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="text-secondary small mb-1">Observaciones Médicas</div>
                                    <div>{{ $estudiante->observaciones_medicas ?? '-' }}</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="mb-3">Necesidades Educativas Especiales (NEE)</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-secondary small mb-1">¿Tiene Discapacidad?</div>
                                    <div>
                                        @if($estudiante->tiene_discapacidad)
                                            <span class="badge bg-warning-lt">Sí</span>
                                        @else
                                            <span class="badge bg-success-lt">No</span>
                                        @endif
                                    </div>
                                </div>
                                @if($estudiante->tiene_discapacidad)
                                    <div class="col-md-6">
                                        <div class="text-secondary small mb-1">Tipo de Discapacidad</div>
                                        <div>{{ $estudiante->tipo_discapacidad ?? '-' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-secondary small mb-1">Adaptaciones Curriculares</div>
                                        <div>{{ $estudiante->adaptaciones_curriculares ?? '-' }}</div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="text-secondary small mb-1">Observaciones Generales</div>
                                    <div>{{ $estudiante->observaciones_generales ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Pestaña: Convivencia --}}
                        <div class="tab-pane" id="tab-convivencia" role="tabpanel">
                            <h3 class="mb-3">Reportes de Convivencia</h3>
                            @if($estudiante->reportesConvivencia && $estudiante->reportesConvivencia->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Reportado por</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estudiante->reportesConvivencia->sortByDesc('fecha_reporte') as $reporte)
                                                <tr style="cursor: pointer;" onclick="window.location.href='{{ route('convivencia.casos.show', $reporte->id) }}'">
                                                    <td>{{ $reporte->fecha_reporte->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if($reporte->tipoAnotacion)
                                                            <span class="badge bg-{{ $reporte->tipoAnotacion->categoria === 'grave' ? 'danger' : ($reporte->tipoAnotacion->categoria === 'leve' ? 'warning' : 'info') }}">
                                                                {{ $reporte->tipoAnotacion->nombre }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $reporte->descripcion_hechos }}">
                                                            {{ Str::limit($reporte->descripcion_hechos, 80) }}
                                                        </div>
                                                    </td>
                                                    <td>{{ $reporte->reportadoPor->name ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $reporte->color_estado }}">
                                                            {{ ucfirst(str_replace('_', ' ', $reporte->estado)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ti ti-clipboard-check icon text-secondary mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-secondary mb-0">No hay reportes de convivencia registrados</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="d-flex gap-2 mt-3 mb-3">
                <a href="{{ route('academico.estudiantes.edit', $estudiante) }}" class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i>Editar
                </a>
                <a href="{{ route('academico.estudiantes.index') }}" class="btn">
                    <i class="ti ti-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.foto-estudiante-container {
    position: relative;
    overflow: hidden;
    cursor: grab;
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.foto-estudiante-container:active {
    cursor: grabbing;
}

.foto-estudiante-container:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.foto-estudiante-wrapper {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.foto-estudiante {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
    transition: transform 0.1s ease;
    user-select: none;
}

/* Tamaños predefinidos - Relación de aspecto 3:4 (tamaño documento) */
.size-small {
    width: 150px;
    height: 200px;
}

.size-medium {
    width: 225px;
    height: 300px;
}

.size-large {
    width: 300px;
    height: 400px;
}

.size-xlarge {
    width: 375px;
    height: 500px;
}

/* Responsive - en pantallas pequeñas ajustar tamaños */
@media (max-width: 768px) {
    .size-small {
        width: 120px;
        height: 160px;
    }

    .size-medium {
        width: 165px;
        height: 220px;
    }

    .size-large {
        width: 210px;
        height: 280px;
    }

    .size-xlarge {
        width: 255px;
        height: 340px;
    }
}

/* Estilos para botones de control */
.btn-group [data-size].active {
    background-color: #206bc4;
    color: white;
    border-color: #206bc4;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('foto-container');
    const wrapper = document.getElementById('foto-wrapper');
    const foto = document.getElementById('foto-estudiante');
    const sizeButtons = document.querySelectorAll('[data-size]');

    let scale = 1;
    let translateX = 0;
    let translateY = 0;
    let isDragging = false;
    let startX = 0;
    let startY = 0;

    // Función para aplicar transformación
    function applyTransform() {
        foto.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
    }

    // Cambiar tamaño de foto
    sizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remover clase active de todos los botones
            sizeButtons.forEach(btn => btn.classList.remove('active'));
            // Agregar clase active al botón clickeado
            this.classList.add('active');

            // Cambiar tamaño del contenedor
            const size = this.getAttribute('data-size');
            container.className = 'foto-estudiante-container mb-3 size-' + size;

            // Resetear zoom
            scale = 1;
            translateX = 0;
            translateY = 0;
            applyTransform();
        });
    });

    // Zoom con rueda del mouse
    container.addEventListener('wheel', function(e) {
        e.preventDefault();

        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        const newScale = Math.min(Math.max(0.5, scale + delta), 3);

        scale = newScale;
        applyTransform();
    }, { passive: false });

    // Arrastre con mouse
    container.addEventListener('mousedown', function(e) {
        if (scale > 1) {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            container.style.cursor = 'grabbing';
        }
    });

    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            applyTransform();
        }
    });

    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            container.style.cursor = 'grab';
        }
    });

    // Soporte táctil para móviles
    let initialDistance = 0;
    let initialScale = 1;

    container.addEventListener('touchstart', function(e) {
        if (e.touches.length === 2) {
            // Pinch zoom
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            initialDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );
            initialScale = scale;
        } else if (e.touches.length === 1 && scale > 1) {
            // Drag
            isDragging = true;
            startX = e.touches[0].clientX - translateX;
            startY = e.touches[0].clientY - translateY;
        }
    });

    container.addEventListener('touchmove', function(e) {
        e.preventDefault();

        if (e.touches.length === 2) {
            // Pinch zoom
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            const distance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );

            scale = Math.min(Math.max(0.5, initialScale * (distance / initialDistance)), 3);
            applyTransform();
        } else if (e.touches.length === 1 && isDragging) {
            // Drag
            translateX = e.touches[0].clientX - startX;
            translateY = e.touches[0].clientY - startY;
            applyTransform();
        }
    }, { passive: false });

    container.addEventListener('touchend', function() {
        isDragging = false;
        initialDistance = 0;
    });

    // Doble clic para resetear zoom
    container.addEventListener('dblclick', function() {
        scale = 1;
        translateX = 0;
        translateY = 0;
        applyTransform();
    });
});
</script>
@endpush
