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
                    <div class="col-md-4">
                        <label class="form-label">Búsqueda</label>
                        <input type="text" name="search" class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Buscar por nombre, documento o código...">
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

                    <div class="col-md-2">
                        <label class="form-label">Grado</label>
                        <select name="grado_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($grados as $grado)
                                <option value="{{ $grado->id }}" @selected(request('grado_id') == $grado->id)>
                                    {{ $grado->nombre }}
                                </option>
                            @endforeach
                        </select>
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
            <h3 class="card-title">Lista de Estudiantes</h3>
            <div class="card-subtitle">Total: {{ $estudiantes->total() }} estudiantes</div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre Completo</th>
                        <th>Documento</th>
                        <th>Sede</th>
                        <th>Grado Actual</th>
                        <th>Estado</th>
                        <th class="w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estudiantes as $estudiante)
                        <tr>
                            <td>
                                <span class="text-primary fw-bold">{{ $estudiante->codigo_estudiante }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2"
                                          style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($estudiante->primer_nombre . ' ' . $estudiante->primer_apellido) }}&background=206bc4&color=fff&size=40)"></span>
                                    <div>
                                        <div class="fw-bold">{{ $estudiante->primer_nombre }} {{ $estudiante->segundo_nombre }} {{ $estudiante->primer_apellido }} {{ $estudiante->segundo_apellido }}</div>
                                        <small class="text-secondary">{{ $estudiante->email ?? 'Sin email' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $estudiante->tipo_documento }}: {{ $estudiante->numero_documento }}</div>
                            </td>
                            <td>
                                @if($estudiante->sede)
                                    <span class="badge bg-blue-lt">{{ $estudiante->sede->nombre }}</span>
                                @else
                                    <span class="text-secondary">Sin sede</span>
                                @endif
                            </td>
                            <td>
                                @if($estudiante->matriculaActual)
                                    <span class="badge bg-cyan-lt">{{ $estudiante->matriculaActual->grupo->grado->nombre }}</span>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <td>
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
                            <td>
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
                            <td colspan="7" class="text-center py-5">
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
@endsection
