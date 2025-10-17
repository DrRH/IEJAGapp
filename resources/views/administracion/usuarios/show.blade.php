@extends('layouts.tabler')

@section('title', 'Ver Usuario')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('administracion.usuarios.index') }}">Usuarios</a>
            </div>
            <h2 class="page-title">{{ $user->name }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('administracion.usuarios.edit', $user) }}" class="btn btn-primary">
                <i class="ti ti-edit me-1"></i>Editar Usuario
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" class="avatar avatar-xl mb-3" alt="{{ $user->name }}">
                    @else
                        <span class="avatar avatar-xl mb-3"
                              style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=206bc4&color=fff&size=128)"></span>
                    @endif
                    <h3 class="mb-1">{{ $user->name }}</h3>
                    <p class="text-secondary mb-3">{{ $user->email }}</p>

                    @if($user->google_id)
                        <div class="badge bg-blue-lt mb-3">
                            <i class="ti ti-brand-google me-1"></i>Cuenta Google
                        </div>
                    @endif

                    @if($user->email_verified_at)
                        <div class="badge bg-success-lt mb-3">
                            <i class="ti ti-check me-1"></i>Email verificado
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Información</h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary small">Fecha de registro</div>
                                <div>{{ $user->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-secondary small">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary small">Última actualización</div>
                                <div>{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                                <div class="text-secondary small">{{ $user->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    @if($user->google_id)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-secondary small">Google ID</div>
                                    <div class="font-monospace small">{{ $user->google_id }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Roles Asignados</h3>
                </div>
                <div class="card-body">
                    @if($user->roles->count() > 0)
                        <div class="row g-3">
                            @foreach($user->roles as $role)
                                <div class="col-md-6">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar" style="background-color: #206bc4;">
                                                        <i class="ti ti-shield-check"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h4 class="mb-1">{{ $role->name }}</h4>
                                                    @if($role->permissions->count() > 0)
                                                        <div class="text-secondary small">
                                                            {{ $role->permissions->count() }} permisos
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-shield-off icon text-secondary mb-3" style="font-size: 3rem;"></i>
                            <p class="text-secondary mb-0">No tiene roles asignados</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($user->permissions->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Permisos Directos</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($user->permissions as $permission)
                                <div class="col-auto">
                                    <span class="badge bg-green-lt">{{ $permission->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Actividad Reciente</h3>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="ti ti-activity icon text-secondary mb-3" style="font-size: 3rem;"></i>
                        <p class="text-secondary mb-0">Función de auditoría en desarrollo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
