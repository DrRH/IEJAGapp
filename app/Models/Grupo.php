<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'grado_id',
        'sede_id',
        'anio',
        'nombre',
        'director_grupo_id',
        'capacidad_maxima',
        'salon',
        'jornada',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'anio' => 'integer',
        'capacidad_maxima' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Grado del grupo
     */
    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Sede del grupo
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Director de grupo (usuario)
     */
    public function directorGrupo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_grupo_id');
    }

    /**
     * Matrículas del grupo
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Asignaciones de docentes al grupo
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionDocenteAsignatura::class);
    }

    /**
     * Estudiantes del grupo a través de matrículas
     */
    public function estudiantes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Estudiante::class,
            Matricula::class,
            'grupo_id',
            'id',
            'id',
            'estudiante_id'
        );
    }

    /**
     * Scope para grupos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para grupos por año
     */
    public function scopePorAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    /**
     * Scope para grupos por sede
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para grupos por grado
     */
    public function scopePorGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    /**
     * Scope para grupos por jornada
     */
    public function scopePorJornada($query, $jornada)
    {
        return $query->where('jornada', $jornada);
    }

    /**
     * Obtener nombre completo del grupo
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->grado->nombre} {$this->nombre}";
    }

    /**
     * Verificar si el grupo está lleno
     */
    public function getLlenoAttribute(): bool
    {
        return $this->matriculas()->where('estado', 'activa')->count() >= $this->capacidad_maxima;
    }

    /**
     * Obtener cantidad de estudiantes matriculados
     */
    public function getCantidadEstudiantesAttribute(): int
    {
        return $this->matriculas()->where('estado', 'activa')->count();
    }

    /**
     * Obtener cupos disponibles
     */
    public function getCuposDisponiblesAttribute(): int
    {
        return max(0, $this->capacidad_maxima - $this->cantidad_estudiantes);
    }
}
