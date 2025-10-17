<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Google_Service_Oauth2;

class GoogleAuthController extends Controller
{
    protected GoogleService $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Redirige a Google para consentir.
     */
    public function redirectToGoogle(Request $request)
    {
        $client = $this->googleService->getClient(); // Asegúrate que fije redirectUri = env('GOOGLE_REDIRECT_URI')
        $authUrl = $client->createAuthUrl();

        Log::info('Google OAuth redirect URL generada', ['url' => $authUrl]);

        return redirect()->away($authUrl);
    }

    /**
     * Callback de Google.
     * GET /auth/google/callback?code=... o ?error=access_denied
     */
    public function handleGoogleCallback(Request $request)
    {
        Log::info('Google Callback iniciado', [
            'has_code'  => $request->has('code'),
            'has_error' => $request->has('error'),
            'params'    => $request->all(),
        ]);

        if ($request->has('error')) {
            Log::warning('Usuario canceló consentimiento de Google', ['error' => $request->get('error')]);
            return redirect()->route('home')->with('error', 'Autenticación cancelada.');
        }

        if (!$request->filled('code')) {
            Log::error('Callback sin parámetro "code"');
            return redirect()->route('home')->with('error', 'Código de autorización ausente.');
        }

        try {
            $code  = (string) $request->get('code');
            Log::info('Código de Google recibido', ['code_preview' => substr($code, 0, 20) . '...']);

            // Intercambio de código por tokens
            $token = $this->googleService->authenticate($code);
            $this->googleService->setAccessToken($token);

            // Obtener perfil del usuario
            $client = $this->googleService->getClient();
            $oauth  = new Google_Service_Oauth2($client);
            $gUser  = $oauth->userinfo->get(); // ->email, ->name, ->id, ->picture

            // Crear/actualizar usuario local
            $user = User::updateOrCreate(
                ['email' => $gUser->email],
                [
                    'name'         => $gUser->name ?: $gUser->email,
                    'google_id'    => $gUser->id,
                    'avatar'       => $gUser->picture ?? null,
                    'google_token' => json_encode($token, JSON_UNESCAPED_UNICODE),
                ]
            );

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            Log::info('Login con Google correcto', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->intended(route('dashboard'))->with('success', '¡Bienvenido!');
        } catch (\Throwable $e) {
            Log::error('Error en callback de Google', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->route('home')->with('error', 'Error autenticando con Google: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
