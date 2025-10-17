<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // Si existe la ruta 'login', úsala; de lo contrario, manda a 'home'
            return route_exists('login') ? route('login') : route('home');
        }
        return null;
    }
}

/**
 * Helper pequeño para evitar errores si la ruta no existe.
 */
if (! function_exists('route_exists')) {
    function route_exists(string $name): bool
    {
        try {
            return app('router')->has($name);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
