@extends('layouts.tabler')

@section('title', 'Gestión de Usuarios')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Gestión de Usuarios</h2>
            <div class="text-secondary mt-1">Administra los usuarios del sistema</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('administracion.usuarios.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Nuevo Usuario
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Lista de Usuarios</h3>
                        <div class="card-subtitle">Total: {{ $users->total() }} usuarios</div>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('administracion.usuarios.index') }}" class="d-flex gap-2">
                            <input type="search" name="search" class="form-control form-control-sm"
                                   placeholder="Buscar usuario..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ti ti-search"></i>
                            </button>
                        </form>
                        <div class="vr"></div>
                        <label class="form-label mb-0 text-nowrap">Mostrar:</label>
                        <select id="per-page-selector" class="form-select form-select-sm" style="width: auto;">
                            <option value="10" @selected(request('per_page', 20) == 10)>10</option>
                            <option value="15" @selected(request('per_page', 20) == 15)>15</option>
                            <option value="20" @selected(request('per_page', 20) == 20)>20</option>
                            <option value="25" @selected(request('per_page', 20) == 25)>25</option>
                            <option value="50" @selected(request('per_page', 20) == 50)>50</option>
                            <option value="100" @selected(request('per_page', 20) == 100)>100</option>
                            <option value="custom">Personalizado...</option>
                        </select>
                        <span class="text-muted text-nowrap">filas</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Registro</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar }}" class="avatar avatar-sm me-2" alt="{{ $user->name }}">
                                            @else
                                                <span class="avatar avatar-sm me-2"
                                                      style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=206bc4&color=fff)"></span>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                @if($user->google_id)
                                                    <div class="text-secondary small">
                                                        <i class="ti ti-brand-google"></i> Cuenta Google
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-secondary">Sin rol asignado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-secondary" title="{{ $user->created_at->format('d/m/Y H:i') }}">
                                            {{ $user->created_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administracion.usuarios.show', $user) }}"
                                               class="btn btn-sm btn-ghost-secondary" title="Ver detalles">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('administracion.usuarios.edit', $user) }}"
                                               class="btn btn-sm btn-ghost-secondary" title="Editar">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <button type="button" class="btn btn-sm btn-ghost-danger"
                                                        title="Eliminar" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $user->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Modal de confirmación de eliminación --}}
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <div class="text-center mb-3">
                                                            <i class="ti ti-alert-triangle icon text-warning" style="font-size: 3rem;"></i>
                                                        </div>
                                                        <h3 class="text-center mb-2">¿Eliminar usuario?</h3>
                                                        <p class="text-secondary text-center">
                                                            Esta acción no se puede deshacer.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancelar</button>
                                                        <form method="POST" action="{{ route('administracion.usuarios.destroy', $user) }}">
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
                                    <td colspan="5" class="text-center text-secondary py-5">
                                        <i class="ti ti-users icon mb-2" style="font-size: 3rem;"></i>
                                        <p class="mb-0">No hay usuarios registrados</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('styles')
<style>
/* Encabezado fijo de tabla */
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endpush
