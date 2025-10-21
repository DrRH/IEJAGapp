<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReporteConvivencia extends Model
{
    use SoftDeletes;

    protected $table = 'reportes_convivencia';

    protected $fillable = [
        'numero_acta',
        'estudiante_id',
        'tipo_anotacion_id',
        'reportado_por',
        'fecha_reporte',
        'hora_reporte',
        'descripcion_hechos',
        'lugar',
        'testigos',
        'evidencias',
        'contexto_situacion',
        'analisis_institucional',
        'conclusiones',
        'acciones_tomadas',
        'acciones_pedagogicas',
        'acudiente_notificado',
        'fecha_notificacion_acudiente',
        'medio_notificacion',
        'respuesta_acudiente',
        'requirio_compromiso',
        'compromiso',
        'compromiso_acudiente',
        'compromiso_estudiante',
        'compromiso_institucion',
        'fecha_compromiso',
        'compromiso_cumplido',
        'requirio_suspension',
        'dias_suspension',
        'fecha_inicio_suspension',
        'fecha_fin_suspension',
        'remitido_psicologia',
        'fecha_remision_psicologia',
        'observaciones_psicologia',
        'estado',
        'fecha_cierre',
        'cerrado_por',
        'observaciones_cierre',
        'observaciones_generales',
    ];

    protected $casts = [
        'fecha_reporte' => 'date',
        'hora_reporte' => 'datetime:H:i',
        'acudiente_notificado' => 'boolean',
        'fecha_notificacion_acudiente' => 'date',
        'requirio_compromiso' => 'boolean',
        'fecha_compromiso' => 'date',
        'compromiso_cumplido' => 'boolean',
        'requirio_suspension' => 'boolean',
        'dias_suspension' => 'integer',
        'fecha_inicio_suspension' => 'date',
        'fecha_fin_suspension' => 'date',
        'remitido_psicologia' => 'boolean',
        'fecha_remision_psicologia' => 'date',
        'fecha_cierre' => 'date',
    ];

    /**
     * Estudiante del reporte
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * Tipo de anotación
     */
    public function tipoAnotacion(): BelongsTo
    {
        return $this->belongsTo(TipoAnotacion::class);
    }

    /**
     * Usuario que reporta
     */
    public function reportadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reportado_por');
    }

    /**
     * Usuario que cierra el reporte
     */
    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    /**
     * Estudiantes involucrados como victimarios
     */
    public function victimarios(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'estudiantes_involucrados', 'reporte_id', 'estudiante_id')
            ->wherePivot('rol', 'victimario')
            ->withTimestamps();
    }

    /**
     * Estudiantes involucrados como víctimas
     */
    public function victimas(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'estudiantes_involucrados', 'reporte_id', 'estudiante_id')
            ->wherePivot('rol', 'victima')
            ->withTimestamps();
    }

    /**
     * Todos los estudiantes involucrados (victimarios y víctimas)
     */
    public function estudiantesInvolucrados(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'estudiantes_involucrados', 'reporte_id', 'estudiante_id')
            ->withPivot('rol')
            ->withTimestamps();
    }

    /**
     * Reportes relacionados por mismo número de acta
     */
    public function reportesRelacionados()
    {
        return $this->where('numero_acta', $this->numero_acta)
            ->where('id', '!=', $this->id);
    }

    /**
     * Númerales del manual de convivencia aplicables a este reporte
     */
    public function numerales(): BelongsToMany
    {
        return $this->belongsToMany(TipoAnotacion::class, 'reporte_numeral', 'reporte_id', 'tipo_anotacion_id')
            ->withTimestamps();
    }

    /**
     * Scope para reportes abiertos
     */
    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierto');
    }

    /**
     * Scope para reportes en seguimiento
     */
    public function scopeEnSeguimiento($query)
    {
        return $query->where('estado', 'en_seguimiento');
    }

    /**
     * Scope para reportes cerrados
     */
    public function scopeCerrados($query)
    {
        return $query->where('estado', 'cerrado');
    }

    /**
     * Scope para reportes por estudiante
     */
    public function scopePorEstudiante($query, $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }

    /**
     * Scope para reportes por tipo de anotación
     */
    public function scopePorTipoAnotacion($query, $tipoAnotacionId)
    {
        return $query->where('tipo_anotacion_id', $tipoAnotacionId);
    }

    /**
     * Scope para reportes por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_reporte', $fecha);
    }

    /**
     * Scope para reportes entre fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_reporte', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para reportes con acudiente notificado
     */
    public function scopeConAcudienteNotificado($query)
    {
        return $query->where('acudiente_notificado', true);
    }

    /**
     * Scope para reportes con suspensión
     */
    public function scopeConSuspension($query)
    {
        return $query->where('requirio_suspension', true);
    }

    /**
     * Scope para reportes remitidos a psicología
     */
    public function scopeRemitidosPsicologia($query)
    {
        return $query->where('remitido_psicologia', true);
    }

    /**
     * Scope para reportes con compromiso
     */
    public function scopeConCompromiso($query)
    {
        return $query->where('requirio_compromiso', true);
    }

    /**
     * Scope para compromisos cumplidos
     */
    public function scopeCompromisosCumplidos($query)
    {
        return $query->where('compromiso_cumplido', true);
    }

    /**
     * Scope para compromisos incumplidos
     */
    public function scopeCompromisosIncumplidos($query)
    {
        return $query->where('requirio_compromiso', true)
                    ->where('compromiso_cumplido', false);
    }

    /**
     * Verificar si el reporte está abierto
     */
    public function getEstaAbiertoAttribute(): bool
    {
        return $this->estado === 'abierto';
    }

    /**
     * Verificar si el reporte está cerrado
     */
    public function getEstaCerradoAttribute(): bool
    {
        return $this->estado === 'cerrado';
    }

    /**
     * Obtener días transcurridos desde el reporte
     */
    public function getDiasTranscurridosAttribute(): int
    {
        return $this->fecha_reporte->diffInDays(now());
    }

    /**
     * Verificar si está en suspensión activa
     */
    public function getEnSuspensionActivaAttribute(): bool
    {
        if (!$this->requirio_suspension) {
            return false;
        }

        return now()->between($this->fecha_inicio_suspension, $this->fecha_fin_suspension);
    }

    /**
     * Obtener color según estado
     */
    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'abierto' => 'danger',
            'en_seguimiento' => 'warning',
            'cerrado' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Obtener gravedad del reporte
     */
    public function getGravedadAttribute(): string
    {
        return $this->tipoAnotacion->categoria;
    }

    /**
     * Verificar si requiere atención urgente
     */
    public function getRequiereAtencionUrgenteAttribute(): bool
    {
        if ($this->estado === 'cerrado') {
            return false;
        }

        // Reportes graves o muy graves abiertos por más de 3 días
        if (in_array($this->tipoAnotacion->categoria, ['grave', 'muy_grave'])) {
            return $this->dias_transcurridos > 3;
        }

        // Compromisos incumplidos
        if ($this->requirio_compromiso && $this->compromiso_cumplido === false) {
            return true;
        }

        return false;
    }
}
