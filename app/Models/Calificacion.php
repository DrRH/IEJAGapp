<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calificacion extends Model
{
    use SoftDeletes;

    protected $table = 'calificaciones';

    protected $fillable = [
        'matricula_id',
        'asignatura_id',
        'periodo_academico_id',
        'docente_id',
        'nota_periodo',
        'nota_acumulada',
        'nota_final',
        'desempeno',
        'fortalezas',
        'debilidades',
        'recomendaciones',
        'observaciones',
        'faltas_periodo',
        'aprobada',
        'requiere_nivelacion',
        'fecha_registro',
    ];

    protected $casts = [
        'nota_periodo' => 'decimal:2',
        'nota_acumulada' => 'decimal:2',
        'nota_final' => 'decimal:2',
        'faltas_periodo' => 'integer',
        'aprobada' => 'boolean',
        'requiere_nivelacion' => 'boolean',
        'fecha_registro' => 'date',
    ];

    /**
     * Matrícula del estudiante
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
     * Asignatura calificada
     */
    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class);
    }

    /**
     * Período académico
     */
    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class);
    }

    /**
     * Docente que registra la calificación
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    /**
     * Scope para calificaciones aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }

    /**
     * Scope para calificaciones reprobadas
     */
    public function scopeReprobadas($query)
    {
        return $query->where('aprobada', false);
    }

    /**
     * Scope para calificaciones por matrícula
     */
    public function scopePorMatricula($query, $matriculaId)
    {
        return $query->where('matricula_id', $matriculaId);
    }

    /**
     * Scope para calificaciones por asignatura
     */
    public function scopePorAsignatura($query, $asignaturaId)
    {
        return $query->where('asignatura_id', $asignaturaId);
    }

    /**
     * Scope para calificaciones por período
     */
    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_academico_id', $periodoId);
    }

    /**
     * Scope para calificaciones por desempeño
     */
    public function scopePorDesempeno($query, $desempeno)
    {
        return $query->where('desempeno', $desempeno);
    }

    /**
     * Scope para calificaciones que requieren nivelación
     */
    public function scopeRequierenNivelacion($query)
    {
        return $query->where('requiere_nivelacion', true);
    }

    /**
     * Determinar si la nota es aprobatoria (>= 3.0)
     */
    public function getEsAprobatoriaAttribute(): bool
    {
        return $this->nota_periodo >= 3.0;
    }

    /**
     * Obtener color según desempeño
     */
    public function getColorDesempenoAttribute(): string
    {
        return match($this->desempeno) {
            'superior' => 'success',
            'alto' => 'info',
            'basico' => 'warning',
            'bajo' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Obtener descripción del desempeño
     */
    public function getDescripcionDesempenoAttribute(): string
    {
        return match($this->desempeno) {
            'superior' => 'Desempeño Superior (4.6 - 5.0)',
            'alto' => 'Desempeño Alto (4.0 - 4.5)',
            'basico' => 'Desempeño Básico (3.0 - 3.9)',
            'bajo' => 'Desempeño Bajo (1.0 - 2.9)',
            default => 'Sin calificar',
        };
    }
}
