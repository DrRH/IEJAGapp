<?php

namespace App\Console\Commands;

use App\Models\Grado;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\Sede;
use Illuminate\Console\Command;

class SetupAcademico extends Command
{
    protected $signature = 'academico:setup';
    protected $description = 'Configurar estructura académica (grados, grupos, período)';

    public function handle()
    {
        $this->info('Configurando estructura académica...');

        // Crear período académico actual (año completo)
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

        // Obtener sede por defecto
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

        // Definir grados
        $gradosConfig = [
            '01' => ['nombre' => 'Primero', 'nivel' => 'primaria'],
            '02' => ['nombre' => 'Segundo', 'nivel' => 'primaria'],
            '03' => ['nombre' => 'Tercero', 'nivel' => 'primaria'],
            '04' => ['nombre' => 'Cuarto', 'nivel' => 'primaria'],
            '05' => ['nombre' => 'Quinto', 'nivel' => 'primaria'],
            '06' => ['nombre' => 'Sexto', 'nivel' => 'secundaria'],
            '07' => ['nombre' => 'Séptimo', 'nivel' => 'secundaria'],
            '08' => ['nombre' => 'Octavo', 'nivel' => 'secundaria'],
            '09' => ['nombre' => 'Noveno', 'nivel' => 'secundaria'],
            '10' => ['nombre' => 'Décimo', 'nivel' => 'media'],
            '11' => ['nombre' => 'Once', 'nivel' => 'media'],
            'AC' => ['nombre' => 'Aceleración', 'nivel' => 'secundaria'],
            'C3' => ['nombre' => 'CLEI 3', 'nivel' => 'secundaria'],
            'C4' => ['nombre' => 'CLEI 4', 'nivel' => 'secundaria'],
            'C5' => ['nombre' => 'CLEI 5', 'nivel' => 'media'],
            'C6' => ['nombre' => 'CLEI 6', 'nivel' => 'media'],
            'P2' => ['nombre' => 'Preescolar 2', 'nivel' => 'preescolar'],
            'PB' => ['nombre' => 'Preescolar B', 'nivel' => 'preescolar'],
            'TS' => ['nombre' => 'Transición', 'nivel' => 'preescolar'],
        ];

        $grados = [];
        foreach ($gradosConfig as $codigo => $config) {
            $grado = Grado::firstOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => $config['nombre'],
                    'nivel' => $config['nivel'],
                    'orden' => $this->getOrden($codigo),
                ]
            );
            $grados[$codigo] = $grado;
            $this->line("  - Grado {$codigo}: {$grado->nombre}");
        }

        $this->info("✓ " . count($grados) . " grados creados");

        // Crear grupos por grado
        $gruposData = [
            '01' => ['010100', '010200'],
            '02' => ['020100', '020200'],
            '03' => ['030100', '030200', '030300'],
            '04' => ['040100', '040200', '040300'],
            '05' => ['050100', '050200'],
            '06' => ['060100', '060200', '060300'],
            '07' => ['070100', '070200'],
            '08' => ['080100', '080200'],
            '09' => ['090100', '090200'],
            '10' => ['100100', '100200', '100300'],
            '11' => ['110100', '110200'],
            'AC' => ['AC0100'],
            'C3' => ['C30100'],
            'C4' => ['C40100', 'C40200'],
            'C5' => ['C5010001', 'C5020001'],
            'C6' => ['C6010002', 'C6020002'],
            'P2' => ['P20100'],
            'PB' => ['PB0100'],
            'TS' => ['TS0100', 'TS0200'],
        ];

        $totalGrupos = 0;
        foreach ($gruposData as $codigoGrado => $codigosGrupos) {
            $grado = $grados[$codigoGrado];
            foreach ($codigosGrupos as $index => $codigoGrupo) {
                $letra = chr(65 + $index); // A, B, C...
                Grupo::firstOrCreate(
                    [
                        'grado_id' => $grado->id,
                        'sede_id' => $sede->id,
                        'anio' => 2025,
                        'nombre' => $letra,
                    ],
                    [
                        'jornada' => 'mañana',
                        'capacidad_maxima' => 40,
                        'activo' => true,
                    ]
                );
                $totalGrupos++;
            }
        }

        $this->info("✓ {$totalGrupos} grupos creados");
        $this->newLine();
        $this->info('======================');
        $this->info('Configuración completada');
        $this->info('======================');

        return 0;
    }

    private function getOrden($codigo)
    {
        $orden = [
            'PB' => 1, 'P2' => 2, 'TS' => 3,
            '01' => 10, '02' => 11, '03' => 12, '04' => 13, '05' => 14,
            '06' => 20, '07' => 21, '08' => 22, '09' => 23,
            '10' => 30, '11' => 31,
            'AC' => 40, 'C3' => 41, 'C4' => 42, 'C5' => 43, 'C6' => 44,
        ];
        return $orden[$codigo] ?? 99;
    }
}
