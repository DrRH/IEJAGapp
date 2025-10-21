<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ComiteConvivencia;
use App\Models\ReporteConvivencia;
use Illuminate\Http\Request;

class ComitesConvivenciaController extends Controller
{
    /**
     * Mostrar lista de actas del comité
     */
    public function index(Request $request)
    {
        $query = ComiteConvivencia::with(['creadoPor', 'aprobadoPor']);

        // Filtros
        if ($request->filled('anio')) {
            $query->porAnio($request->anio);
        } else {
            $query->porAnio(date('Y'));
        }

        if ($request->filled('estado')) {
            $estados = is_array($request->estado) ? $request->estado : [$request->estado];
            $query->whereIn('estado', $estados);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('numero_acta', 'like', '%' . $request->search . '%')
                  ->orWhere('resumen_ejecutivo', 'like', '%' . $request->search . '%')
                  ->orWhere('lugar', 'like', '%' . $request->search . '%');
            });
        }

        // Ordenamiento
        $sortColumn = $request->input('sort', 'fecha_reunion');
        $sortDirection = $request->input('direction', 'desc');

        $allowedSorts = ['numero_acta', 'fecha_reunion', 'estado'];
        if (!in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'fecha_reunion';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortColumn, $sortDirection);

        // Paginación
        $perPage = $request->input('per_page', 20);
        $perPage = is_numeric($perPage) && $perPage > 0 && $perPage <= 1000 ? (int)$perPage : 20;

        $actas = $query->paginate($perPage)->withQueryString();

        // Años disponibles
        $anios = ComiteConvivencia::selectRaw('YEAR(fecha_reunion) as anio')
            ->groupBy('anio')
            ->orderByDesc('anio')
            ->pluck('anio');

        if ($anios->isEmpty()) {
            $anios = collect([date('Y')]);
        }

        return view('actas.comite-convivencia.index', compact('actas', 'anios'));
    }

    /**
     * Mostrar formulario para crear acta
     */
    public function create()
    {
        // Generar número de acta automáticamente
        $numeroActa = ComiteConvivencia::generarNumeroActa();

        // Obtener casos abiertos para revisar
        $casosAbiertos = ReporteConvivencia::with('estudiante', 'tipoAnotacion')
            ->whereIn('estado', ['abierto', 'en_seguimiento'])
            ->orderBy('fecha_reporte', 'desc')
            ->get();

        return view('actas.comite-convivencia.create', compact('numeroActa', 'casosAbiertos'));
    }

    /**
     * Guardar nueva acta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_acta' => ['required', 'string', 'max:20', 'unique:comites_convivencia,numero_acta'],
            'fecha_reunion' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i'],
            'lugar' => ['required', 'string', 'max:200'],
            'resumen_ejecutivo' => ['required', 'string', 'max:500'],
            'asistentes' => ['required', 'string'],
            'invitados' => ['nullable', 'string'],
            'orden_dia' => ['required', 'string'],
            'desarrollo' => ['required', 'string'],
            'casos_revisados' => ['nullable', 'array'],
            'decisiones' => ['nullable', 'string'],
            'compromisos' => ['nullable', 'string'],
            'seguimiento_compromisos_anteriores' => ['nullable', 'string'],
            'proxima_reunion' => ['nullable', 'date'],
            'temas_proxima_reunion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'estado' => ['required', 'in:borrador,aprobada,publicada'],
        ]);

        $validated['creado_por'] = auth()->id();

        // Si se aprueba directamente, registrar
        if ($validated['estado'] === 'aprobada') {
            $validated['aprobado_por'] = auth()->id();
            $validated['fecha_aprobacion'] = now();
        }

        $acta = ComiteConvivencia::create($validated);

        // Registrar actividad
        ActivityLog::log(
            'created',
            "Acta Comité Convivencia '{$acta->numero_acta}' creada",
            $acta
        );

        return redirect()
            ->route('actas.comite-convivencia.show', $acta)
            ->with('success', 'Acta creada exitosamente.');
    }

    /**
     * Mostrar acta completa
     */
    public function show(ComiteConvivencia $acta)
    {
        $acta->load(['creadoPor', 'aprobadoPor']);

        // Cargar casos revisados si existen
        $casosRevisados = [];
        if ($acta->casos_revisados && is_array($acta->casos_revisados)) {
            $casosRevisados = ReporteConvivencia::with('estudiante', 'tipoAnotacion')
                ->whereIn('id', $acta->casos_revisados)
                ->get();
        }

        return view('actas.comite-convivencia.show', compact('acta', 'casosRevisados'));
    }

    /**
     * Mostrar formulario para editar acta
     */
    public function edit(ComiteConvivencia $acta)
    {
        // Solo se pueden editar borradores
        if (!$acta->es_borrador) {
            return back()->with('error', 'Solo se pueden editar actas en estado borrador.');
        }

        // Obtener casos abiertos para revisar
        $casosAbiertos = ReporteConvivencia::with('estudiante', 'tipoAnotacion')
            ->whereIn('estado', ['abierto', 'en_seguimiento'])
            ->orderBy('fecha_reporte', 'desc')
            ->get();

        return view('actas.comite-convivencia.edit', compact('acta', 'casosAbiertos'));
    }

    /**
     * Actualizar acta
     */
    public function update(Request $request, ComiteConvivencia $acta)
    {
        // Solo se pueden editar borradores
        if (!$acta->es_borrador) {
            return back()->with('error', 'Solo se pueden editar actas en estado borrador.');
        }

        $validated = $request->validate([
            'numero_acta' => ['required', 'string', 'max:20', 'unique:comites_convivencia,numero_acta,' . $acta->id],
            'fecha_reunion' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i'],
            'lugar' => ['required', 'string', 'max:200'],
            'resumen_ejecutivo' => ['required', 'string', 'max:500'],
            'asistentes' => ['required', 'string'],
            'invitados' => ['nullable', 'string'],
            'orden_dia' => ['required', 'string'],
            'desarrollo' => ['required', 'string'],
            'casos_revisados' => ['nullable', 'array'],
            'decisiones' => ['nullable', 'string'],
            'compromisos' => ['nullable', 'string'],
            'seguimiento_compromisos_anteriores' => ['nullable', 'string'],
            'proxima_reunion' => ['nullable', 'date'],
            'temas_proxima_reunion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'estado' => ['required', 'in:borrador,aprobada,publicada'],
        ]);

        // Si se aprueba, registrar
        if ($validated['estado'] === 'aprobada' && $acta->estado !== 'aprobada') {
            $validated['aprobado_por'] = auth()->id();
            $validated['fecha_aprobacion'] = now();
        }

        $acta->update($validated);

        // Registrar actividad
        ActivityLog::log(
            'updated',
            "Acta Comité Convivencia '{$acta->numero_acta}' actualizada",
            $acta
        );

        return redirect()
            ->route('actas.comite-convivencia.show', $acta)
            ->with('success', 'Acta actualizada exitosamente.');
    }

    /**
     * Eliminar acta
     */
    public function destroy(ComiteConvivencia $acta)
    {
        // Solo se pueden eliminar borradores
        if (!$acta->es_borrador) {
            return back()->with('error', 'Solo se pueden eliminar actas en estado borrador.');
        }

        $numeroActa = $acta->numero_acta;
        $acta->delete();

        // Registrar actividad
        ActivityLog::log(
            'deleted',
            "Acta Comité Convivencia '{$numeroActa}' eliminada",
            null,
            ['acta_id' => $acta->id]
        );

        return redirect()
            ->route('actas.comite-convivencia.index')
            ->with('success', 'Acta eliminada exitosamente.');
    }

    /**
     * Aprobar acta
     */
    public function aprobar(ComiteConvivencia $acta)
    {
        if ($acta->esta_aprobada || $acta->esta_publicada) {
            return back()->with('error', 'El acta ya está aprobada o publicada.');
        }

        $acta->update([
            'estado' => 'aprobada',
            'aprobado_por' => auth()->id(),
            'fecha_aprobacion' => now(),
        ]);

        // Registrar actividad
        ActivityLog::log(
            'updated',
            "Acta Comité Convivencia '{$acta->numero_acta}' aprobada",
            $acta
        );

        return back()->with('success', 'Acta aprobada exitosamente.');
    }

    /**
     * Publicar acta
     */
    public function publicar(ComiteConvivencia $acta)
    {
        if (!$acta->esta_aprobada) {
            return back()->with('error', 'El acta debe estar aprobada antes de publicarse.');
        }

        $acta->update(['estado' => 'publicada']);

        // Registrar actividad
        ActivityLog::log(
            'updated',
            "Acta Comité Convivencia '{$acta->numero_acta}' publicada",
            $acta
        );

        return back()->with('success', 'Acta publicada exitosamente.');
    }

    /**
     * Exportar acta a PDF
     */
    public function exportPDF(ComiteConvivencia $acta)
    {
        // Funcionalidad para implementar con librería PDF
        return back()->with('info', 'Exportación a PDF en desarrollo');
    }
}
