<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Sheets;
use Google_Service_Docs;
use Google_Service_Calendar;
use Illuminate\Support\Facades\Session;

class GoogleService
{
    private Google_Client $client;

    public function __construct()
    {
        $this->client = new Google_Client();

        // === Configuración base desde config/services.php ===
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));

        // ¡CRÍTICO!: redirect exacto. Si cambia, Google responde 404.
        $this->client->setRedirectUri(config('services.google.redirect'));

        // Scopes: primero OpenID + perfil/correo; luego APIs extra que necesites
        $this->client->setScopes([
            'openid',
            'email',
            'profile',
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/spreadsheets',
            'https://www.googleapis.com/auth/documents',
            'https://www.googleapis.com/auth/calendar',
        ]);

        // Buenas prácticas para OAuth web
        $this->client->setAccessType('offline');          // refresh token
        $this->client->setPrompt('consent');              // asegura obtener refresh en pruebas
        $this->client->setIncludeGrantedScopes(true);     // incremental auth
    }

    /**
     * Devuelve el cliente para usos avanzados.
     */
    public function getClient(): Google_Client
    {
        return $this->client;
    }

    /**
     * URL de autorización (para redirigir al usuario).
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Intercambia el "code" por tokens y los guarda en sesión.
     *
     * @return array<string,mixed> Token completo devuelto por Google
     */
    public function authenticate(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            $msg = $token['error_description'] ?? $token['error'];
            throw new \RuntimeException("Google OAuth error: {$msg}");
        }

        Session::put('google_token', $token);
        return $token;
    }

    /**
     * Establece el access token en el cliente y refresca si está vencido.
     *
     * @param array<string,mixed>|string $token
     */
    public function setAccessToken(array|string $token): void
    {
        if (is_string($token)) {
            $decoded = json_decode($token, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $token = $decoded;
            } else {
                // si te pasan solo el access_token como string
                $token = ['access_token' => $token];
            }
        }

        $this->client->setAccessToken($token);

        // Refrescar si expiró y hay refresh token
        if ($this->client->isAccessTokenExpired() && $this->client->getRefreshToken()) {
            $refreshed = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());

            if (!isset($refreshed['error'])) {
                $merged = array_merge((array) $token, $refreshed);
                Session::put('google_token', $merged);
                $this->client->setAccessToken($merged);
            }
        }
    }

    /**
     * Permite sobreescribir el redirect URI en caliente (poco común, útil para debug).
     */
    public function withRedirect(string $redirectUri): self
    {
        $this->client->setRedirectUri($redirectUri);
        return $this;
    }

    /**
     * Revoca el token actual y limpia sesión.
     */
    public function revoke(): void
    {
        $current = $this->client->getAccessToken();
        if ($current) {
            $this->client->revokeToken();
        }
        Session::forget('google_token');
    }
}
