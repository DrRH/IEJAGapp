<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * GET /login
     * Redirige a Google OAuth.
     */
    public function redirectToGoogle()
    {
        // Si quieres forzar consentimiento cada vez, agrega ->with(['prompt' => 'consent'])
        return Socialite::driver('google')->redirect();
        // Si tu hosting/proxy molesta con state, usa stateless():
        // return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * GET /auth/google/callback
     * Recibe el callback y autentica al usuario.
     */
    public function callback(Request $request)
    {
        try {
            // Si arriba usaste stateless(), aquí también:
            $googleUser = Socialite::driver('google')->user();
            // $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'       => $googleUser->getName() ?: $googleUser->getNickname() ?: $googleUser->getEmail(),
                    'google_id'  => $googleUser->getId(),
                    'avatar'     => $googleUser->getAvatar(),
                ],
            );

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Autenticación fallida: '.$e->getMessage());
        }
    }

    /**
     * GET /dashboard (vista protegida)
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * POST /logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
