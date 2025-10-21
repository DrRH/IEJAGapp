<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\PeriodoAcademico;
use App\Models\Sede;
use Illuminate\Console\Command;

class AsignarMatriculas extends Command
{
    protected $signature = 'estudiantes:asignar-matriculas {file}';
    protected $description = 'Asignar matrículas a estudiantes desde CSV';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("El archivo {$file} no existe.");
            return 1;
        }

        $this->info("Asignando matrículas desde: {$file}");

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);

        $this->info("Encabezados: " . implode(', ', $headers));

        $asignadas = 0;
        $omitidas = 0;
        $rowNumber = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $data = array_combine($headers, $row);

            $documento = trim($data['DOCUMENTO'] ?? '');
            $codigoGrado = trim($data['GRADO'] ?? '');
            $codigoGrupo = trim($data['GRUPO'] ?? '');

            if (empty($documento) || empty($codigoGrado) || empty($codigoGrupo)) {
                $omitidas++;
                continue;
            }

            try {
                $estudiante = Estudiante::where('numero_documento', $documento)->first();
                if (!$estudiante) {
                    $omitidas++;
                    continue;
                }

                if ($this->crearMatricula($estudiante, $codigoGrado, $codigoGrupo)) {
                    $asignadas++;
                    if ($asignadas % 50 == 0) {
                        $this->info("  Asignadas: {$asignadas} matrículas...");
                    }
                } else {
                    $omitidas++;
                }
            } catch (\Exception $e) {
                $omitidas++;
                if ($omitidas <= 5) {
                    $this->warn("Error fila {$rowNumber}: " . $e->getMessage());
                }
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info("======================");
        $this->info("Asignación completada");
        $this->info("======================");
        $this->info("Asignadas: {$asignadas}");
        $this->warn("Omitidas: {$omitidas}");

        return 0;
    }

    protected function crearMatricula($estudiante, $codigoGrado, $codigoGrupo)
    {
        // Buscar el grado por código
        $grado = Grado::where('codigo', $codigoGrado)->first();
        if (!$grado) {
            $this->line("  x No se encontró grado: {$codigoGrado}");
            return false;
        }

        // Obtener sede
        $sede = $estudiante->sede ?? Sede::first();
        if (!$sede) {
            return false;
        }

        // Determinar el nombre del grupo desde el código
        // Ejemplo: 010100 -> grado 01, grupo 00 -> incrementar 1 -> A
        // 010200 -> grado 01, grupo 00 pero segunda ocurrencia -> B
        // Simplemente contar cuántos grupos del mismo grado ya existen
        $gruposExistentes = Grupo::where('grado_id', $grado->id)
            ->where('sede_id', $sede->id)
            ->where('anio', 2025)
            ->orderBy('nombre')
            ->get();

        // Mapear código de grupo a letra basándose en el patrón
        // 010100, 010200, 010300 -> A, B, C
        $grupoMap = [];
        foreach ($gruposExistentes as $index => $g) {
            $grupoMap[$index] = $g->nombre;
        }

        // Extraer el número de grupo del código (último dígito antes de 00)
        // 010100 -> 1, 010200 -> 2, 010300 -> 3
        $grupoIndex = (int)substr($codigoGrupo, 3, 1) - 1;
        $grupoLetra = $grupoMap[$grupoIndex] ?? null;

        if (!$grupoLetra) {
            $this->line("  x Código de grupo no mapeado: {$codigoGrupo}");
            return false;
        }

        // Buscar el grupo
        $grupo = Grupo::where('grado_id', $grado->id)
            ->where('sede_id', $sede->id)
            ->where('anio', 2025)
            ->where('nombre', $grupoLetra)
            ->first();

        if (!$grupo) {
            $this->line("  x No se encontró grupo {$grupoLetra} para grado {$codigoGrado}");
            return false;
        }

        // Obtener período académico activo
        $periodo = PeriodoAcademico::where('anio', 2025)->where('activo', true)->first();
        if (!$periodo) {
            return false;
        }

        // Crear la matrícula
        $matricula = Matricula::firstOrCreate(
            [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
                'periodo_academico_id' => $periodo->id,
            ],
            [
                'anio' => 2025,
                'fecha_matricula' => now(),
                'estado' => 'activa',
                'tipo_matricula' => 'renovacion',
                'jornada' => 'mañana',
                'numero_matricula' => 'MAT-2025-' . str_pad($estudiante->id, 6, '0', STR_PAD_LEFT),
            ]
        );

        return true;
    }
}
