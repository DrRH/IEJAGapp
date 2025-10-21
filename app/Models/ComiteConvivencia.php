<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComiteConvivencia extends Model
{
    use SoftDeletes;

    protected $table = 'comites_convivencia';

    protected $fillable = [
        'numero_acta',
        'fecha_reunion',
        'hora_inicio',
        'hora_fin',
        'lugar',
        'resumen_ejecutivo',
        'asistentes',
        'invitados',
        'orden_dia',
        'desarrollo',
        'casos_revisados',
        'decisiones',
        'compromisos',
        'seguimiento_compromisos_anteriores',
        'proxima_reunion',
        'temas_proxima_reunion',
        'observaciones',
        'estado',
        'archivos_adjuntos',
        'creado_por',
        'aprobado_por',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha_reunion' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'proxima_reunion' => 'date',
        'fecha_aprobacion' => 'datetime',
        'casos_revisados' => 'array',
        'archivos_adjuntos' => 'array',
    ];

    /**
     * Usuario que creó el acta
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Usuario que aprobó el acta
     */
    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    /**
     * Generar número de acta consecutivo
     */
    public static function generarNumeroActa(): string
    {
        $anio = date('Y');
        $ultimaActa = self::where('numero_acta', 'like', "CEC-{$anio}-%")
            ->orderByDesc('numero_acta')
            ->first();

        if ($ultimaActa) {
            // Extraer el número consecutivo
            preg_match('/CEC-\d{4}-(\d+)/', $ultimaActa->numero_acta, $matches);
            $consecutivo = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $consecutivo = 1;
        }

        return sprintf('CEC-%s-%03d', $anio, $consecutivo);
    }

    /**
     * Scope para actas borradores
     */
    public function scopeBorradores($query)
    {
        return $query->where('estado', 'borrador');
    }

    /**
     * Scope para actas aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Scope para actas publicadas
     */
    public function scopePublicadas($query)
    {
        return $query->where('estado', 'publicada');
    }

    /**
     * Scope para actas por año
     */
    public function scopePorAnio($query, $anio)
    {
        return $query->whereYear('fecha_reunion', $anio);
    }

    /**
     * Scope para actas por mes
     */
    public function scopePorMes($query, $mes, $anio = null)
    {
        $anio = $anio ?? date('Y');
        return $query->whereYear('fecha_reunion', $anio)
                    ->whereMonth('fecha_reunion', $mes);
    }

    /**
     * Obtener color del badge según estado
     */
    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'borrador' => 'secondary',
            'aprobada' => 'success',
            'publicada' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Obtener duración de la reunión en minutos
     */
    public function getDuracionReunionAttribute(): ?int
    {
        if (!$this->hora_inicio || !$this->hora_fin) {
            return null;
        }

        return $this->hora_inicio->diffInMinutes($this->hora_fin);
    }

    /**
     * Verificar si el acta está en borrador
     */
    public function getEsBorradorAttribute(): bool
    {
        return $this->estado === 'borrador';
    }

    /**
     * Verificar si el acta está aprobada
     */
    public function getEstaAprobadaAttribute(): bool
    {
        return $this->estado === 'aprobada';
    }

    /**
     * Verificar si el acta está publicada
     */
    public function getEstaPublicadaAttribute(): bool
    {
        return $this->estado === 'publicada';
    }

    /**
     * Obtener año del acta
     */
    public function getAnioAttribute(): ?int
    {
        return $this->fecha_reunion?->year;
    }

    /**
     * Obtener mes del acta
     */
    public function getMesAttribute(): ?int
    {
        return $this->fecha_reunion?->month;
    }

    /**
     * Obtener cantidad de casos revisados
     */
    public function getCantidadCasosAttribute(): int
    {
        return is_array($this->casos_revisados) ? count($this->casos_revisados) : 0;
    }

    /**
     * Obtener lista de asistentes como array
     */
    public function getListaAsistentesAttribute(): array
    {
        if (is_array($this->asistentes)) {
            return $this->asistentes;
        }

        // Si es texto, separar por líneas
        return array_filter(explode("\n", $this->asistentes));
    }

    /**
     * Obtener lista de invitados como array
     */
    public function getListaInvitadosAttribute(): array
    {
        if (empty($this->invitados)) {
            return [];
        }

        if (is_array($this->invitados)) {
            return $this->invitados;
        }

        return array_filter(explode("\n", $this->invitados));
    }
}
