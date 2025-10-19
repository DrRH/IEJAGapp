@extends('layouts.tabler')

@section('title', 'Detalle del Log')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('administracion.logs.index') }}">Logs y Auditoría</a>
            </div>
            <h2 class="page-title">Detalle del Log #{{ $log->id }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('administracion.logs.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6">
            {{-- Información principal --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información Principal</h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-4 text-secondary">Fecha y hora</div>
                            <div class="col-8">
                                <div>{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                <small class="text-secondary">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-4 text-secondary">Usuario</div>
                            <div class="col-8">
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
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-4 text-secondary">Acción</div>
                            <div class="col-8">
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
                                <span class="badge bg-{{ $badgeColor }}-lt fs-4">{{ $log->action }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-4 text-secondary">Descripción</div>
                            <div class="col-8">{{ $log->description }}</div>
                        </div>
                    </div>

                    @if($log->model_type)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-4 text-secondary">Modelo afectado</div>
                                <div class="col-8">
                                    <div><strong>{{ class_basename($log->model_type) }}</strong></div>
                                    <div class="text-secondary">ID: {{ $log->model_id }}</div>
                                    <div class="text-secondary small">{{ $log->model_type }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            {{-- Información técnica --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información Técnica</h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-4 text-secondary">Dirección IP</div>
                            <div class="col-8">
                                <span class="font-monospace">{{ $log->ip_address ?? 'No disponible' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-4 text-secondary">User Agent</div>
                            <div class="col-8">
                                <div class="font-monospace small" style="word-break: break-all;">
                                    {{ $log->user_agent ?? 'No disponible' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-4 text-secondary">ID del registro</div>
                            <div class="col-8">
                                <span class="font-monospace">#{{ $log->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Propiedades adicionales --}}
            @if($log->properties)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Datos Adicionales</h3>
                    </div>
                    <div class="card-body">
                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
