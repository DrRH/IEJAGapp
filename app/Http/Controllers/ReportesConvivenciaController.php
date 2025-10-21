<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\ReporteConvivencia;
use App\Models\Sede;
use App\Models\TipoAnotacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesConvivenciaController extends Controller
{
    /**
     * Mostrar reportes y estadísticas de convivencia
     */
    public function index(Request $request)
    {
        // Fechas por defecto (mes actual)
        $fechaDesde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
        $fechaHasta = $request->input('fecha_hasta', now()->endOfMonth()->format('Y-m-d'));

        // Query base
        $query = ReporteConvivencia::with([
            'estudiante.sede',
            'estudiante.matriculaActual.grupo.grado',
            'tipoAnotacion',
            'reportadoPor'
        ])->whereBetween('fecha_reporte', [$fechaDesde, $fechaHasta]);

        // Filtros
        if ($request->filled('sede_id')) {
            $sedeIds = is_array($request->sede_id) ? $request->sede_id : [$request->sede_id];
            $query->whereHas('estudiante', function($q) use ($sedeIds) {
                $q->whereIn('sede_id', $sedeIds);
            });
        }

        if ($request->filled('grado_id')) {
            $gradoIds = is_array($request->grado_id) ? $request->grado_id : [$request->grado_id];
            $query->whereHas('estudiante.matriculaActual.grupo', function($q) use ($gradoIds) {
                $q->whereIn('grado_id', $gradoIds);
            });
        }

        if ($request->filled('grupo_id')) {
            $grupoIds = is_array($request->grupo_id) ? $request->grupo_id : [$request->grupo_id];
            $query->whereHas('estudiante.matriculaActual', function($q) use ($grupoIds) {
                $q->whereIn('grupo_id', $grupoIds);
            });
        }

        if ($request->filled('tipo_anotacion_id')) {
            $tipoIds = is_array($request->tipo_anotacion_id) ? $request->tipo_anotacion_id : [$request->tipo_anotacion_id];
            $query->whereIn('tipo_anotacion_id', $tipoIds);
        }

        if ($request->filled('estado')) {
            $estados = is_array($request->estado) ? $request->estado : [$request->estado];
            $query->whereIn('estado', $estados);
        }

        // Estadísticas generales
        $totalCasos = (clone $query)->count();
        $casosAbiertos = (clone $query)->where('estado', 'abierto')->count();
        $casosCerrados = (clone $query)->where('estado', 'cerrado')->count();
        $casosConSuspension = (clone $query)->where('requirio_suspension', true)->count();
        $casosRemitidosPsicologia = (clone $query)->where('remitido_psicologia', true)->count();
        $casosConCompromiso = (clone $query)->where('requirio_compromiso', true)->count();

        // Casos por tipo de anotación
        $casosPorTipo = (clone $query)->select('tipo_anotacion_id', DB::raw('count(*) as total'))
            ->groupBy('tipo_anotacion_id')
            ->with('tipoAnotacion')
            ->get()
            ->map(function($item) {
                return [
                    'tipo' => $item->tipoAnotacion->nombre,
                    'total' => $item->total,
                    'categoria' => $item->tipoAnotacion->categoria
                ];
            });

        // Casos por mes (últimos 6 meses)
        $casosPorMes = ReporteConvivencia::select(
                DB::raw('DATE_FORMAT(fecha_reporte, "%Y-%m") as mes'),
                DB::raw('count(*) as total')
            )
            ->where('fecha_reporte', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Casos por grado
        $casosPorGrado = (clone $query)
            ->select(DB::raw('count(*) as total'))
            ->join('estudiantes', 'reportes_convivencia.estudiante_id', '=', 'estudiantes.id')
            ->join('matriculas', function($join) {
                $join->on('estudiantes.id', '=', 'matriculas.estudiante_id')
                     ->where('matriculas.estado', '=', 'activa');
            })
            ->join('grupos', 'matriculas.grupo_id', '=', 'grupos.id')
            ->join('grados', 'grupos.grado_id', '=', 'grados.id')
            ->groupBy('grados.id', 'grados.nombre')
            ->select('grados.nombre as grado', DB::raw('count(*) as total'))
            ->get();

        // Casos por sede
        $casosPorSede = (clone $query)
            ->join('estudiantes', 'reportes_convivencia.estudiante_id', '=', 'estudiantes.id')
            ->join('sedes', 'estudiantes.sede_id', '=', 'sedes.id')
            ->groupBy('sedes.id', 'sedes.nombre')
            ->select('sedes.nombre as sede', DB::raw('count(*) as total'))
            ->get();

        // Top 10 estudiantes con más reportes
        $estudiantesConMasReportes = (clone $query)
            ->select('estudiante_id', DB::raw('count(*) as total_reportes'))
            ->groupBy('estudiante_id')
            ->orderByDesc('total_reportes')
            ->limit(10)
            ->with('estudiante')
            ->get()
            ->map(function($item) {
                return [
                    'estudiante' => $item->estudiante->nombre_completo,
                    'documento' => $item->estudiante->numero_documento,
                    'grado' => $item->estudiante->matriculaActual?->grupo?->grado?->nombre ?? 'N/A',
                    'total' => $item->total_reportes
                ];
            });

        // Promedio de días para cerrar casos
        $promedioDiasCierre = ReporteConvivencia::where('estado', 'cerrado')
            ->whereNotNull('fecha_cierre')
            ->whereBetween('fecha_reporte', [$fechaDesde, $fechaHasta])
            ->get()
            ->avg(function($caso) {
                return $caso->fecha_reporte->diffInDays($caso->fecha_cierre);
            });

        // Listado de casos (paginado)
        $perPage = $request->input('per_page', 20);
        $perPage = is_numeric($perPage) && $perPage > 0 && $perPage <= 1000 ? (int)$perPage : 20;

        $casos = $query->orderBy('fecha_reporte', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Datos para filtros
        $sedes = Sede::activas()->orderBy('nombre')->get();
        $grados = Grado::orderBy('orden')->get();
        $grupos = Grupo::with('grado')->where('anio', date('Y'))->orderBy('nombre')->get();
        $tiposAnotacion = TipoAnotacion::orderBy('nombre')->get();

        return view('reportes.convivencia.index', compact(
            'casos',
            'totalCasos',
            'casosAbiertos',
            'casosCerrados',
            'casosConSuspension',
            'casosRemitidosPsicologia',
            'casosConCompromiso',
            'casosPorTipo',
            'casosPorMes',
            'casosPorGrado',
            'casosPorSede',
            'estudiantesConMasReportes',
            'promedioDiasCierre',
            'sedes',
            'grados',
            'grupos',
            'tiposAnotacion',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    /**
     * Exportar reporte a CSV
     */
    public function exportCSV(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
        $fechaHasta = $request->input('fecha_hasta', now()->endOfMonth()->format('Y-m-d'));

        $query = ReporteConvivencia::with([
            'estudiante.sede',
            'estudiante.matriculaActual.grupo.grado',
            'tipoAnotacion',
            'reportadoPor'
        ])->whereBetween('fecha_reporte', [$fechaDesde, $fechaHasta]);

        // Aplicar filtros
        if ($request->filled('sede_id')) {
            $sedeIds = is_array($request->sede_id) ? $request->sede_id : [$request->sede_id];
            $query->whereHas('estudiante', function($q) use ($sedeIds) {
                $q->whereIn('sede_id', $sedeIds);
            });
        }

        if ($request->filled('grado_id')) {
            $gradoIds = is_array($request->grado_id) ? $request->grado_id : [$request->grado_id];
            $query->whereHas('estudiante.matriculaActual.grupo', function($q) use ($gradoIds) {
                $q->whereIn('grado_id', $gradoIds);
            });
        }

        if ($request->filled('tipo_anotacion_id')) {
            $tipoIds = is_array($request->tipo_anotacion_id) ? $request->tipo_anotacion_id : [$request->tipo_anotacion_id];
            $query->whereIn('tipo_anotacion_id', $tipoIds);
        }

        if ($request->filled('estado')) {
            $estados = is_array($request->estado) ? $request->estado : [$request->estado];
            $query->whereIn('estado', $estados);
        }

        $casos = $query->orderBy('fecha_reporte', 'desc')->get();

        $filename = 'reporte_convivencia_' . date('Y-m-d_His') . '.csv';
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
                'Caso #', 'Fecha', 'Estudiante', 'Documento', 'Grado', 'Grupo', 'Sede',
                'Tipo Anotación', 'Descripción', 'Reportado Por', 'Estado',
                'Acudiente Notificado', 'Compromiso', 'Suspensión', 'Días Suspensión',
                'Remitido Psicología', 'Fecha Cierre'
            ]);

            // Datos
            foreach ($casos as $caso) {
                fputcsv($file, [
                    $caso->id,
                    $caso->fecha_reporte->format('Y-m-d'),
                    $caso->estudiante->nombre_completo,
                    $caso->estudiante->numero_documento,
                    $caso->estudiante->matriculaActual?->grupo?->grado?->nombre ?? 'N/A',
                    $caso->estudiante->matriculaActual?->grupo?->nombre ?? 'N/A',
                    $caso->estudiante->sede?->nombre ?? 'N/A',
                    $caso->tipoAnotacion->nombre,
                    $caso->descripcion_hechos,
                    $caso->reportadoPor->name,
                    ucfirst(str_replace('_', ' ', $caso->estado)),
                    $caso->acudiente_notificado ? 'Sí' : 'No',
                    $caso->requirio_compromiso ? 'Sí' : 'No',
                    $caso->requirio_suspension ? 'Sí' : 'No',
                    $caso->dias_suspension ?? 0,
                    $caso->remitido_psicologia ? 'Sí' : 'No',
                    $caso->fecha_cierre?->format('Y-m-d') ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar estadísticas a PDF
     */
    public function exportPDF(Request $request)
    {
        // Esta funcionalidad requiere una librería como DomPDF o similar
        // Por ahora retornamos un mensaje
        return back()->with('info', 'Exportación a PDF en desarrollo');
    }
}
