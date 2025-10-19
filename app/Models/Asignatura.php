<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asignatura extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'codigo',
        'area',
        'descripcion',
        'intensidad_horaria',
        'es_fundamental',
        'es_optativa',
        'aprueba_estudiante',
        'orden',
        'activa',
        'observaciones',
    ];

    protected $casts = [
        'intensidad_horaria' => 'integer',
        'es_fundamental' => 'boolean',
        'es_optativa' => 'boolean',
        'aprueba_estudiante' => 'boolean',
        'orden' => 'integer',
        'activa' => 'boolean',
    ];

    /**
     * Asignaciones de docentes a esta asignatura
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionDocenteAsignatura::class);
    }

    /**
     * Calificaciones de esta asignatura
     */
    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    /**
     * Asistencias de esta asignatura
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Scope para asignaturas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para asignaturas por área
     */
    public function scopePorArea($query, $area)
    {
        return $query->where('area', $area);
    }

    /**
     * Scope para asignaturas fundamentales
     */
    public function scopeFundamentales($query)
    {
        return $query->where('es_fundamental', true);
    }

    /**
     * Scope para asignaturas optativas
     */
    public function scopeOptativas($query)
    {
        return $query->where('es_optativa', true);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    /**
     * Scope para asignaturas que aprueban estudiante
     */
    public function scopeQueAprueban($query)
    {
        return $query->where('aprueba_estudiante', true);
    }
}
