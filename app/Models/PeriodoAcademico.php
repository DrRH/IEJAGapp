<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodoAcademico extends Model
{
    use SoftDeletes;

    protected $table = 'periodos_academicos';

    protected $fillable = [
        'anio',
        'nombre',
        'numero',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'porcentaje',
        'observaciones',
    ];

    protected $casts = [
        'anio' => 'integer',
        'numero' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'porcentaje' => 'decimal:2',
    ];

    /**
     * Matrículas de este período
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Asignaciones de docentes en este período
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionDocenteAsignatura::class);
    }

    /**
     * Calificaciones de este período
     */
    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    /**
     * Scope para períodos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para períodos por año
     */
    public function scopePorAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    /**
     * Scope para ordenar por año y número
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('anio', 'desc')->orderBy('numero');
    }

    /**
     * Scope para el período activo actual
     */
    public function scopeActual($query)
    {
        return $query->where('activo', true)
                    ->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now())
                    ->first();
    }

    /**
     * Verificar si el período está en curso
     */
    public function getEnCursoAttribute(): bool
    {
        return $this->fecha_inicio <= now() && $this->fecha_fin >= now();
    }

    /**
     * Obtener nombre completo del período
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->anio}";
    }
}
