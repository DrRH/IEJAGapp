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
<form method="POST" action="{{ route('settings.update') }}">
  @csrf @method('PUT')

  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
        <li class="nav-item" role="presentation">
          <a href="#tabs-basica" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
            <i class="ti ti-building me-1"></i>Información básica
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-legal" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
            <i class="ti ti-file-certificate me-1"></i>Datos legales
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-contacto" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
            <i class="ti ti-phone me-1"></i>Contacto
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-directivos" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
            <i class="ti ti-user-star me-1"></i>Directivos
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-academico" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
            <i class="ti ti-school me-1"></i>Académico
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-redes" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
            <i class="ti ti-brand-facebook me-1"></i>Redes sociales
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#tabs-sistema" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
            <i class="ti ti-settings me-1"></i>Sistema
          </a>
        </li>
      </ul>
    </div>

    <div class="card-body">
      <div class="tab-content">
        {{-- Tab: Información básica --}}
        <div class="tab-pane active show" id="tabs-basica" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Nombre de la institución</label>
              <input type="text" name="institution_name" class="form-control @error('institution_name') is-invalid @enderror"
                     value="{{ old('institution_name', $settings['institution_name']) }}" required>
              @error('institution_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label required">Ciudad</label>
              <input type="text" name="institution_city" class="form-control @error('institution_city') is-invalid @enderror"
                     value="{{ old('institution_city', $settings['institution_city']) }}" required>
              @error('institution_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label class="form-label">Dirección</label>
              <input type="text" name="institution_address" class="form-control @error('institution_address') is-invalid @enderror"
                     value="{{ old('institution_address', $settings['institution_address']) }}"
                     placeholder="Dirección completa de la institución">
              @error('institution_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label required">Departamento</label>
              <input type="text" name="institution_department" class="form-control @error('institution_department') is-invalid @enderror"
                     value="{{ old('institution_department', $settings['institution_department']) }}" required>
              @error('institution_department')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label required">País</label>
              <input type="text" name="institution_country" class="form-control @error('institution_country') is-invalid @enderror"
                     value="{{ old('institution_country', $settings['institution_country']) }}" required>
              @error('institution_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Tab: Datos legales --}}
        <div class="tab-pane" id="tabs-legal" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">NIT</label>
              <input type="text" name="institution_nit" class="form-control @error('institution_nit') is-invalid @enderror"
                     value="{{ old('institution_nit', $settings['institution_nit']) }}"
                     placeholder="Ej: 890.123.456-7">
              @error('institution_nit')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <small class="form-hint">Número de Identificación Tributaria</small>
            </div>

            <div class="col-md-6">
              <label class="form-label">Código DANE</label>
              <input type="text" name="institution_dane" class="form-control @error('institution_dane') is-invalid @enderror"
                     value="{{ old('institution_dane', $settings['institution_dane']) }}"
                     placeholder="Ej: 105001234567">
              @error('institution_dane')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <small class="form-hint">Código asignado por el Ministerio de Educación</small>
            </div>

            <div class="col-md-8">
              <label class="form-label">Resolución de aprobación</label>
              <input type="text" name="institution_resolution" class="form-control @error('institution_resolution') is-invalid @enderror"
                     value="{{ old('institution_resolution', $settings['institution_resolution']) }}"
                     placeholder="Ej: Resolución 1234 de la Secretaría de Educación">
              @error('institution_resolution')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Fecha de resolución</label>
              <input type="date" name="institution_resolution_date" class="form-control @error('institution_resolution_date') is-invalid @enderror"
                     value="{{ old('institution_resolution_date', $settings['institution_resolution_date']) }}">
              @error('institution_resolution_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Tab: Contacto --}}
        <div class="tab-pane" id="tabs-contacto" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Correo electrónico</label>
              <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                     value="{{ old('contact_email', $settings['contact_email']) }}" required>
              @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Sitio web</label>
              <input type="url" name="contact_website" class="form-control @error('contact_website') is-invalid @enderror"
                     value="{{ old('contact_website', $settings['contact_website']) }}"
                     placeholder="https://josegalan.edu.co">
              @error('contact_website')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Teléfono fijo</label>
              <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror"
                     value="{{ old('contact_phone', $settings['contact_phone']) }}"
                     placeholder="Ej: (4) 123 4567">
              @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Teléfono celular</label>
              <input type="text" name="contact_cellphone" class="form-control @error('contact_cellphone') is-invalid @enderror"
                     value="{{ old('contact_cellphone', $settings['contact_cellphone']) }}"
                     placeholder="Ej: +57 300 123 4567">
              @error('contact_cellphone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Tab: Directivos --}}
        <div class="tab-pane" id="tabs-directivos" role="tabpanel">
          <div class="row g-3">
            <div class="col-12">
              <h3 class="card-title">Rector(a)</h3>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nombre completo</label>
              <input type="text" name="rector_name" class="form-control @error('rector_name') is-invalid @enderror"
                     value="{{ old('rector_name', $settings['rector_name']) }}"
                     placeholder="Nombre del rector o rectora">
              @error('rector_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="rector_email" class="form-control @error('rector_email') is-invalid @enderror"
                     value="{{ old('rector_email', $settings['rector_email']) }}"
                     placeholder="rector@josegalan.edu.co">
              @error('rector_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 mt-4">
              <h3 class="card-title">Coordinador(a)</h3>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nombre completo</label>
              <input type="text" name="coordinator_name" class="form-control @error('coordinator_name') is-invalid @enderror"
                     value="{{ old('coordinator_name', $settings['coordinator_name']) }}"
                     placeholder="Nombre del coordinador o coordinadora">
              @error('coordinator_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="coordinator_email" class="form-control @error('coordinator_email') is-invalid @enderror"
                     value="{{ old('coordinator_email', $settings['coordinator_email']) }}"
                     placeholder="coordinacion@josegalan.edu.co">
              @error('coordinator_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Tab: Académico --}}
        <div class="tab-pane" id="tabs-academico" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">Año académico</label>
              <input type="number" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror"
                     value="{{ old('academic_year', $settings['academic_year']) }}"
                     min="2020" max="2100" required>
              @error('academic_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Inicio del calendario</label>
              <input type="date" name="academic_calendar_start" class="form-control @error('academic_calendar_start') is-invalid @enderror"
                     value="{{ old('academic_calendar_start', $settings['academic_calendar_start']) }}">
              @error('academic_calendar_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">Fin del calendario</label>
              <input type="date" name="academic_calendar_end" class="form-control @error('academic_calendar_end') is-invalid @enderror"
                     value="{{ old('academic_calendar_end', $settings['academic_calendar_end']) }}">
              @error('academic_calendar_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label class="form-label">Niveles educativos ofrecidos</label>
              <textarea name="education_levels" rows="3" class="form-control @error('education_levels') is-invalid @enderror"
                        placeholder="Ej: Preescolar, Básica Primaria, Básica Secundaria, Media">{{ old('education_levels', $settings['education_levels']) }}</textarea>
              @error('education_levels')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <small class="form-hint">Separe los niveles con comas</small>
            </div>
          </div>
        </div>

        {{-- Tab: Redes sociales --}}
        <div class="tab-pane" id="tabs-redes" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">
                <i class="ti ti-brand-facebook me-1" style="color: #1877F2;"></i>Facebook
              </label>
              <input type="url" name="social_facebook" class="form-control @error('social_facebook') is-invalid @enderror"
                     value="{{ old('social_facebook', $settings['social_facebook']) }}"
                     placeholder="https://facebook.com/josegalan">
              @error('social_facebook')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="ti ti-brand-instagram me-1" style="color: #E4405F;"></i>Instagram
              </label>
              <input type="url" name="social_instagram" class="form-control @error('social_instagram') is-invalid @enderror"
                     value="{{ old('social_instagram', $settings['social_instagram']) }}"
                     placeholder="https://instagram.com/josegalan">
              @error('social_instagram')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="ti ti-brand-twitter me-1" style="color: #1DA1F2;"></i>Twitter / X
              </label>
              <input type="url" name="social_twitter" class="form-control @error('social_twitter') is-invalid @enderror"
                     value="{{ old('social_twitter', $settings['social_twitter']) }}"
                     placeholder="https://twitter.com/josegalan">
              @error('social_twitter')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="ti ti-brand-youtube me-1" style="color: #FF0000;"></i>YouTube
              </label>
              <input type="url" name="social_youtube" class="form-control @error('social_youtube') is-invalid @enderror"
                     value="{{ old('social_youtube', $settings['social_youtube']) }}"
                     placeholder="https://youtube.com/@josegalan">
              @error('social_youtube')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        {{-- Tab: Sistema --}}
        <div class="tab-pane" id="tabs-sistema" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Tema de la interfaz</label>
              <select name="theme" class="form-select @error('theme') is-invalid @enderror" required>
                <option value="light" @selected(old('theme', $settings['theme']) === 'light')>Claro</option>
                <option value="dark"  @selected(old('theme', $settings['theme']) === 'dark')>Oscuro</option>
              </select>
              @error('theme')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label required">Zona horaria</label>
              <select name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                <option value="America/Bogota" @selected(old('timezone', $settings['timezone']) === 'America/Bogota')>Bogotá (GMT-5)</option>
                <option value="America/Caracas" @selected(old('timezone', $settings['timezone']) === 'America/Caracas')>Caracas (GMT-4)</option>
                <option value="America/Mexico_City" @selected(old('timezone', $settings['timezone']) === 'America/Mexico_City')>Ciudad de México (GMT-6)</option>
                <option value="America/Lima" @selected(old('timezone', $settings['timezone']) === 'America/Lima')>Lima (GMT-5)</option>
                <option value="America/Buenos_Aires" @selected(old('timezone', $settings['timezone']) === 'America/Buenos_Aires')>Buenos Aires (GMT-3)</option>
              </select>
              @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer text-end">
      <button type="submit" class="btn btn-primary">
        <i class="ti ti-device-floppy me-1"></i>Guardar cambios
      </button>
    </div>
  </div>
</form>
@endsection
