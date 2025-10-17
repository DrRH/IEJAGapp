@extends('layouts.tabler')

@section('title', 'Editar Usuario')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('administracion.usuarios.index') }}">Usuarios</a>
            </div>
            <h2 class="page-title">Editar Usuario: {{ $user->name }}</h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 col-12">
            <form method="POST" action="{{ route('administracion.usuarios.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Usuario</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Nombre completo</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Correo electrónico</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($user->google_id)
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        <i class="ti ti-brand-google me-2"></i>
                                        Este usuario está vinculado con Google OAuth
                                    </div>
                                </div>
                            @endif

                            <div class="col-12">
                                <hr>
                                <h4 class="mb-3">Cambiar Contraseña</h4>
                                <p class="text-secondary small">Deja estos campos vacíos si no deseas cambiar la contraseña</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Roles y Permisos</h3>
                    </div>
                    <div class="card-body">
                        @if($roles->count() > 0)
                            <div class="form-label">Asignar roles</div>
                            <div class="row g-2">
                                @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <label class="form-check">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                   class="form-check-input"
                                                   {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $role->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                No hay roles configurados en el sistema.
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('administracion.usuarios.index') }}" class="btn">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Usuario</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-secondary small mb-1">Fecha de registro</div>
                        <div>{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-secondary small mb-1">Última actualización</div>
                        <div>{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($user->email_verified_at)
                        <div class="mb-3">
                            <div class="text-secondary small mb-1">Email verificado</div>
                            <div>
                                <span class="badge bg-success-lt">
                                    <i class="ti ti-check me-1"></i>Verificado
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($user->id !== auth()->id())
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title text-danger">Zona de Peligro</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small">Eliminar este usuario es una acción irreversible.</p>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="ti ti-trash me-1"></i>Eliminar Usuario
                        </button>
                    </div>
                </div>

                {{-- Modal de confirmación --}}
                <div class="modal fade" id="deleteModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <i class="ti ti-alert-triangle icon text-danger" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="text-center mb-2">¿Eliminar usuario?</h3>
                                <p class="text-secondary text-center">
                                    Estás a punto de eliminar a <strong>{{ $user->name }}</strong>.
                                    Esta acción no se puede deshacer.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancelar</button>
                                <form method="POST" action="{{ route('administracion.usuarios.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Sí, eliminar usuario</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
