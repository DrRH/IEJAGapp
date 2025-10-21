<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estudiante extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'genero',
        'grupo_sanguineo',
        'rh',
        'lugar_nacimiento',
        'direccion',
        'barrio',
        'municipio',
        'telefono',
        'celular',
        'email',
        'nombre_acudiente',
        'telefono_acudiente',
        'email_acudiente',
        'parentesco_acudiente',
        'nombre_madre',
        'telefono_madre',
        'nombre_padre',
        'telefono_padre',
        'codigo_estudiante',
        'sede_id',
        'estrato',
        'eps',
        'estado',
        'fecha_ingreso',
        'fecha_retiro',
        'motivo_retiro',
        'observaciones_medicas',
        'observaciones_generales',
        'tiene_discapacidad',
        'tipo_discapacidad',
        'adaptaciones_curriculares',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_retiro' => 'date',
        'tiene_discapacidad' => 'boolean',
    ];

    /**
     * Sede del estudiante
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Matrículas del estudiante
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Matrícula activa actual del estudiante
     */
    public function matriculaActual()
    {
        return $this->hasOne(Matricula::class)
            ->where('estado', 'activa')
            ->where('periodo_academico_id', function($query) {
                $query->select('id')
                    ->from('periodos_academicos')
                    ->where('activo', true)
                    ->where('anio', date('Y'))
                    ->orderBy('numero', 'desc')
                    ->limit(1);
            })
            ->with('grupo.grado');
    }

    /**
     * Reportes de convivencia del estudiante
     */
    public function reportesConvivencia(): HasMany
    {
        return $this->hasMany(ReporteConvivencia::class);
    }

    /**
     * Nombre completo del estudiante
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellidos} {$this->nombres}";
    }

    /**
     * Edad del estudiante
     */
    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento?->age ?? 0;
    }

    /**
     * Scope para estudiantes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para estudiantes por sede
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para buscar por nombre o documento
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombres', 'like', "%{$termino}%")
              ->orWhere('apellidos', 'like', "%{$termino}%")
              ->orWhere('numero_documento', 'like', "%{$termino}%")
              ->orWhere('codigo_estudiante', 'like', "%{$termino}%");
        });
    }
}
