@extends('layouts.tabler')

@section('title', 'Crear Usuario')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('administracion.usuarios.index') }}">Usuarios</a>
            </div>
            <h2 class="page-title">Crear Nuevo Usuario</h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 col-12">
            <form method="POST" action="{{ route('administracion.usuarios.store') }}">
                @csrf

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Usuario</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Nombre completo</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Correo electrónico</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Se usará para iniciar sesión</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Dejar vacío si el usuario usará Google OAuth</small>
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
                                                   {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $role->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                No hay roles configurados. Primero debes crear roles en el sistema.
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Crear Usuario
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
                    <h3 class="card-title">Ayuda</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="mb-2">Autenticación</h4>
                        <p class="text-secondary small mb-0">
                            Los usuarios pueden iniciar sesión mediante:
                        </p>
                        <ul class="small text-secondary">
                            <li>Correo y contraseña</li>
                            <li>Cuenta de Google (OAuth)</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h4 class="mb-2">Roles</h4>
                        <p class="text-secondary small mb-0">
                            Los roles determinan los permisos y accesos del usuario en el sistema.
                        </p>
                    </div>

                    <div>
                        <h4 class="mb-2">Contraseña</h4>
                        <p class="text-secondary small mb-0">
                            Si dejas la contraseña vacía, el usuario solo podrá iniciar sesión con Google.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
