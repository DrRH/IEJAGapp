{{-- resources/views/layouts/tabler.blade.php2 - Dashboard Unificado --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
@php
    use Illuminate\Support\Str;

    // Determinar el rol del usuario de manera segura
    $userRole = 'Usuario';
    if (auth()->check()) {
        $u = auth()->user();

        if (method_exists($u, 'getRoleNames')) {
            // Spatie Permission
            $userRole = optional($u->getRoleNames())->first() ?? 'Usuario';
        } elseif (method_exists($u, 'roles')) {
            // Relación roles() clásica
            $firstRole = optional($u->roles)->first();
            $userRole = $firstRole->display_name ?? $firstRole->name ?? 'Usuario';
        } elseif (isset($u->role_name) || isset($u->role)) {
            // Campo directo en el modelo
            $userRole = $u->role_name ?? $u->role ?? 'Usuario';
        }
    }

    // Iniciales del usuario para avatares
    $initials = auth()->check()
        ? Str::of(auth()->user()->name ?? 'US')->substr(0, 2)->upper()
        : '';
@endphp

<div class="page">
    {{-- ========== SIDEBAR VERTICAL (IZQUIERDA) ========== --}}
    <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#sidebar-menu" aria-controls="sidebar-menu"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <h1 class="navbar-brand navbar-brand-autodark">
                <a href="{{ route('dashboard') }}">
                    <span class="navbar-brand-text">I.E. José Antonio Galán</span>
                </a>
            </h1>

            {{-- Usuario en móvil --}}
            @auth
            <div class="navbar-nav flex-row d-lg-none">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Menú de usuario">
                        <span class="avatar avatar-sm">{{ $initials }}</span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="mt-1 small text-secondary">{{ $userRole }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="ti ti-user me-2"></i>Mi perfil
                        </a>
                        <a href="{{ route('settings.index') }}" class="dropdown-item">
                            <i class="ti ti-settings me-2"></i>Configuración
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ti ti-logout me-2"></i>Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth

            {{-- Menú lateral principal --}}
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-home"></i></span>
                            <span class="nav-link-title">Inicio</span>
                        </a>
                    </li>

                    {{-- Sección Académico --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('academico*') ? 'show' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-books"></i></span>
                            <span class="nav-link-title">Académico</span>
                        </a>
                        <div class="dropdown-menu {{ request()->is('academico*') ? 'show' : '' }}">
                            <a class="dropdown-item" href="{{ route('academico.estudiantes.index') }}">
                                <i class="ti ti-users me-2"></i>Estudiantes
                            </a>
                            <a class="dropdown-item" href="/academico/calificaciones">
                                <i class="ti ti-certificate me-2"></i>Calificaciones
                            </a>
                            <a class="dropdown-item" href="/academico/asistencia">
                                <i class="ti ti-calendar-check me-2"></i>Asistencia
                            </a>
                        </div>
                    </li>

                    {{-- Sección Convivencia --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('convivencia*') ? 'show' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-heart-handshake"></i></span>
                            <span class="nav-link-title">Convivencia</span>
                        </a>
                        <div class="dropdown-menu {{ request()->is('convivencia*') ? 'show' : '' }}">
                            <a class="dropdown-item" href="/convivencia/observador">
                                <i class="ti ti-clipboard-list me-2"></i>Observador del Estudiante
                            </a>
                            <a class="dropdown-item" href="/convivencia/casos">
                                <i class="ti ti-gavel me-2"></i>Casos Disciplinarios
                            </a>
                            <a class="dropdown-item" href="/convivencia/psicosocial">
                                <i class="ti ti-stethoscope me-2"></i>Atención Psicosocial
                            </a>
                        </div>
                    </li>

                    {{-- Sección Actas y Comités --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('actas*') ? 'show' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-file-description"></i></span>
                            <span class="nav-link-title">Actas y Comités</span>
                        </a>
                        <div class="dropdown-menu {{ request()->is('actas*') ? 'show' : '' }}">
                            <a class="dropdown-item" href="/actas/generales">
                                <i class="ti ti-files me-2"></i>Actas Generales
                            </a>
                            <a class="dropdown-item" href="/actas/comite-academico">
                                <i class="ti ti-school me-2"></i>Comité Académico
                            </a>
                            <a class="dropdown-item" href="/actas/comite-convivencia">
                                <i class="ti ti-shield-check me-2"></i>Comité de Convivencia
                            </a>
                        </div>
                    </li>

                    {{-- Sección Reportes e Indicadores --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('reportes*') ? 'show' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-chart-line"></i></span>
                            <span class="nav-link-title">Reportes e Indicadores</span>
                        </a>
                        <div class="dropdown-menu {{ request()->is('reportes*') ? 'show' : '' }}">
                            <a class="dropdown-item" href="/reportes/academicos">
                                <i class="ti ti-report-analytics me-2"></i>Reportes Académicos
                            </a>
                            <a class="dropdown-item" href="/reportes/convivencia">
                                <i class="ti ti-report me-2"></i>Reportes de Convivencia
                            </a>
                            <a class="dropdown-item" href="/reportes/indicadores">
                                <i class="ti ti-dashboard me-2"></i>Tableros de Indicadores
                            </a>
                        </div>
                    </li>

                    {{-- Sección Administración --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('administracion*') ? 'show' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-settings"></i></span>
                            <span class="nav-link-title">Administración</span>
                        </a>
                        <div class="dropdown-menu {{ request()->is('administracion*') ? 'show' : '' }}">
                            <a class="dropdown-item" href="{{ route('administracion.usuarios.index') }}">
                                <i class="ti ti-users me-2"></i>Usuarios
                            </a>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="ti ti-building me-2"></i>Configuración Institucional
                            </a>
                            <a class="dropdown-item" href="{{ route('administracion.logs.index') }}">
                                <i class="ti ti-file-analytics me-2"></i>Logs y Auditoría
                            </a>
                        </div>
                    </li>

                    {{-- Sección Integraciones Google --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('integraciones*') ? 'show' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-brand-google"></i></span>
                            <span class="nav-link-title">Integraciones Google</span>
                        </a>
                        <div class="dropdown-menu {{ request()->is('integraciones*') ? 'show' : '' }}">
                            <a class="dropdown-item" href="/integraciones/drive">
                                <i class="ti ti-brand-google-drive me-2"></i>Google Drive
                            </a>
                            <a class="dropdown-item" href="/integraciones/sheets">
                                <i class="ti ti-table me-2"></i>Google Sheets
                            </a>
                            <a class="dropdown-item" href="/integraciones/calendar">
                                <i class="ti ti-calendar me-2"></i>Google Calendar
                            </a>
                        </div>
                    </li>
                    @endauth

                    {{-- Enlaces externos --}}
                    <li class="nav-item"><div class="dropdown-divider my-2"></div></li>

                    <li class="nav-item">
                        <a class="nav-link" href="https://josegalan.edupage.org/timetable/" target="_blank" rel="noopener noreferrer">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar"></i></span>
                            <span class="nav-link-title">Horarios (Edupage)</span>
                            <span class="badge badge-sm bg-blue-lt ms-auto">Externo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.canva.com/design/DAGKjmYMirw/2EUSYUXQHZG9gMTG3Z928w/edit" target="_blank" rel="noopener noreferrer">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-bell"></i></span>
                            <span class="nav-link-title">Notificados (Canva)</span>
                            <span class="badge badge-sm bg-green-lt ms-auto">Externo</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    {{-- ========== HEADER HORIZONTAL (SUPERIOR) ========== --}}
    <header class="navbar navbar-expand-md d-print-none">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-nav flex-row order-md-last">
                @auth
                {{-- Notificaciones --}}
                <div class="nav-item dropdown d-none d-md-flex me-3">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Mostrar notificaciones">
                        <i class="ti ti-bell"></i>
                        @if(!empty($unreadNotifications))
                            <span class="badge bg-red"></span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Notificaciones</h3>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable">
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><i class="ti ti-info-circle text-blue"></i></div>
                                        <div class="col text-truncate">
                                            <div class="text-body d-block">No hay notificaciones nuevas</div>
                                            <div class="d-block text-secondary text-truncate mt-n1">Todo al día</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-center">
                                <a href="/notificaciones" class="text-muted">Ver todas las notificaciones</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Menú de usuario --}}
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menú de usuario">
                        <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=206bc4&color=fff)"></span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="mt-1 small text-secondary">{{ $userRole }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="ti ti-user me-2"></i>Mi perfil
                        </a>
                        <a href="{{ route('settings.index') }}" class="dropdown-item">
                            <i class="ti ti-settings me-2"></i>Configuración
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ti ti-logout me-2"></i>Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
                @endauth

                @guest
                <div class="nav-item">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="ti ti-login me-1"></i>Ingresar con Google
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </header>

    {{-- ========== CONTENIDO PRINCIPAL ========== --}}
    <div class="page-wrapper">
        @if(View::hasSection('page-header'))
        <div class="page-header d-print-none">
            <div class="container-fluid">
                @yield('page-header')
            </div>
        </div>
        @endif

        <div class="page-body">
            <div class="container-fluid">
                {{-- Alertas flash mejoradas --}}
                @php
                    $alerts = [
                        'success' => ['icon' => 'check',          'title' => '¡Éxito!'],
                        'error'   => ['icon' => 'alert-circle',   'title' => 'Error'],
                        'warning' => ['icon' => 'alert-triangle', 'title' => 'Advertencia'],
                        'info'    => ['icon' => 'info-circle',    'title' => 'Información'],
                    ];
                @endphp
                @foreach($alerts as $type => $meta)
                    @if(session($type))
                        <div class="alert alert-{{ $type }} alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div><i class="ti ti-{{ $meta['icon'] }} icon alert-icon"></i></div>
                                <div>
                                    <h4 class="alert-title">{{ $meta['title'] }}</h4>
                                    <div class="text-secondary">{{ session($type) }}</div>
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    @endif
                @endforeach

                {{-- Contenido de la vista --}}
                @yield('content')
            </div>
        </div>

        {{-- ========== FOOTER ========== --}}
        <footer class="footer footer-transparent d-print-none">
            <div class="container-fluid">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                <a href="https://github.com/yourusername/iejag" target="_blank" class="link-secondary" rel="noopener">
                                    Versión 1.0.0
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                &copy; {{ date('Y') }} <a href="/" class="link-secondary">I.E. José Antonio Galán</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
