<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Estudiante;
use App\Models\Sede;
use Illuminate\Http\Request;

class EstudiantesController extends Controller
{
    /**
     * Mostrar lista de estudiantes
     */
    public function index(Request $request)
    {
        $query = Estudiante::with('sede');

        // Filtros
        if ($request->filled('search')) {
            $query->buscar($request->search);
        }

        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('grado')) {
            // Este filtro se implementará cuando tengamos la relación con matrículas
        }

        $estudiantes = $query->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(20)
            ->withQueryString();

        $sedes = Sede::activas()->orderBy('nombre')->get();

        return view('academico.estudiantes.index', compact('estudiantes', 'sedes'));
    }

    /**
     * Mostrar formulario para crear estudiante
     */
    public function create()
    {
        $sedes = Sede::activas()->orderBy('nombre')->get();
        return view('academico.estudiantes.create', compact('sedes'));
    }

    /**
     * Guardar nuevo estudiante
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Información personal
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'in:TI,CC,CE,RC,NUIP'],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:estudiantes,numero_documento'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'genero' => ['required', 'in:M,F,Otro'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:10'],
            'rh' => ['nullable', 'string', 'max:10'],
            'lugar_nacimiento' => ['nullable', 'string', 'max:100'],

            // Información de contacto
            'direccion' => ['nullable', 'string'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'municipio' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'celular' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],

            // Información familiar
            'nombre_acudiente' => ['required', 'string', 'max:200'],
            'telefono_acudiente' => ['required', 'string', 'max:50'],
            'email_acudiente' => ['nullable', 'email', 'max:100'],
            'parentesco_acudiente' => ['nullable', 'string', 'max:50'],
            'nombre_madre' => ['nullable', 'string', 'max:200'],
            'telefono_madre' => ['nullable', 'string', 'max:50'],
            'nombre_padre' => ['nullable', 'string', 'max:200'],
            'telefono_padre' => ['nullable', 'string', 'max:50'],

            // Información académica
            'codigo_estudiante' => ['nullable', 'string', 'max:30', 'unique:estudiantes,codigo_estudiante'],
            'sede_id' => ['nullable', 'exists:sedes,id'],
            'estrato' => ['nullable', 'in:1,2,3,4,5,6'],
            'eps' => ['nullable', 'string', 'max:100'],

            // Estado y observaciones
            'estado' => ['nullable', 'in:activo,inactivo,retirado,trasladado,graduado'],
            'fecha_ingreso' => ['nullable', 'date'],
            'observaciones_medicas' => ['nullable', 'string'],
            'observaciones_generales' => ['nullable', 'string'],

            // NEE
            'tiene_discapacidad' => ['nullable', 'boolean'],
            'tipo_discapacidad' => ['nullable', 'string', 'max:200'],
            'adaptaciones_curriculares' => ['nullable', 'string'],
        ]);

        $estudiante = Estudiante::create($validated);

        // Registrar actividad
        ActivityLog::log(
            'created',
            "Estudiante '{$estudiante->nombre_completo}' creado",
            $estudiante
        );

        return redirect()
            ->route('academico.estudiantes.index')
            ->with('success', 'Estudiante creado exitosamente.');
    }

    /**
     * Mostrar un estudiante específico
     */
    public function show(Estudiante $estudiante)
    {
        $estudiante->load('sede', 'matriculas.grupo.grado', 'reportesConvivencia');
        return view('academico.estudiantes.show', compact('estudiante'));
    }

    /**
     * Mostrar formulario para editar estudiante
     */
    public function edit(Estudiante $estudiante)
    {
        $sedes = Sede::activas()->orderBy('nombre')->get();
        return view('academico.estudiantes.edit', compact('estudiante', 'sedes'));
    }

    /**
     * Actualizar estudiante
     */
    public function update(Request $request, Estudiante $estudiante)
    {
        $validated = $request->validate([
            // Información personal
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'in:TI,CC,CE,RC,NUIP'],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:estudiantes,numero_documento,' . $estudiante->id],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'genero' => ['required', 'in:M,F,Otro'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:10'],
            'rh' => ['nullable', 'string', 'max:10'],
            'lugar_nacimiento' => ['nullable', 'string', 'max:100'],

            // Información de contacto
            'direccion' => ['nullable', 'string'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'municipio' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'celular' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],

            // Información familiar
            'nombre_acudiente' => ['required', 'string', 'max:200'],
            'telefono_acudiente' => ['required', 'string', 'max:50'],
            'email_acudiente' => ['nullable', 'email', 'max:100'],
            'parentesco_acudiente' => ['nullable', 'string', 'max:50'],
            'nombre_madre' => ['nullable', 'string', 'max:200'],
            'telefono_madre' => ['nullable', 'string', 'max:50'],
            'nombre_padre' => ['nullable', 'string', 'max:200'],
            'telefono_padre' => ['nullable', 'string', 'max:50'],

            // Información académica
            'codigo_estudiante' => ['nullable', 'string', 'max:30', 'unique:estudiantes,codigo_estudiante,' . $estudiante->id],
            'sede_id' => ['nullable', 'exists:sedes,id'],
            'estrato' => ['nullable', 'in:1,2,3,4,5,6'],
            'eps' => ['nullable', 'string', 'max:100'],

            // Estado y observaciones
            'estado' => ['nullable', 'in:activo,inactivo,retirado,trasladado,graduado'],
            'fecha_ingreso' => ['nullable', 'date'],
            'fecha_retiro' => ['nullable', 'date'],
            'motivo_retiro' => ['nullable', 'string'],
            'observaciones_medicas' => ['nullable', 'string'],
            'observaciones_generales' => ['nullable', 'string'],

            // NEE
            'tiene_discapacidad' => ['nullable', 'boolean'],
            'tipo_discapacidad' => ['nullable', 'string', 'max:200'],
            'adaptaciones_curriculares' => ['nullable', 'string'],
        ]);

        $estudiante->update($validated);

        // Registrar actividad
        ActivityLog::log(
            'updated',
            "Estudiante '{$estudiante->nombre_completo}' actualizado",
            $estudiante
        );

        return redirect()
            ->route('academico.estudiantes.index')
            ->with('success', 'Estudiante actualizado exitosamente.');
    }

    /**
     * Eliminar estudiante (soft delete)
     */
    public function destroy(Estudiante $estudiante)
    {
        $nombreCompleto = $estudiante->nombre_completo;
        $estudiante->delete();

        // Registrar actividad
        ActivityLog::log(
            'deleted',
            "Estudiante '{$nombreCompleto}' eliminado",
            null,
            ['estudiante_id' => $estudiante->id]
        );

        return redirect()
            ->route('academico.estudiantes.index')
            ->with('success', 'Estudiante eliminado exitosamente.');
    }

    /**
     * Exportar estudiantes a CSV
     */
    public function export()
    {
        $estudiantes = Estudiante::with('sede')->get();

        $filename = 'estudiantes_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($estudiantes) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($file, [
                'Código', 'Nombres', 'Apellidos', 'Tipo Doc', 'Número Doc',
                'Fecha Nacimiento', 'Género', 'Dirección', 'Teléfono', 'Email',
                'Acudiente', 'Tel. Acudiente', 'Sede', 'Estado'
            ]);

            // Datos
            foreach ($estudiantes as $est) {
                fputcsv($file, [
                    $est->codigo_estudiante,
                    $est->nombres,
                    $est->apellidos,
                    $est->tipo_documento,
                    $est->numero_documento,
                    $est->fecha_nacimiento->format('Y-m-d'),
                    $est->genero,
                    $est->direccion,
                    $est->telefono,
                    $est->email,
                    $est->nombre_acudiente,
                    $est->telefono_acudiente,
                    $est->sede?->nombre,
                    $est->estado,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
