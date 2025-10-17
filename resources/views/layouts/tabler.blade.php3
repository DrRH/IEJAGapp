{{-- resources/views/layouts/tabler.blade.php --}}
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
    $userRole = 'Usuario';
    if (auth()->check()) {
        $u = auth()->user();
        if (method_exists($u, 'getRoleNames')) {
            $userRole = $u->getRoleNames()->first() ?? 'Usuario';
        } elseif (method_exists($u, 'roles')) {
            $userRole = optional(optional($u->roles)->first())->display_name
                ?? optional(optional($u->roles)->first())->name
                ?? 'Usuario';
        } elseif (isset($u->role) || isset($u->role_name)) {
            $userRole = $u->role_name ?? $u->role ?? 'Usuario';
        }
    }
@endphp

<div class="page">
    <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <h1 class="navbar-brand navbar-brand-autodark">
                <a href="{{ route('dashboard') }}">
                    <span class="navbar-brand-text">I.E. José Antonio Galán</span>
                </a>
            </h1>

            @auth
            <div class="navbar-nav flex-row d-lg-none">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menú de usuario">
                        <span class="avatar avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="mt-1 small text-secondary">{{ $userRole }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">Mi perfil</a>
                        <a href="{{ route('settings.index') }}" class="dropdown-item">Configuración</a>
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

            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-home"></i></span>
                            <span class="nav-link-title">Inicio</span>
                        </a>
                    </li>

                    {{-- … (resto del menú lateral igual que ya tienes) … --}}

                    @endauth

                    <li class="nav-item"><div class="dropdown-divider my-2"></div></li>

                    <li class="nav-item">
                        <a class="nav-link" href="https://josegalan.edupage.org/timetable/" target="_blank">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar"></i></span>
                            <span class="nav-link-title">Horarios (Edupage)</span>
                            <span class="badge badge-sm bg-blue-lt ms-auto">Externo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.canva.com/design/DAGKjmYMirw/2EUSYUXQHZG9gMTG3Z928w/edit" target="_blank">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-bell"></i></span>
                            <span class="nav-link-title">Notificados (Canva)</span>
                            <span class="badge badge-sm bg-green-lt ms-auto">Externo</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <header class="navbar navbar-expand-md d-print-none">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-nav flex-row order-md-last">
                @auth
                <div class="nav-item dropdown d-none d-md-flex me-3">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Mostrar notificaciones">
                        <i class="ti ti-bell"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Notificaciones</h3></div>
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

    <div class="page-wrapper">
        @if(View::hasSection('page-header'))
        <div class="page-header d-print-none"><div class="container-fluid">@yield('page-header')</div></div>
        @endif

        <div class="page-body">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex"><div><i class="ti ti-check icon alert-icon"></i></div>
                            <div><h4 class="alert-title">¡Éxito!</h4><div class="text-secondary">{{ session('success') }}</div></div>
                        </div><a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex"><div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                            <div><h4 class="alert-title">Error</h4><div class="text-secondary">{{ session('error') }}</div></div>
                        </div><a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

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
