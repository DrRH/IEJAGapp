<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Estudiante;
use App\Models\ReporteConvivencia;
use App\Models\TipoAnotacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CasosController extends Controller
{
    /**
     * Mostrar lista de casos disciplinarios
     */
    public function index(Request $request)
    {
        $query = ReporteConvivencia::with([
            'estudiante.sede',
            'tipoAnotacion',
            'reportadoPor',
            'cerradoPor'
        ]);

        // Filtros
        if ($request->filled('search')) {
            $query->whereHas('estudiante', function($q) use ($request) {
                $q->where('nombres', 'like', '%' . $request->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_documento', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por estado (soporta selección múltiple)
        if ($request->filled('estado')) {
            $estados = is_array($request->estado) ? $request->estado : [$request->estado];
            $query->whereIn('estado', $estados);
        }

        // Filtro por tipo de anotación (soporta selección múltiple)
        if ($request->filled('tipo_anotacion_id')) {
            $tipoIds = is_array($request->tipo_anotacion_id) ? $request->tipo_anotacion_id : [$request->tipo_anotacion_id];
            $query->whereIn('tipo_anotacion_id', $tipoIds);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_reporte', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_reporte', '<=', $request->fecha_hasta);
        }

        // Filtro por acudiente notificado
        if ($request->filled('acudiente_notificado')) {
            $query->where('acudiente_notificado', $request->acudiente_notificado === '1');
        }

        // Filtro por suspensión
        if ($request->filled('requirio_suspension')) {
            $query->where('requirio_suspension', $request->requirio_suspension === '1');
        }

        // Filtro por remisión psicología
        if ($request->filled('remitido_psicologia')) {
            $query->where('remitido_psicologia', $request->remitido_psicologia === '1');
        }

        // Cantidad de filas por página (con validación)
        $perPage = $request->input('per_page', 20);
        $perPage = is_numeric($perPage) && $perPage > 0 && $perPage <= 1000 ? (int)$perPage : 20;

        // Ordenamiento
        $sortColumn = $request->input('sort', 'fecha_reporte');
        $sortDirection = $request->input('direction', 'desc');

        // Validar columna de ordenamiento
        $allowedSorts = ['fecha_reporte', 'estudiante_id', 'tipo_anotacion_id', 'estado', 'reportado_por'];
        if (!in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'fecha_reporte';
        }

        // Validar dirección
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Aplicar ordenamiento
        $query->orderBy($sortColumn, $sortDirection);

        $casos = $query->paginate($perPage)
            ->withQueryString();

        $tiposAnotacion = TipoAnotacion::orderBy('nombre')->get();

        return view('convivencia.casos.index', compact('casos', 'tiposAnotacion'));
    }

    /**
     * Mostrar formulario para crear caso
     */
    public function create()
    {
        $estudiantes = Estudiante::where('estado', 'activo')
            ->with(['matriculaActual.grupo.grado'])
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $tiposAnotacion = TipoAnotacion::orderBy('nombre')->get();

        // Obtener grados
        $grados = \App\Models\Grado::orderBy('nombre')->get();

        // Obtener grupos organizados por grado
        $grupos = \App\Models\Grupo::with('grado')->orderBy('grado_id')->orderBy('nombre')->get();
        $gruposPorGrado = $grupos->groupBy('grado_id')->map(function($items) {
            return $items->map(function($grupo) {
                return [
                    'id' => $grupo->id,
                    'nombre' => $grupo->nombre
                ];
            })->values();
        });

        return view('convivencia.casos.create', compact('estudiantes', 'tiposAnotacion', 'grados', 'gruposPorGrado'));
    }

    /**
     * Guardar nuevo caso
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Estudiantes involucrados
            'victimarios' => ['required', 'array', 'min:1'],
            'victimarios.*' => ['required', 'exists:estudiantes,id'],
            'victimas' => ['nullable', 'array'],
            'victimas.*' => ['nullable', 'exists:estudiantes,id'],

            // Número de acta
            'numero_acta' => ['nullable', 'string', 'max:50'],

            // Información básica
            'tipo_anotacion_id' => ['required', 'exists:tipos_anotacion,id'],
            'fecha_reporte' => ['required', 'date'],
            'hora_reporte' => ['nullable', 'date_format:H:i'],
            'descripcion_hechos' => ['required', 'string'],
            'lugar' => ['nullable', 'string', 'max:200'],
            'testigos' => ['nullable', 'string'],
            'evidencias' => ['nullable', 'string'],

            // Seguimiento
            'acciones_tomadas' => ['nullable', 'string'],
            'acudiente_notificado' => ['nullable', 'boolean'],
            'fecha_notificacion_acudiente' => ['nullable', 'date'],
            'medio_notificacion' => ['nullable', 'string', 'max:100'],
            'respuesta_acudiente' => ['nullable', 'string'],

            // Compromisos
            'requirio_compromiso' => ['nullable', 'boolean'],
            'compromiso' => ['nullable', 'string'],
            'fecha_compromiso' => ['nullable', 'date'],
            'compromiso_cumplido' => ['nullable', 'boolean'],

            // Suspensión
            'requirio_suspension' => ['nullable', 'boolean'],
            'dias_suspension' => ['nullable', 'integer', 'min:0'],
            'fecha_inicio_suspension' => ['nullable', 'date'],
            'fecha_fin_suspension' => ['nullable', 'date'],

            // Psicología
            'remitido_psicologia' => ['nullable', 'boolean'],
            'fecha_remision_psicologia' => ['nullable', 'date'],
            'observaciones_psicologia' => ['nullable', 'string'],

            // Estado
            'estado' => ['nullable', 'in:abierto,en_seguimiento,cerrado'],
            'observaciones_generales' => ['nullable', 'string'],
        ]);

        // Generar número de acta si no fue proporcionado
        if (empty($validated['numero_acta'])) {
            // Generar número de acta automático basado en el año y un consecutivo
            $year = date('Y');
            $lastActa = ReporteConvivencia::whereYear('created_at', $year)
                ->whereNotNull('numero_acta')
                ->orderBy('numero_acta', 'desc')
                ->first();

            if ($lastActa && preg_match('/(\d+)-' . $year . '$/', $lastActa->numero_acta, $matches)) {
                $consecutivo = intval($matches[1]) + 1;
            } else {
                $consecutivo = 1;
            }

            $validated['numero_acta'] = str_pad($consecutivo, 3, '0', STR_PAD_LEFT) . '-' . $year;
        }

        $numeroActa = $validated['numero_acta'];
        $victimarios = $validated['victimarios'];
        $victimas = $validated['victimas'] ?? [];

        // Remover los arrays de estudiantes del array de validated
        unset($validated['victimarios'], $validated['victimas']);

        // Asignar el usuario que reporta
        $validated['reportado_por'] = auth()->id();

        $casosCreados = [];

        // Crear un caso por cada victimario con el mismo número de acta
        foreach ($victimarios as $victimarioId) {
            $validated['estudiante_id'] = $victimarioId;
            $validated['numero_acta'] = $numeroActa;

            $caso = ReporteConvivencia::create($validated);

            // Asociar el victimario en la tabla pivot
            $caso->victimarios()->attach($victimarioId);

            // Asociar todas las víctimas en la tabla pivot
            if (!empty($victimas)) {
                $caso->victimas()->attach($victimas);
            }

            $casosCreados[] = $caso;

            // Registrar actividad
            ActivityLog::log(
                'created',
                "Caso #{$caso->id} (Acta #{$numeroActa}) creado para estudiante {$caso->estudiante->nombre_completo}",
                $caso
            );
        }

        $mensaje = count($casosCreados) === 1
            ? "Caso creado exitosamente (Acta #{$numeroActa})."
            : count($casosCreados) . " casos creados exitosamente con el Acta #{$numeroActa}.";

        return redirect()
            ->route('convivencia.casos.index')
            ->with('success', $mensaje);
    }

    /**
     * Mostrar un caso específico
     */
    public function show(ReporteConvivencia $caso)
    {
        $caso->load([
            'estudiante.sede',
            'estudiante.matriculaActual.grupo.grado',
            'tipoAnotacion',
            'reportadoPor',
            'cerradoPor',
            'victimarios.matriculaActual.grupo.grado',
            'victimas.matriculaActual.grupo.grado'
        ]);

        return view('convivencia.casos.show', compact('caso'));
    }

    /**
     * Mostrar formulario para editar caso
     */
    public function edit(ReporteConvivencia $caso)
    {
        $estudiantes = Estudiante::where('estado', 'activo')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
        $tiposAnotacion = TipoAnotacion::orderBy('nombre')->get();

        return view('convivencia.casos.edit', compact('caso', 'estudiantes', 'tiposAnotacion'));
    }

    /**
     * Actualizar caso
     */
    public function update(Request $request, ReporteConvivencia $caso)
    {
        $validated = $request->validate([
            // Número de acta
            'numero_acta' => ['nullable', 'string', 'max:50'],

            // Información básica
            'estudiante_id' => ['nullable', 'exists:estudiantes,id'],
            'tipo_anotacion_id' => ['required', 'exists:tipos_anotacion,id'],
            'fecha_reporte' => ['required', 'date'],
            'hora_reporte' => ['nullable', 'date_format:H:i'],
            'descripcion_hechos' => ['required', 'string'],
            'lugar' => ['nullable', 'string', 'max:200'],
            'testigos' => ['nullable', 'string'],
            'evidencias' => ['nullable', 'string'],

            // Campos de desarrollo y análisis
            'contexto_situacion' => ['nullable', 'string'],
            'analisis_institucional' => ['nullable', 'string'],
            'conclusiones' => ['nullable', 'string'],

            // Seguimiento
            'acciones_tomadas' => ['nullable', 'string'],
            'acciones_pedagogicas' => ['nullable', 'string'],
            'acudiente_notificado' => ['nullable', 'boolean'],
            'fecha_notificacion_acudiente' => ['nullable', 'date'],
            'medio_notificacion' => ['nullable', 'string', 'max:100'],
            'respuesta_acudiente' => ['nullable', 'string'],

            // Compromisos
            'requirio_compromiso' => ['nullable', 'boolean'],
            'compromiso' => ['nullable', 'string'],
            'compromiso_acudiente' => ['nullable', 'string'],
            'compromiso_estudiante' => ['nullable', 'string'],
            'compromiso_institucion' => ['nullable', 'string'],
            'fecha_compromiso' => ['nullable', 'date'],
            'compromiso_cumplido' => ['nullable', 'boolean'],

            // Suspensión
            'requirio_suspension' => ['nullable', 'boolean'],
            'dias_suspension' => ['nullable', 'integer', 'min:0'],
            'fecha_inicio_suspension' => ['nullable', 'date'],
            'fecha_fin_suspension' => ['nullable', 'date'],

            // Psicología
            'remitido_psicologia' => ['nullable', 'boolean'],
            'fecha_remision_psicologia' => ['nullable', 'date'],
            'observaciones_psicologia' => ['nullable', 'string'],

            // Estado
            'estado' => ['nullable', 'in:abierto,en_seguimiento,cerrado'],
            'fecha_cierre' => ['nullable', 'date'],
            'observaciones_cierre' => ['nullable', 'string'],
            'observaciones_generales' => ['nullable', 'string'],
        ]);

        // Si se está cerrando el caso, asignar quien lo cierra
        if (isset($validated['estado']) && $validated['estado'] === 'cerrado' && $caso->estado !== 'cerrado') {
            $validated['cerrado_por'] = auth()->id();
            if (empty($validated['fecha_cierre'])) {
                $validated['fecha_cierre'] = now();
            }
        }

        $caso->update($validated);

        // Registrar actividad
        ActivityLog::log(
            'updated',
            "Caso disciplinario #{$caso->id} actualizado",
            $caso
        );

        return redirect()
            ->route('convivencia.casos.acta', $caso)
            ->with('success', 'Acta de convivencia actualizada exitosamente.');
    }

    /**
     * Eliminar caso (soft delete)
     */
    public function destroy(ReporteConvivencia $caso)
    {
        $casoId = $caso->id;
        $estudianteNombre = $caso->estudiante->nombre_completo;
        $caso->delete();

        // Registrar actividad
        ActivityLog::log(
            'deleted',
            "Caso disciplinario #{$casoId} eliminado (Estudiante: {$estudianteNombre})",
            null,
            ['caso_id' => $casoId]
        );

        return redirect()
            ->route('convivencia.casos.index')
            ->with('success', 'Caso disciplinario eliminado exitosamente.');
    }

    /**
     * Exportar casos a CSV
     */
    public function export(Request $request)
    {
        $query = ReporteConvivencia::with([
            'estudiante.sede',
            'tipoAnotacion',
            'reportadoPor'
        ]);

        // Aplicar mismos filtros que en index
        if ($request->filled('search')) {
            $query->whereHas('estudiante', function($q) use ($request) {
                $q->where('nombres', 'like', '%' . $request->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_documento', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('estado')) {
            $estados = is_array($request->estado) ? $request->estado : [$request->estado];
            $query->whereIn('estado', $estados);
        }

        if ($request->filled('tipo_anotacion_id')) {
            $tipoIds = is_array($request->tipo_anotacion_id) ? $request->tipo_anotacion_id : [$request->tipo_anotacion_id];
            $query->whereIn('tipo_anotacion_id', $tipoIds);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_reporte', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_reporte', '<=', $request->fecha_hasta);
        }

        $casos = $query->orderBy('fecha_reporte', 'desc')->get();

        $filename = 'casos_disciplinarios_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($casos) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($file, [
                'Caso #', 'Fecha', 'Estudiante', 'Documento', 'Tipo Anotación',
                'Descripción', 'Lugar', 'Reportado Por', 'Estado',
                'Acudiente Notificado', 'Requirió Compromiso', 'Requirió Suspensión',
                'Días Suspensión', 'Remitido Psicología'
            ]);

            // Datos
            foreach ($casos as $caso) {
                fputcsv($file, [
                    $caso->id,
                    $caso->fecha_reporte->format('Y-m-d'),
                    $caso->estudiante->nombre_completo,
                    $caso->estudiante->numero_documento,
                    $caso->tipoAnotacion->nombre,
                    $caso->descripcion_hechos,
                    $caso->lugar,
                    $caso->reportadoPor->name,
                    $caso->estado,
                    $caso->acudiente_notificado ? 'Sí' : 'No',
                    $caso->requirio_compromiso ? 'Sí' : 'No',
                    $caso->requirio_suspension ? 'Sí' : 'No',
                    $caso->dias_suspension,
                    $caso->remitido_psicologia ? 'Sí' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Vista editable del acta de convivencia
     */
    public function acta(ReporteConvivencia $caso)
    {
        // Cargar relaciones necesarias
        $caso->load([
            'estudiante.sede',
            'estudiante.matriculaActual.grupo.grado',
            'estudiante.matriculaActual.grupo.directorGrupo',
            'tipoAnotacion',
            'reportadoPor',
            'cerradoPor',
            'estudiantesInvolucrados',
            'numerales'
        ]);

        ActivityLog::log('viewed', "Acta editable del caso #{$caso->id} - {$caso->estudiante->nombre_completo}", $caso);

        return view('convivencia.casos.acta-editable', compact('caso'));
    }

    /**
     * Vista de impresión del acta de convivencia
     */
    public function print(ReporteConvivencia $caso)
    {
        // Cargar relaciones necesarias
        $caso->load([
            'estudiante.sede',
            'estudiante.matriculaActual.grupo.grado',
            'estudiante.matriculaActual.grupo.directorGrupo',
            'tipoAnotacion',
            'reportadoPor',
            'cerradoPor'
        ]);

        ActivityLog::log('viewed', "Acta de convivencia del caso #{$caso->id} - {$caso->estudiante->nombre_completo}", $caso);

        return view('convivencia.casos.print', compact('caso'));
    }

    /**
     * Generar PDF del acta de convivencia
     */
    public function downloadPdf(ReporteConvivencia $caso)
    {
        // Cargar relaciones necesarias
        $caso->load([
            'estudiante.sede',
            'estudiante.matriculaActual.grupo.grado',
            'estudiante.matriculaActual.grupo.directorGrupo',
            'tipoAnotacion',
            'reportadoPor',
            'cerradoPor',
            'estudiantesInvolucrados',
            'numerales'
        ]);

        ActivityLog::log('downloaded', "PDF del acta de convivencia del caso #{$caso->id} - {$caso->estudiante->nombre_completo}", $caso);

        // Generar PDF usando DomPDF
        $pdf = Pdf::loadView('convivencia.casos.print', compact('caso'))
            ->setPaper('letter', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('isPhpEnabled', true)
            ->setOption('dpi', 96)
            ->setOption('defaultFont', 'Arial');

        // Agregar header y footer con callback
        $numeroActa = $caso->numero_acta ?? $caso->id;
        $logoPath = public_path('img/Escudo.jpg');

        $pdf->getDomPDF()->setCallbacks([
            'myCallbacks' => [
                'event' => 'end_frame', 'f' => function ($infos) use ($numeroActa, $logoPath) {
                    $frame = $infos['frame'];
                    if ($frame->get_node()->nodeName === 'body') {
                        $canvas = $infos['canvas'];
                        $font = $canvas->get_font_metrics()->getFont("Arial");
                        $w = $canvas->get_width();
                        $h = $canvas->get_height();

                        // HEADER - Escudo
                        if (file_exists($logoPath)) {
                            $canvas->image($logoPath, 40, 20, 50, 50);
                        }

                        // HEADER - Títulos
                        $canvas->text($w / 2 - 150, 35, "INSTITUCIÓN EDUCATIVA JOSÉ ANTONIO GALÁN", $font, 10, [0, 0, 0]);
                        $canvas->text($w / 2 - 160, 50, "ACTA DE ATENCIÓN A SITUACIÓN DE CONVIVENCIA", $font, 10, [0, 0, 0]);

                        // HEADER - Número de acta
                        $canvas->text($w - 120, 35, "ACTA N°:", $font, 9, [0, 0, 0]);
                        $canvas->text($w - 120, 50, (string)$numeroActa, $font, 11, [0, 0, 0]);

                        // HEADER - Línea separadora
                        $canvas->line(30, 75, $w - 30, 75, [0, 0, 0], 2);

                        // FOOTER - Numeración (se maneja con page_text en la vista)
                    }
                }
            ]
        ]);

        // Nombre del archivo
        $nombreEstudiante = str_replace(' ', '_', $caso->estudiante->nombre_completo);
        $filename = "Acta_Convivencia_{$numeroActa}_{$nombreEstudiante}.pdf";

        return $pdf->download($filename);
    }
}
