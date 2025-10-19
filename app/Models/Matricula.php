<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matricula extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'estudiante_id',
        'grupo_id',
        'periodo_academico_id',
        'anio',
        'numero_matricula',
        'fecha_matricula',
        'tipo_matricula',
        'jornada',
        'estado',
        'fecha_retiro',
        'motivo_retiro',
        'repite_anio',
        'observaciones',
    ];

    protected $casts = [
        'anio' => 'integer',
        'fecha_matricula' => 'date',
        'fecha_retiro' => 'date',
        'repite_anio' => 'boolean',
    ];

    /**
     * Estudiante matriculado
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * Grupo al que pertenece
     */
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    /**
     * Período académico
     */
    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class);
    }

    /**
     * Calificaciones de la matrícula
     */
    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    /**
     * Asistencias de la matrícula
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Scope para matrículas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    /**
     * Scope para matrículas por año
     */
    public function scopePorAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    /**
     * Scope para matrículas por estudiante
     */
    public function scopePorEstudiante($query, $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }

    /**
     * Scope para matrículas por grupo
     */
    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    /**
     * Scope para matrículas por período
     */
    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_academico_id', $periodoId);
    }

    /**
     * Scope para estudiantes que repiten año
     */
    public function scopeRepitenAnio($query)
    {
        return $query->where('repite_anio', true);
    }

    /**
     * Scope para matrículas nuevas
     */
    public function scopeNuevas($query)
    {
        return $query->where('tipo_matricula', 'nueva');
    }

    /**
     * Verificar si la matrícula está activa
     */
    public function getEstaActivaAttribute(): bool
    {
        return $this->estado === 'activa';
    }

    /**
     * Obtener promedio de calificaciones
     */
    public function getPromedioAttribute(): ?float
    {
        $calificaciones = $this->calificaciones()
            ->whereNotNull('nota_periodo')
            ->get();

        if ($calificaciones->isEmpty()) {
            return null;
        }

        return round($calificaciones->avg('nota_periodo'), 2);
    }

    /**
     * Obtener total de inasistencias
     */
    public function getTotalInasistenciasAttribute(): int
    {
        return $this->asistencias()
            ->whereIn('estado', ['ausente', 'tarde'])
            ->count();
    }
}
