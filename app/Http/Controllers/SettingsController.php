<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        // Carga valores existentes (con defaults)
        $settings = [
            // Información básica
            'institution_name' => Setting::get('institution_name', config('app.name')),
            'institution_city' => Setting::get('institution_city', 'Medellín'),
            'institution_address' => Setting::get('institution_address', ''),
            'institution_department' => Setting::get('institution_department', 'Antioquia'),
            'institution_country' => Setting::get('institution_country', 'Colombia'),

            // Datos legales
            'institution_nit' => Setting::get('institution_nit', ''),
            'institution_dane' => Setting::get('institution_dane', ''),
            'institution_resolution' => Setting::get('institution_resolution', ''),
            'institution_resolution_date' => Setting::get('institution_resolution_date', ''),

            // Contacto
            'contact_email' => Setting::get('contact_email', 'info@josegalan.edu.co'),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_cellphone' => Setting::get('contact_cellphone', ''),
            'contact_website' => Setting::get('contact_website', 'https://josegalan.edu.co'),

            // Directivos
            'rector_name' => Setting::get('rector_name', ''),
            'rector_email' => Setting::get('rector_email', ''),
            'coordinator_name' => Setting::get('coordinator_name', ''),
            'coordinator_email' => Setting::get('coordinator_email', ''),

            // Académico
            'academic_year' => Setting::get('academic_year', date('Y')),
            'academic_calendar_start' => Setting::get('academic_calendar_start', ''),
            'academic_calendar_end' => Setting::get('academic_calendar_end', ''),
            'education_levels' => Setting::get('education_levels', 'Preescolar, Básica Primaria, Básica Secundaria, Media'),

            // Redes sociales
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_twitter' => Setting::get('social_twitter', ''),
            'social_youtube' => Setting::get('social_youtube', ''),

            // Sistema
            'theme' => Setting::get('theme', 'light'),
            'timezone' => Setting::get('timezone', 'America/Bogota'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // Información básica
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_city' => ['required', 'string', 'max:255'],
            'institution_address' => ['nullable', 'string', 'max:500'],
            'institution_department' => ['required', 'string', 'max:100'],
            'institution_country' => ['required', 'string', 'max:100'],

            // Datos legales
            'institution_nit' => ['nullable', 'string', 'max:50'],
            'institution_dane' => ['nullable', 'string', 'max:50'],
            'institution_resolution' => ['nullable', 'string', 'max:255'],
            'institution_resolution_date' => ['nullable', 'date'],

            // Contacto
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_cellphone' => ['nullable', 'string', 'max:50'],
            'contact_website' => ['nullable', 'url', 'max:255'],

            // Directivos
            'rector_name' => ['nullable', 'string', 'max:255'],
            'rector_email' => ['nullable', 'email', 'max:255'],
            'coordinator_name' => ['nullable', 'string', 'max:255'],
            'coordinator_email' => ['nullable', 'email', 'max:255'],

            // Académico
            'academic_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'academic_calendar_start' => ['nullable', 'date'],
            'academic_calendar_end' => ['nullable', 'date', 'after:academic_calendar_start'],
            'education_levels' => ['nullable', 'string', 'max:500'],

            // Redes sociales
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],

            // Sistema
            'theme' => ['required', 'in:light,dark'],
            'timezone' => ['required', 'string', 'max:100'],
        ]);

        $changedSettings = [];
        foreach ($data as $key => $value) {
            $oldValue = Setting::get($key);
            if ($oldValue !== $value) {
                $changedSettings[$key] = [
                    'old' => $oldValue,
                    'new' => $value
                ];
            }
            Setting::set($key, $value);
        }

        // Registrar actividad si hubo cambios
        if (!empty($changedSettings)) {
            ActivityLog::log(
                'updated',
                'Configuración institucional actualizada',
                null,
                ['changed_settings' => array_keys($changedSettings)]
            );
        }

        return back()->with('success', 'Configuración institucional actualizada correctamente.');
    }
}
