<?php

namespace App\Console\Commands;

use App\Models\Grado;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\Sede;
use Illuminate\Console\Command;

class SetupAcademico2025 extends Command
{
    protected $signature = 'academico:setup-2025';
    protected $description = 'Configurar estructura académica 2025 con grados y grupos correctos';

    public function handle()
    {
        $this->info('Configurando estructura académica 2025...');

        // Crear período académico
        $periodo = PeriodoAcademico::firstOrCreate(
            ['anio' => 2025, 'numero' => 1],
            [
                'nombre' => 'Primer Período 2025',
                'fecha_inicio' => '2025-01-20',
                'fecha_fin' => '2025-03-31',
                'activo' => true,
                'porcentaje' => 25.00,
            ]
        );
        $this->info("✓ Período académico: {$periodo->nombre}");

        // Obtener o crear sede
        $sede = Sede::first();
        if (!$sede) {
            $sede = Sede::create([
                'codigo' => 'PRINCIPAL',
                'nombre' => 'Sede Principal',
                'direccion' => 'Pendiente',
                'telefono' => 'Pendiente',
                'estado' => 'activo',
            ]);
        }

        // Definir grados con sus grupos
        $estructura = [
            'TS' => [
                'nombre' => 'Transición',
                'nivel' => 'preescolar',
                'orden' => 1,
                'grupos' => ['TS01', 'TS02']
            ],
            '01' => [
                'nombre' => 'Primero',
                'nivel' => 'primaria',
                'orden' => 10,
                'grupos' => ['101', '102']
            ],
            '02' => [
                'nombre' => 'Segundo',
                'nivel' => 'primaria',
                'orden' => 11,
                'grupos' => ['201', '202']
            ],
            '03' => [
                'nombre' => 'Tercero',
                'nivel' => 'primaria',
                'orden' => 12,
                'grupos' => ['301', '302', '303']
            ],
            '04' => [
                'nombre' => 'Cuarto',
                'nivel' => 'primaria',
                'orden' => 13,
                'grupos' => ['401', '402', '403']
            ],
            '05' => [
                'nombre' => 'Quinto',
                'nivel' => 'primaria',
                'orden' => 14,
                'grupos' => ['501', '502']
            ],
            '06' => [
                'nombre' => 'Sexto',
                'nivel' => 'secundaria',
                'orden' => 20,
                'grupos' => ['601', '602', '603']
            ],
            '07' => [
                'nombre' => 'Séptimo',
                'nivel' => 'secundaria',
                'orden' => 21,
                'grupos' => ['701', '702']
            ],
            '08' => [
                'nombre' => 'Octavo',
                'nivel' => 'secundaria',
                'orden' => 22,
                'grupos' => ['801', '802']
            ],
            '09' => [
                'nombre' => 'Noveno',
                'nivel' => 'secundaria',
                'orden' => 23,
                'grupos' => ['901', '902']
            ],
            '10' => [
                'nombre' => 'Décimo',
                'nivel' => 'media',
                'orden' => 30,
                'grupos' => ['1001', '1002', '1003']
            ],
            '11' => [
                'nombre' => 'Once',
                'nivel' => 'media',
                'orden' => 31,
                'grupos' => ['1101', '1102']
            ],
            'AC' => [
                'nombre' => 'Aceleración',
                'nivel' => 'secundaria',
                'orden' => 40,
                'grupos' => ['AC01']
            ],
            'C3' => [
                'nombre' => 'CLEI 3',
                'nivel' => 'secundaria',
                'orden' => 41,
                'grupos' => ['C301']
            ],
            'C4' => [
                'nombre' => 'CLEI 4',
                'nivel' => 'secundaria',
                'orden' => 42,
                'grupos' => ['C401', 'C402']
            ],
            'C5' => [
                'nombre' => 'CLEI 5',
                'nivel' => 'media',
                'orden' => 43,
                'grupos' => ['C501', 'C502']
            ],
            'C6' => [
                'nombre' => 'CLEI 6',
                'nivel' => 'media',
                'orden' => 44,
                'grupos' => ['C601-1', 'C601-2', 'C602-1', 'C602-2']
            ],
            'P2' => [
                'nombre' => 'Pensar',
                'nivel' => 'preescolar',
                'orden' => 2,
                'grupos' => ['P201']
            ],
            'PB' => [
                'nombre' => 'Brújula',
                'nivel' => 'preescolar',
                'orden' => 3,
                'grupos' => ['PB01']
            ],
        ];

        $totalGrados = 0;
        $totalGrupos = 0;

        foreach ($estructura as $codigo => $config) {
            // Crear grado
            $grado = Grado::create([
                'codigo' => $codigo,
                'nombre' => $config['nombre'],
                'nivel' => $config['nivel'],
                'orden' => $config['orden'],
            ]);
            $totalGrados++;
            $this->line("  - Grado {$codigo}: {$grado->nombre}");

            // Crear grupos
            foreach ($config['grupos'] as $codigoGrupo) {
                Grupo::create([
                    'grado_id' => $grado->id,
                    'sede_id' => $sede->id,
                    'anio' => 2025,
                    'nombre' => $codigoGrupo,
                    'jornada' => 'mañana',
                    'capacidad_maxima' => 40,
                    'activo' => true,
                ]);
                $totalGrupos++;
            }
        }

        $this->info("✓ {$totalGrados} grados creados");
        $this->info("✓ {$totalGrupos} grupos creados");
        $this->newLine();
        $this->info('======================');
        $this->info('Configuración completada');
        $this->info('======================');

        return 0;
    }
}
