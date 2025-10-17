<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        // Carga valores existentes (con defaults)
        $settings = [
            'institution_name' => Setting::get('institution_name', config('app.name')),
            'institution_city' => Setting::get('institution_city', 'Medellín'),
            'contact_email'    => Setting::get('contact_email', 'info@josegalan.edu.co'),
            'theme'            => Setting::get('theme', 'light'), // light | dark
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'institution_name' => ['required','string','max:255'],
            'institution_city' => ['required','string','max:255'],
            'contact_email'    => ['required','email','max:255'],
            'theme'            => ['required','in:light,dark'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Configuración guardada.');
    }
}
