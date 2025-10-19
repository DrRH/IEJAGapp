<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoAnotacion extends Model
{
    use SoftDeletes;

    protected $table = 'tipos_anotacion';

    protected $fillable = [
        'nombre',
        'codigo',
        'categoria',
        'tipo',
        'descripcion',
        'color',
        'puntos',
        'notificar_acudiente',
        'requiere_compromiso',
        'requiere_suspension',
        'dias_suspension',
        'activa',
        'orden',
    ];

    protected $casts = [
        'puntos' => 'integer',
        'notificar_acudiente' => 'boolean',
        'requiere_compromiso' => 'boolean',
        'requiere_suspension' => 'boolean',
        'dias_suspension' => 'integer',
        'activa' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Reportes de convivencia con este tipo de anotación
     */
    public function reportesConvivencia(): HasMany
    {
        return $this->hasMany(ReporteConvivencia::class);
    }

    /**
     * Scope para tipos de anotación activos
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para tipos de anotación por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para tipos de anotación por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para anotaciones positivas
     */
    public function scopePositivas($query)
    {
        return $query->where('categoria', 'positiva');
    }

    /**
     * Scope para anotaciones negativas (leve, grave, muy grave)
     */
    public function scopeNegativas($query)
    {
        return $query->whereIn('categoria', ['leve', 'grave', 'muy_grave']);
    }

    /**
     * Scope para anotaciones de convivencia
     */
    public function scopeConvivencia($query)
    {
        return $query->where('tipo', 'convivencia');
    }

    /**
     * Scope para anotaciones académicas
     */
    public function scopeAcademicas($query)
    {
        return $query->where('tipo', 'academica');
    }

    /**
     * Scope para anotaciones disciplinarias
     */
    public function scopeDisciplinarias($query)
    {
        return $query->where('tipo', 'disciplinaria');
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    /**
     * Scope para anotaciones que requieren notificación
     */
    public function scopeQueNotifican($query)
    {
        return $query->where('notificar_acudiente', true);
    }

    /**
     * Scope para anotaciones que requieren suspensión
     */
    public function scopeConSuspension($query)
    {
        return $query->where('requiere_suspension', true);
    }

    /**
     * Determinar si es una anotación positiva
     */
    public function getEsPositivaAttribute(): bool
    {
        return $this->categoria === 'positiva';
    }

    /**
     * Determinar si es una anotación negativa
     */
    public function getEsNegativaAttribute(): bool
    {
        return in_array($this->categoria, ['leve', 'grave', 'muy_grave']);
    }

    /**
     * Obtener nivel de gravedad (0-4)
     */
    public function getNivelGravedadAttribute(): int
    {
        return match($this->categoria) {
            'positiva' => 0,
            'informativa' => 0,
            'leve' => 1,
            'grave' => 2,
            'muy_grave' => 3,
            default => 0,
        };
    }

    /**
     * Obtener descripción de la categoría
     */
    public function getDescripcionCategoriaAttribute(): string
    {
        return match($this->categoria) {
            'positiva' => 'Anotación Positiva',
            'informativa' => 'Anotación Informativa',
            'leve' => 'Falta Leve',
            'grave' => 'Falta Grave',
            'muy_grave' => 'Falta Muy Grave',
            default => 'Sin categoría',
        };
    }

    /**
     * Obtener icono según categoría
     */
    public function getIconoAttribute(): string
    {
        return match($this->categoria) {
            'positiva' => 'ti ti-award',
            'informativa' => 'ti ti-info-circle',
            'leve' => 'ti ti-alert-circle',
            'grave' => 'ti ti-alert-triangle',
            'muy_grave' => 'ti ti-alert-octagon',
            default => 'ti ti-file-text',
        };
    }
}
