<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalAdministrativo extends Model
{
    use SoftDeletes;

    protected $table = 'personal_administrativo';

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
        'codigo_empleado',
        'sede_id',
        'cargo',
        'dependencia',
        'tipo_vinculacion',
        'fecha_ingreso',
        'fecha_retiro',
        'nivel_estudio',
        'titulo',
        'funciones',
        'horario',
        'permisos_especiales',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_retiro' => 'date',
        'permisos_especiales' => 'array',
    ];

    /**
     * Usuario asociado
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sede del personal administrativo
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Nombre completo del personal administrativo
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellidos} {$this->nombres}";
    }

    /**
     * Edad del personal administrativo
     */
    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento?->age ?? 0;
    }

    /**
     * Scope para personal activo
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para personal por sede
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para personal por cargo
     */
    public function scopePorCargo($query, $cargo)
    {
        return $query->where('cargo', $cargo);
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
              ->orWhere('codigo_empleado', 'like', "%{$termino}%");
        });
    }
}
