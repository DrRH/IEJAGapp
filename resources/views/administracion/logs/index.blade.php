@extends('layouts.tabler')

@section('title', 'Logs y Auditoría')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Logs y Auditoría</h2>
            <div class="text-secondary">Registro de todas las actividades del sistema</div>
        </div>
        <div class="col-auto ms-auto">
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                <i class="ti ti-trash me-1"></i>Limpiar logs antiguos
            </button>
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
            <form method="GET" action="{{ route('administracion.logs.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Búsqueda</label>
                        <input type="text" name="search" class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Buscar en descripción...">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Acción</label>
                        <select name="action" class="form-select">
                            <option value="">Todas</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>
                                    {{ $action }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipo de modelo</label>
                        <select name="model_type" class="form-select">
                            <option value="">Todos</option>
                            @foreach($modelTypes as $modelType)
                                <option value="{{ $modelType }}" @selected(request('model_type') === $modelType)>
                                    {{ class_basename($modelType) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="date_from" class="form-control"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="date_to" class="form-control"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('administracion.logs.index') }}" class="btn btn-secondary">
                            <i class="ti ti-x me-1"></i>Limpiar filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de logs --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Actividades Registradas</h3>
            <div class="card-subtitle">Total: {{ $logs->total() }} registros</div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>Modelo</th>
                        <th>IP</th>
                        <th class="w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                <small class="text-secondary">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        @if($log->user->avatar)
                                            <img src="{{ $log->user->avatar }}" class="avatar avatar-sm me-2" alt="{{ $log->user->name }}">
                                        @else
                                            <span class="avatar avatar-sm me-2"
                                                  style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($log->user->name) }}&background=206bc4&color=fff&size=40)"></span>
                                        @endif
                                        <div>
                                            <div>{{ $log->user->name }}</div>
                                            <small class="text-secondary">{{ $log->user->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-secondary">Sistema</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeColor = match($log->action) {
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'deleted' => 'danger',
                                        'logged_in' => 'blue',
                                        'logged_out' => 'secondary',
                                        default => 'primary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}-lt">{{ $log->action }}</span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td>
                                @if($log->model_type)
                                    <span class="badge bg-azure-lt">
                                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                    </span>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-secondary font-monospace">{{ $log->ip_address }}</span>
                            </td>
                            <td>
                                <a href="{{ route('administracion.logs.show', $log) }}"
                                   class="btn btn-sm btn-ghost-secondary"
                                   title="Ver detalles">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="ti ti-activity icon text-secondary mb-3" style="font-size: 3rem;"></i>
                                <p class="text-secondary mb-0">No hay actividades registradas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de limpieza --}}
    <div class="modal fade" id="cleanupModal" tabindex="-1" aria-labelledby="cleanupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('administracion.logs.cleanup') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cleanupModalLabel">Limpiar logs antiguos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Esta acción eliminará permanentemente los logs de actividad antiguos.</p>
                        <div class="mb-3">
                            <label class="form-label required">Eliminar registros más antiguos que:</label>
                            <select name="days" class="form-select" required>
                                <option value="30">30 días</option>
                                <option value="60">60 días</option>
                                <option value="90" selected>90 días</option>
                                <option value="180">180 días</option>
                                <option value="365">1 año</option>
                            </select>
                        </div>
                        <div class="alert alert-warning mb-0">
                            <i class="ti ti-alert-triangle me-1"></i>
                            Esta acción no se puede deshacer.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar logs</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
