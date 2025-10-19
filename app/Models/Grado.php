<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grado extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'codigo',
        'nivel',
        'orden',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Grupos de este grado
     */
    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }

    /**
     * Scope para grados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para ordenar por nivel
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    /**
     * Scope por nivel educativo
     */
    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }
}
