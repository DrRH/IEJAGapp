<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Docente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'municipio',
        'telefono',
        'celular',
        'email',
        'email_institucional',
        'codigo_docente',
        'sede_id',
        'tipo_vinculacion',
        'escalafon',
        'fecha_ingreso',
        'fecha_retiro',
        'nivel_estudio',
        'titulo_profesional',
        'universidad',
        'areas_conocimiento',
        'horas_semanales',
        'es_director_grupo',
        'es_coordinador',
        'es_jefe_area',
        'area_jefatura',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_retiro' => 'date',
        'areas_conocimiento' => 'array',
        'horas_semanales' => 'integer',
        'es_director_grupo' => 'boolean',
        'es_coordinador' => 'boolean',
        'es_jefe_area' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionDocenteAsignatura::class);
    }

    public function gruposDirigidos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'director_grupo_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellidos} {$this->nombres}";
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
