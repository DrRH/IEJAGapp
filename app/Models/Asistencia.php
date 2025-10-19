<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asistencia extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'matricula_id',
        'asignatura_id',
        'fecha',
        'estado',
        'hora_llegada',
        'minutos_tarde',
        'observaciones',
        'justificacion',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_llegada' => 'datetime:H:i',
        'minutos_tarde' => 'integer',
    ];

    /**
     * Matrícula del estudiante
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
     * Asignatura (opcional)
     */
    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class);
    }

    /**
     * Usuario que registró la asistencia
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Scope para asistencias presentes
     */
    public function scopePresentes($query)
    {
        return $query->where('estado', 'presente');
    }

    /**
     * Scope para asistencias ausentes
     */
    public function scopeAusentes($query)
    {
        return $query->where('estado', 'ausente');
    }

    /**
     * Scope para llegadas tarde
     */
    public function scopeTardes($query)
    {
        return $query->where('estado', 'tarde');
    }

    /**
     * Scope para asistencias por matrícula
     */
    public function scopePorMatricula($query, $matriculaId)
    {
        return $query->where('matricula_id', $matriculaId);
    }

    /**
     * Scope para asistencias por asignatura
     */
    public function scopePorAsignatura($query, $asignaturaId)
    {
        return $query->where('asignatura_id', $asignaturaId);
    }

    /**
     * Scope para asistencias por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    /**
     * Scope para asistencias en rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para asistencias con excusa médica
     */
    public function scopeConExcusaMedica($query)
    {
        return $query->where('estado', 'excusa_medica');
    }

    /**
     * Scope para asistencias con permiso
     */
    public function scopeConPermiso($query)
    {
        return $query->where('estado', 'permiso');
    }

    /**
     * Scope para asistencias justificadas
     */
    public function scopeJustificadas($query)
    {
        return $query->whereIn('estado', ['excusa_medica', 'permiso']);
    }

    /**
     * Scope para asistencias no justificadas
     */
    public function scopeNoJustificadas($query)
    {
        return $query->where('estado', 'ausente')
                    ->whereNull('justificacion');
    }

    /**
     * Determinar si la asistencia es falta
     */
    public function getEsFaltaAttribute(): bool
    {
        return in_array($this->estado, ['ausente', 'tarde']);
    }

    /**
     * Determinar si está justificada
     */
    public function getEstaJustificadaAttribute(): bool
    {
        return in_array($this->estado, ['excusa_medica', 'permiso']) || !empty($this->justificacion);
    }

    /**
     * Obtener color según estado
     */
    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'presente' => 'success',
            'tarde' => 'warning',
            'ausente' => 'danger',
            'excusa_medica' => 'info',
            'permiso' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Obtener descripción del estado
     */
    public function getDescripcionEstadoAttribute(): string
    {
        return match($this->estado) {
            'presente' => 'Presente',
            'tarde' => 'Llegó tarde (' . $this->minutos_tarde . ' min)',
            'ausente' => 'Ausente',
            'excusa_medica' => 'Excusa médica',
            'permiso' => 'Permiso',
            default => 'Sin registrar',
        };
    }
}
