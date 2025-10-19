@extends('layouts.tabler')

@section('title', 'Ver Estudiante')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('academico.estudiantes.index') }}">Estudiantes</a>
            </div>
            <h2 class="page-title">{{ $estudiante->primer_nombre }} {{ $estudiante->segundo_nombre }} {{ $estudiante->primer_apellido }} {{ $estudiante->segundo_apellido }}</h2>
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
            {{-- Card de perfil --}}
            <div class="card">
                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3"
                          style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($estudiante->primer_nombre . ' ' . $estudiante->primer_apellido) }}&background=206bc4&color=fff&size=128)"></span>
                    <h3 class="mb-1">{{ $estudiante->primer_nombre }} {{ $estudiante->segundo_nombre }}</h3>
                    <h3 class="mb-3">{{ $estudiante->primer_apellido }} {{ $estudiante->segundo_apellido }}</h3>

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
                                <div class="text-secondary small">Código</div>
                                <div class="fw-bold">{{ $estudiante->codigo_estudiante }}</div>
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
                                        <div class="text-secondary small">Grupo: {{ $estudiante->matriculaActual->grupo->nombre }}</div>
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

        {{-- Columna derecha: Información detallada --}}
        <div class="col-lg-8">
            {{-- Card de información personal --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-user me-2"></i>Información Personal
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
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
                </div>
            </div>

            {{-- Card de contacto --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-address-book me-2"></i>Información de Contacto
                    </h3>
                </div>
                <div class="card-body">
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
            </div>

            {{-- Card de información familiar --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-users me-2"></i>Información Familiar
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Acudiente --}}
                    <h4 class="mb-3">Acudiente</h4>
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
                            <div class="text-secondary small mb-1">Ocupación</div>
                            <div>{{ $estudiante->ocupacion_acudiente ?? '-' }}</div>
                        </div>
                    </div>

                    <hr>

                    {{-- Madre --}}
                    <h4 class="mb-3">Madre</h4>
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

                    <hr>

                    {{-- Padre --}}
                    <h4 class="mb-3">Padre</h4>
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
            </div>

            {{-- Card de información académica --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-school me-2"></i>Información Académica
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
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
                </div>
            </div>

            {{-- Card de salud y NEE --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-heartbeat me-2"></i>Salud y Necesidades Educativas Especiales
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="text-secondary small mb-1">Observaciones Médicas</div>
                            <div>{{ $estudiante->observaciones_medicas ?? '-' }}</div>
                        </div>
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
            </div>

            {{-- Card de historial de matrículas --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-clipboard-list me-2"></i>Historial de Matrículas
                    </h3>
                </div>
                <div class="card-body">
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
            </div>

            {{-- Card de reportes de convivencia --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-alert-triangle me-2"></i>Reportes de Convivencia
                    </h3>
                </div>
                <div class="card-body">
                    @if($estudiante->reportesConvivencia && $estudiante->reportesConvivencia->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Reportado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estudiante->reportesConvivencia->sortByDesc('fecha') as $reporte)
                                        <tr>
                                            <td>{{ $reporte->fecha?->format('d/m/Y') ?? '-' }}</td>
                                            <td>
                                                @if($reporte->tipoAnotacion)
                                                    <span class="badge bg-{{ $reporte->tipoAnotacion->categoria === 'positiva' ? 'success' : 'danger' }}-lt">
                                                        {{ $reporte->tipoAnotacion->nombre }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;" title="{{ $reporte->descripcion }}">
                                                    {{ $reporte->descripcion ?? '-' }}
                                                </div>
                                            </td>
                                            <td>{{ $reporte->docente->nombre ?? '-' }}</td>
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

            {{-- Botones de acción --}}
            <div class="d-flex gap-2 mb-3">
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
