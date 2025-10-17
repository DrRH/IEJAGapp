{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.tabler')

@section('title','Configuración institucional')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Configuración institucional</h2>
    <div class="text-secondary">Datos generales de la I.E. y preferencias del sistema.</div>
  </div>
</div>
@endsection

@section('content')
<form class="card" method="POST" action="{{ route('settings.update') }}">
  @csrf @method('PUT')

  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre de la institución</label>
        <input type="text" name="institution_name" class="form-control @error('institution_name') is-invalid @enderror"
               value="{{ old('institution_name', $settings['institution_name']) }}" required>
        @error('institution_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Ciudad</label>
        <input type="text" name="institution_city" class="form-control @error('institution_city') is-invalid @enderror"
               value="{{ old('institution_city', $settings['institution_city']) }}" required>
        @error('institution_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Correo de contacto</label>
        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
               value="{{ old('contact_email', $settings['contact_email']) }}" required>
        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Tema</label>
        <select name="theme" class="form-select @error('theme') is-invalid @enderror">
          <option value="light" @selected(old('theme', $settings['theme']) === 'light')>Claro</option>
          <option value="dark"  @selected(old('theme', $settings['theme']) === 'dark')>Oscuro</option>
        </select>
        @error('theme')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>

  <div class="card-footer text-end">
    <button class="btn btn-primary">
      <i class="ti ti-device-floppy me-1"></i>Guardar cambios
    </button>
  </div>
</form>
@endsection
