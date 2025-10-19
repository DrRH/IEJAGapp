@extends('layouts.tabler')

@section('title', 'Crear Estudiante')

@section('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('academico.estudiantes.index') }}">Estudiantes</a>
            </div>
            <h2 class="page-title">Crear Nuevo Estudiante</h2>
        </div>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('academico.estudiantes.store') }}">
        @csrf

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-personal" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                            <i class="ti ti-user me-2"></i>Información Personal
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-contacto" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                            <i class="ti ti-address-book me-2"></i>Contacto
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-familia" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                            <i class="ti ti-users me-2"></i>Información Familiar
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-academico" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                            <i class="ti ti-school me-2"></i>Información Académica
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-salud" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                            <i class="ti ti-heartbeat me-2"></i>Salud y NEE
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    {{-- Pestaña: Información Personal --}}
                    <div class="tab-pane active show" id="tabs-personal" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Primer Nombre</label>
                                <input type="text" name="primer_nombre" class="form-control @error('primer_nombre') is-invalid @enderror"
                                       value="{{ old('primer_nombre') }}" required autofocus>
                                @error('primer_nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" name="segundo_nombre" class="form-control @error('segundo_nombre') is-invalid @enderror"
                                       value="{{ old('segundo_nombre') }}">
                                @error('segundo_nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Primer Apellido</label>
                                <input type="text" name="primer_apellido" class="form-control @error('primer_apellido') is-invalid @enderror"
                                       value="{{ old('primer_apellido') }}" required>
                                @error('primer_apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" name="segundo_apellido" class="form-control @error('segundo_apellido') is-invalid @enderror"
                                       value="{{ old('segundo_apellido') }}">
                                @error('segundo_apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Tipo de Documento</label>
                                <select name="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    <option value="TI" @selected(old('tipo_documento') === 'TI')>Tarjeta de Identidad</option>
                                    <option value="CC" @selected(old('tipo_documento') === 'CC')>Cédula de Ciudadanía</option>
                                    <option value="RC" @selected(old('tipo_documento') === 'RC')>Registro Civil</option>
                                    <option value="CE" @selected(old('tipo_documento') === 'CE')>Cédula de Extranjería</option>
                                    <option value="PA" @selected(old('tipo_documento') === 'PA')>Pasaporte</option>
                                </select>
                                @error('tipo_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Número de Documento</label>
                                <input type="text" name="numero_documento" class="form-control @error('numero_documento') is-invalid @enderror"
                                       value="{{ old('numero_documento') }}" required>
                                @error('numero_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                       value="{{ old('fecha_nacimiento') }}" required>
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Género</label>
                                <select name="genero" class="form-select @error('genero') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    <option value="masculino" @selected(old('genero') === 'masculino')>Masculino</option>
                                    <option value="femenino" @selected(old('genero') === 'femenino')>Femenino</option>
                                    <option value="otro" @selected(old('genero') === 'otro')>Otro</option>
                                </select>
                                @error('genero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Grupo Sanguíneo</label>
                                <select name="grupo_sanguineo" class="form-select @error('grupo_sanguineo') is-invalid @enderror">
                                    <option value="">Seleccione...</option>
                                    <option value="A" @selected(old('grupo_sanguineo') === 'A')>A</option>
                                    <option value="B" @selected(old('grupo_sanguineo') === 'B')>B</option>
                                    <option value="AB" @selected(old('grupo_sanguineo') === 'AB')>AB</option>
                                    <option value="O" @selected(old('grupo_sanguineo') === 'O')>O</option>
                                </select>
                                @error('grupo_sanguineo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">RH</label>
                                <select name="rh" class="form-select @error('rh') is-invalid @enderror">
                                    <option value="">Seleccione...</option>
                                    <option value="+" @selected(old('rh') === '+')>Positivo (+)</option>
                                    <option value="-" @selected(old('rh') === '-')>Negativo (-)</option>
                                </select>
                                @error('rh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Lugar de Nacimiento</label>
                                <input type="text" name="lugar_nacimiento" class="form-control @error('lugar_nacimiento') is-invalid @enderror"
                                       value="{{ old('lugar_nacimiento') }}" placeholder="Ciudad, Departamento">
                                @error('lugar_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña: Contacto --}}
                    <div class="tab-pane" id="tabs-contacto" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                       value="{{ old('direccion') }}">
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Barrio</label>
                                <input type="text" name="barrio" class="form-control @error('barrio') is-invalid @enderror"
                                       value="{{ old('barrio') }}">
                                @error('barrio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Municipio</label>
                                <input type="text" name="municipio" class="form-control @error('municipio') is-invalid @enderror"
                                       value="{{ old('municipio') }}">
                                @error('municipio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono') }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Celular</label>
                                <input type="text" name="celular" class="form-control @error('celular') is-invalid @enderror"
                                       value="{{ old('celular') }}">
                                @error('celular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña: Información Familiar --}}
                    <div class="tab-pane" id="tabs-familia" role="tabpanel">
                        <h3 class="mb-3">Datos del Acudiente</h3>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nombre del Acudiente</label>
                                <input type="text" name="nombre_acudiente" class="form-control @error('nombre_acudiente') is-invalid @enderror"
                                       value="{{ old('nombre_acudiente') }}">
                                @error('nombre_acudiente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Parentesco del Acudiente</label>
                                <input type="text" name="parentesco_acudiente" class="form-control @error('parentesco_acudiente') is-invalid @enderror"
                                       value="{{ old('parentesco_acudiente') }}" placeholder="Madre, Padre, Tío, Abuelo, etc.">
                                @error('parentesco_acudiente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono del Acudiente</label>
                                <input type="text" name="telefono_acudiente" class="form-control @error('telefono_acudiente') is-invalid @enderror"
                                       value="{{ old('telefono_acudiente') }}">
                                @error('telefono_acudiente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ocupación del Acudiente</label>
                                <input type="text" name="ocupacion_acudiente" class="form-control @error('ocupacion_acudiente') is-invalid @enderror"
                                       value="{{ old('ocupacion_acudiente') }}">
                                @error('ocupacion_acudiente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <h3 class="mb-3">Datos de la Madre</h3>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nombre de la Madre</label>
                                <input type="text" name="nombre_madre" class="form-control @error('nombre_madre') is-invalid @enderror"
                                       value="{{ old('nombre_madre') }}">
                                @error('nombre_madre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono de la Madre</label>
                                <input type="text" name="telefono_madre" class="form-control @error('telefono_madre') is-invalid @enderror"
                                       value="{{ old('telefono_madre') }}">
                                @error('telefono_madre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ocupación de la Madre</label>
                                <input type="text" name="ocupacion_madre" class="form-control @error('ocupacion_madre') is-invalid @enderror"
                                       value="{{ old('ocupacion_madre') }}">
                                @error('ocupacion_madre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email de la Madre</label>
                                <input type="email" name="email_madre" class="form-control @error('email_madre') is-invalid @enderror"
                                       value="{{ old('email_madre') }}">
                                @error('email_madre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <h3 class="mb-3">Datos del Padre</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre del Padre</label>
                                <input type="text" name="nombre_padre" class="form-control @error('nombre_padre') is-invalid @enderror"
                                       value="{{ old('nombre_padre') }}">
                                @error('nombre_padre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono del Padre</label>
                                <input type="text" name="telefono_padre" class="form-control @error('telefono_padre') is-invalid @enderror"
                                       value="{{ old('telefono_padre') }}">
                                @error('telefono_padre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ocupación del Padre</label>
                                <input type="text" name="ocupacion_padre" class="form-control @error('ocupacion_padre') is-invalid @enderror"
                                       value="{{ old('ocupacion_padre') }}">
                                @error('ocupacion_padre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email del Padre</label>
                                <input type="email" name="email_padre" class="form-control @error('email_padre') is-invalid @enderror"
                                       value="{{ old('email_padre') }}">
                                @error('email_padre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña: Información Académica --}}
                    <div class="tab-pane" id="tabs-academico" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Código del Estudiante</label>
                                <input type="text" name="codigo_estudiante" class="form-control @error('codigo_estudiante') is-invalid @enderror"
                                       value="{{ old('codigo_estudiante') }}" required>
                                @error('codigo_estudiante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Código único del estudiante en la institución</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Sede</label>
                                <select name="sede_id" class="form-select @error('sede_id') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}" @selected(old('sede_id') == $sede->id)>
                                            {{ $sede->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sede_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Estrato</label>
                                <select name="estrato" class="form-select @error('estrato') is-invalid @enderror">
                                    <option value="">Seleccione...</option>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" @selected(old('estrato') == $i)>Estrato {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('estrato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">EPS</label>
                                <input type="text" name="eps" class="form-control @error('eps') is-invalid @enderror"
                                       value="{{ old('eps') }}">
                                @error('eps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">Estado</label>
                                <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                                    <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                                    <option value="retirado" @selected(old('estado') === 'retirado')>Retirado</option>
                                    <option value="graduado" @selected(old('estado') === 'graduado')>Graduado</option>
                                    <option value="trasladado" @selected(old('estado') === 'trasladado')>Trasladado</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Fecha de Ingreso</label>
                                <input type="date" name="fecha_ingreso" class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                       value="{{ old('fecha_ingreso', date('Y-m-d')) }}" required>
                                @error('fecha_ingreso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Institución de Procedencia</label>
                                <input type="text" name="institucion_procedencia" class="form-control @error('institucion_procedencia') is-invalid @enderror"
                                       value="{{ old('institucion_procedencia') }}" placeholder="Colegio anterior">
                                @error('institucion_procedencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pestaña: Salud y NEE --}}
                    <div class="tab-pane" id="tabs-salud" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Observaciones Médicas</label>
                                <textarea name="observaciones_medicas" rows="3" class="form-control @error('observaciones_medicas') is-invalid @enderror"
                                          placeholder="Alergias, enfermedades crónicas, medicamentos, etc.">{{ old('observaciones_medicas') }}</textarea>
                                @error('observaciones_medicas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">¿Tiene Discapacidad?</label>
                                <select name="tiene_discapacidad" class="form-select @error('tiene_discapacidad') is-invalid @enderror">
                                    <option value="0" @selected(old('tiene_discapacidad', '0') === '0')>No</option>
                                    <option value="1" @selected(old('tiene_discapacidad') === '1')>Sí</option>
                                </select>
                                @error('tiene_discapacidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Tipo de Discapacidad</label>
                                <input type="text" name="tipo_discapacidad" class="form-control @error('tipo_discapacidad') is-invalid @enderror"
                                       value="{{ old('tipo_discapacidad') }}" placeholder="Visual, auditiva, física, cognitiva, etc.">
                                @error('tipo_discapacidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Adaptaciones Curriculares</label>
                                <textarea name="adaptaciones_curriculares" rows="3" class="form-control @error('adaptaciones_curriculares') is-invalid @enderror"
                                          placeholder="Describe las adaptaciones curriculares necesarias para el estudiante">{{ old('adaptaciones_curriculares') }}</textarea>
                                @error('adaptaciones_curriculares')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Observaciones Generales</label>
                                <textarea name="observaciones_generales" rows="4" class="form-control @error('observaciones_generales') is-invalid @enderror"
                                          placeholder="Cualquier información adicional relevante sobre el estudiante">{{ old('observaciones_generales') }}</textarea>
                                @error('observaciones_generales')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Guardar Estudiante
                    </button>
                    <a href="{{ route('academico.estudiantes.index') }}" class="btn">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection
