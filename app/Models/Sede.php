<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sede extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'telefono',
        'email',
        'ciudad',
        'es_principal',
        'activa',
        'observaciones',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'activa' => 'boolean',
    ];

    /**
     * Estudiantes de esta sede
     */
    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class);
    }

    /**
     * Docentes de esta sede
     */
    public function docentes(): HasMany
    {
        return $this->hasMany(Docente::class);
    }

    /**
     * Personal administrativo de esta sede
     */
    public function personalAdministrativo(): HasMany
    {
        return $this->hasMany(PersonalAdministrativo::class);
    }

    /**
     * Grupos de esta sede
     */
    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }

    /**
     * Scope para sedes activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para la sede principal
     */
    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }
}
