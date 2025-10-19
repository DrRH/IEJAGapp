<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsignacionDocenteAsignatura extends Model
{
    use SoftDeletes;

    protected $table = 'asignaciones_docente_asignatura';

    protected $fillable = [
        'docente_id',
        'asignatura_id',
        'grupo_id',
        'periodo_academico_id',
        'anio',
        'horas_semanales',
        'observaciones',
        'activa',
    ];

    protected $casts = [
        'anio' => 'integer',
        'horas_semanales' => 'integer',
        'activa' => 'boolean',
    ];

    /**
     * Docente asignado
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    /**
     * Asignatura asignada
     */
    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class);
    }

    /**
     * Grupo al que se asigna
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
     * Scope para asignaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para asignaciones por docente
     */
    public function scopePorDocente($query, $docenteId)
    {
        return $query->where('docente_id', $docenteId);
    }

    /**
     * Scope para asignaciones por asignatura
     */
    public function scopePorAsignatura($query, $asignaturaId)
    {
        return $query->where('asignatura_id', $asignaturaId);
    }

    /**
     * Scope para asignaciones por grupo
     */
    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    /**
     * Scope para asignaciones por período
     */
    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_academico_id', $periodoId);
    }

    /**
     * Scope para asignaciones por año
     */
    public function scopePorAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    /**
     * Obtener descripción completa de la asignación
     */
    public function getDescripcionAttribute(): string
    {
        return "{$this->docente->nombre_completo} - {$this->asignatura->nombre} - {$this->grupo->nombre_completo}";
    }

    /**
     * Verificar si la asignación está en curso
     */
    public function getEnCursoAttribute(): bool
    {
        return $this->activa && $this->periodoAcademico->en_curso;
    }
}
