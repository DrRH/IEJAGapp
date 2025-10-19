<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Mostrar listado de logs de actividad
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por acción
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtro por tipo de modelo
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Búsqueda por descripción
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50)->withQueryString();

        // Obtener listas para filtros
        $actions = ActivityLog::distinct()->pluck('action')->sort();
        $modelTypes = ActivityLog::distinct()->whereNotNull('model_type')->pluck('model_type')->sort();

        return view('administracion.logs.index', compact('logs', 'actions', 'modelTypes'));
    }

    /**
     * Mostrar detalles de un log específico
     */
    public function show(ActivityLog $log)
    {
        $log->load('user');
        return view('administracion.logs.show', compact('log'));
    }

    /**
     * Eliminar logs antiguos (limpieza)
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $date = now()->subDays($request->days);
        $deleted = ActivityLog::where('created_at', '<', $date)->delete();

        return back()->with('success', "Se eliminaron {$deleted} registros de actividad anteriores a " . $date->format('d/m/Y'));
    }
}
