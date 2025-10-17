{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.tabler')

@section('title','Mi perfil')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Mi perfil</h2>
    <div class="text-secondary">Actualiza tus datos personales y contraseña.</div>
  </div>
</div>
@endsection

@section('content')
<div class="row row-cards">
  <div class="col-md-6">
    <form class="card" method="POST" action="{{ route('profile.update') }}">
      @csrf @method('PUT')
      <div class="card-header">
        <h3 class="card-title">Datos personales</h3>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $user->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Correo</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email', $user->email) }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">
          <i class="ti ti-device-floppy me-1"></i>Guardar
        </button>
      </div>
    </form>
  </div>

  <div class="col-md-6">
    <form class="card" method="POST" action="{{ route('profile.password') }}">
      @csrf @method('PUT')
      <div class="card-header">
        <h3 class="card-title">Cambiar contraseña</h3>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Contraseña actual</label>
          <input type="password" name="current_password"
                 class="form-control @error('current_password') is-invalid @enderror" required>
          @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Nueva contraseña</label>
          <input type="password" name="password"
                 class="form-control @error('password') is-invalid @enderror" required>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="form-label">Confirmación</label>
          <input type="password" name="password_confirmation" class="form-control" required>
        </div>
      </div>
      <div class="card-footer text-end">
        <button class="btn btn-warning" type="submit">
          <i class="ti ti-lock me-1"></i>Actualizar contraseña
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
