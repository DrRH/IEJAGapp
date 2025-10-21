<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\PeriodoAcademico;
use App\Models\Sede;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ImportEstudiantes2025 extends Command
{
    protected $signature = 'estudiantes:import-2025 {file}';
    protected $description = 'Importar estudiantes desde CSV 2025';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("El archivo {$file} no existe.");
            return 1;
        }

        $this->info("Importando estudiantes desde: {$file}");

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle, 0, ';'); // Delimitador es ;

        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 10) continue; // Saltar filas vacías

            $data = array_combine($headers, $row);

            try {
                $this->importRow($data);
                $imported++;

                if ($imported % 50 == 0) {
                    $this->info("Procesados: {$imported} estudiantes...");
                }
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'matricula' => $data['MATRICULA'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];

                if ($skipped <= 5) {
                    $this->warn("Error: " . $e->getMessage());
                }
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info("======================");
        $this->info("Importación completada");
        $this->info("======================");
        $this->info("Importados: {$imported}");
        $this->warn("Omitidos: {$skipped}");

        if (!empty($errors) && $skipped > 5) {
            $this->newLine();
            $this->warn("Primeros 10 errores:");
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->line("  - Mat {$error['matricula']}: {$error['error']}");
            }
        }

        return 0;
    }

    protected function importRow(array $data)
    {
        // Limpiar y validar datos básicos
        $numeroDocumento = trim($data['NRODOCUMENTO'] ?? '');
        $matricula = trim($data['MATRICULA'] ?? '');
        $nombres = trim($data['NOMBRES'] ?? '');
        $apellidos = trim($data['APELLIDOS'] ?? '');

        if (empty($numeroDocumento) || empty($nombres) || empty($apellidos)) {
            throw new \Exception('Datos incompletos');
        }

        // Parsear fecha de nacimiento
        $fechaNacimiento = null;
        if (!empty($data['FNACIMIENTO'])) {
            try {
                $fechaNacimiento = \Carbon\Carbon::createFromFormat('d/m/Y', trim($data['FNACIMIENTO']))->format('Y-m-d');
            } catch (\Exception $e) {
                // Formato alternativo
                $fechaNacimiento = null;
            }
        }

        // Mapear tipo de documento
        $tipoDoc = 'TI';
        if (isset($data['TIPODOCUMENTO'])) {
            $tipo = strtoupper(trim($data['TIPODOCUMENTO']));
            if (strpos($tipo, 'R.C') !== false) $tipoDoc = 'RC';
            elseif (strpos($tipo, 'T.I') !== false) $tipoDoc = 'TI';
            elseif (strpos($tipo, 'C.C') !== false) $tipoDoc = 'CC';
            elseif (strpos($tipo, 'C.E') !== false) $tipoDoc = 'CE';
            elseif (strpos($tipo, 'P.P.T') !== false || strpos($tipo, 'NUIP') !== false) $tipoDoc = 'NUIP';
        }

        // Crear o actualizar estudiante
        $estudiante = Estudiante::updateOrCreate(
            ['numero_documento' => $numeroDocumento],
            [
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'tipo_documento' => $tipoDoc,
                'fecha_nacimiento' => $fechaNacimiento,
                'genero' => 'M',
                'grupo_sanguineo' => trim($data['TIPOSANGRE'] ?? ''),
                'lugar_nacimiento' => substr(trim($data['MPIONACIMIENTO'] ?? ''), 0, 100),
                'direccion' => trim($data['DIRECCIONALUMNO'] ?? ''),
                'barrio' => trim($data['BARRIO'] ?? ''),
                'municipio' => trim($data['MPIORESIDENCIA'] ?? 'Medellín'),
                'telefono' => trim($data['TELMOVIL'] ?? ''),
                'email' => trim($data['EMAIL'] ?? ''),
                'eps' => trim($data['SEGUROMEDICO'] ?? ''),
                'nombre_madre' => trim($data['NOMBREMADRE'] ?? ''),
                'telefono_madre' => trim($data['TELMOVILMADRE'] ?? ''),
                'nombre_padre' => trim($data['NOMBREPADRE'] ?? ''),
                'telefono_padre' => trim($data['TELMOVILPADRE'] ?? ''),
                'nombre_acudiente' => trim($data['NOMBREACUDIENTE'] ?? $data['NOMBREMADRE'] ?? 'Por definir'),
                'telefono_acudiente' => trim($data['TELMOVILACUDIENTE'] ?? $data['TELMOVILMADRE'] ?? '0000000'),
                'parentesco_acudiente' => trim($data['AFINIDAD'] ?? ''),
                'email_acudiente' => trim($data['EMAILACUDIENTE'] ?? ''),
                'codigo_estudiante' => $matricula, // Usar matrícula como código
                'estrato' => trim($data['ESTRATO'] ?? '2'),
                'estado' => 'activo',
                'fecha_ingreso' => now()->format('Y-m-d'),
            ]
        );

        // Crear matrícula
        $codigoGrado = trim($data['GRADO'] ?? '');
        $codigoGrupo = trim($data['GRUPO'] ?? '');

        if (!empty($codigoGrado) && !empty($codigoGrupo)) {
            $this->crearMatricula($estudiante, $codigoGrado, $codigoGrupo, $matricula);
        }

        $this->line("  ✓ {$estudiante->nombre_completo} - Mat: {$matricula}");
    }

    protected function crearMatricula($estudiante, $codigoGrado, $codigoGrupo, $numeroMatricula)
    {
        // Buscar grado
        $grado = Grado::where('codigo', $codigoGrado)->first();
        if (!$grado) {
            return;
        }

        // Buscar grupo por nombre exacto
        $sede = Sede::first();
        $grupo = Grupo::where('grado_id', $grado->id)
            ->where('sede_id', $sede->id)
            ->where('anio', 2025)
            ->where('nombre', $codigoGrupo)
            ->first();

        if (!$grupo) {
            return;
        }

        // Obtener período académico
        $periodo = PeriodoAcademico::where('anio', 2025)->where('activo', true)->first();
        if (!$periodo) {
            return;
        }

        // Crear matrícula
        Matricula::updateOrCreate(
            [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
                'periodo_academico_id' => $periodo->id,
            ],
            [
                'anio' => 2025,
                'numero_matricula' => $numeroMatricula,
                'fecha_matricula' => now(),
                'estado' => 'activa',
                'tipo_matricula' => 'renovacion',
                'jornada' => 'mañana',
            ]
        );
    }
}
