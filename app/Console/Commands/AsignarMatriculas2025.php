<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\PeriodoAcademico;
use App\Models\Sede;
use Illuminate\Console\Command;

class AsignarMatriculas2025 extends Command
{
    protected $signature = 'estudiantes:asignar-matriculas-2025 {file}';
    protected $description = 'Asignar matrículas desde CSV 2025';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("El archivo {$file} no existe.");
            return 1;
        }

        $this->info("Asignando matrículas desde: {$file}");

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle, 0, ';');

        $asignadas = 0;
        $omitidas = 0;

        $periodo = PeriodoAcademico::where('anio', 2025)->where('activo', true)->first();
        $sede = Sede::first();

        if (!$periodo || !$sede) {
            $this->error('No se encontró período o sede');
            return 1;
        }

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $data = array_combine($headers, $row);

            $doc = trim($data['NRODOCUMENTO'] ?? '');
            $mat = trim($data['MATRICULA'] ?? '');
            $codGrado = trim($data['GRADO'] ?? '');
            $codGrupo = trim($data['GRUPO'] ?? '');

            if (empty($doc) || empty($codGrado) || empty($codGrupo) || empty($mat)) {
                $omitidas++;
                continue;
            }

            $estudiante = Estudiante::where('numero_documento', $doc)->first();
            if (!$estudiante) {
                $omitidas++;
                continue;
            }

            $grado = Grado::where('codigo', $codGrado)->first();
            if (!$grado) {
                $omitidas++;
                continue;
            }

            // Convertir código de grupo del CSV al formato real
            // CSV: 010100 -> Grado 01, Grupo 01 -> Nombre real: 101
            // CSV: 010200 -> Grado 01, Grupo 02 -> Nombre real: 102
            $nombreGrupo = $this->convertirCodigoGrupo($codGrado, $codGrupo);

            $grupo = Grupo::where('grado_id', $grado->id)
                ->where('sede_id', $sede->id)
                ->where('anio', 2025)
                ->where('nombre', $nombreGrupo)
                ->first();

            if (!$grupo) {
                $omitidas++;
                continue;
            }

            Matricula::updateOrCreate(
                [
                    'estudiante_id' => $estudiante->id,
                    'grupo_id' => $grupo->id,
                    'periodo_academico_id' => $periodo->id,
                ],
                [
                    'anio' => 2025,
                    'numero_matricula' => $mat,
                    'fecha_matricula' => now(),
                    'estado' => 'activa',
                    'tipo_matricula' => 'renovacion',
                    'jornada' => 'mañana',
                ]
            );

            $asignadas++;
            if ($asignadas % 100 == 0) {
                $this->info("Asignadas: {$asignadas}");
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

    protected function convertirCodigoGrupo($codGrado, $codGrupo)
    {
        // Convertir código de grupo del CSV al formato real
        // CSV usa formato: GGVVXX donde GG=grado, VV=variación, XX=secuencia
        // Ejemplos:
        // 010100 -> Grado 01 grupo 01 -> 101
        // 010200 -> Grado 01 grupo 02 -> 102
        // C30100 -> Grado C3 grupo 01 -> C301
        // TS0100 -> Grado TS grupo 01 -> TS01

        // Extraer el número de grupo (caract 3-4, quitando ceros a la izq)
        $numGrupo = ltrim(substr($codGrupo, 2, 2), '0');
        if (empty($numGrupo)) $numGrupo = '1';

        // Para grados numéricos (01-11), quitar el cero del grado también
        if (preg_match('/^\d+$/', $codGrado)) {
            $codGrado = ltrim($codGrado, '0');
        }

        // Formar el nombre del grupo
        return $codGrado . '0' . $numGrupo;
    }
}
