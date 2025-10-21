<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\PeriodoAcademico;
use App\Models\Sede;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportEstudiantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estudiantes:import {file} {--dry-run : Ver qué se importaría sin guardar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar estudiantes desde un archivo CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $dryRun = $this->option('dry-run');

        if (!file_exists($file)) {
            $this->error("El archivo {$file} no existe.");
            return 1;
        }

        $this->info("Importando estudiantes desde: {$file}");
        if ($dryRun) {
            $this->warn("Modo DRY RUN - No se guardarán cambios");
        }

        $handle = fopen($file, 'r');

        // Leer encabezados
        $headers = fgetcsv($handle);
        $this->info("Encabezados detectados: " . implode(', ', $headers));

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $data = array_combine($headers, $row);

            try {
                $this->importRow($data, $dryRun, $rowNumber);
                $imported++;

                if ($imported % 10 == 0) {
                    $this->info("Procesados: {$imported} estudiantes...");
                }
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'documento' => $data['DOCUMENTO'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];

                if ($skipped <= 5) {
                    $this->warn("Error en fila {$rowNumber}: " . $e->getMessage());
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
                $this->line("  - Doc {$error['documento']}: {$error['error']}");
            }
        }

        return 0;
    }

    protected function importRow(array $data, bool $dryRun, int $rowNumber)
    {
        // Construir fecha de nacimiento desde DIA, MES, AÑO
        $fechaNacimiento = null;
        if (isset($data['DIA'], $data['MES'], $data['AÑO']) && $data['DIA'] && $data['MES'] && $data['AÑO']) {
            $fechaNacimiento = sprintf('%04d-%02d-%02d', $data['AÑO'], $data['MES'], $data['DIA']);
        }

        // Mapear tipo de documento
        $tipoDoc = 'TI';
        if (isset($data['TIPODOC'])) {
            $tipo = strtoupper(trim($data['TIPODOC']));
            if (strpos($tipo, 'R.C') !== false || strpos($tipo, 'RC') !== false) $tipoDoc = 'RC';
            elseif (strpos($tipo, 'T.I') !== false || strpos($tipo, 'TI') !== false) $tipoDoc = 'TI';
            elseif (strpos($tipo, 'C.C') !== false || strpos($tipo, 'CC') !== false) $tipoDoc = 'CC';
            elseif (strpos($tipo, 'C.E') !== false || strpos($tipo, 'CE') !== false) $tipoDoc = 'CE';
            elseif (strpos($tipo, 'P.P.T') !== false || strpos($tipo, 'NUIP') !== false) $tipoDoc = 'NUIP';
        }

        // Mapear los datos según las columnas del CSV
        $estudianteData = [
            'nombres' => trim($data['NOMBRES'] ?? ''),
            'apellidos' => trim($data['APELLIDOS'] ?? ''),
            'tipo_documento' => $tipoDoc,
            'numero_documento' => trim($data['DOCUMENTO'] ?? ''),
            'fecha_nacimiento' => $fechaNacimiento,
            'genero' => 'M', // Por defecto, ajustar si tienes columna de género
            'lugar_nacimiento' => $data['LUGARNACIMIENTO'] ?? null,
            'direccion' => $data['DIRECCION'] ?? null,
            'barrio' => $data['BARRIO'] ?? null,
            'municipio' => $data['DE'] ?? 'Medellín',
            'telefono' => $data['TELEFONO'] ?? null,
            'celular' => $data['CELESTUDIANTE'] ?? null,
            'eps' => $data['EPS'] ?? null,
            'nombre_madre' => $data['MADRE'] ?? null,
            'telefono_madre' => $data['CELMADRE'] ?? null,
            'nombre_padre' => $data['PADRE'] ?? null,
            'telefono_padre' => $data['CELPADRE'] ?? null,
            'nombre_acudiente' => $data['ACUDIENTE'] ?? $data['MADRE'] ?? 'Por definir',
            'telefono_acudiente' => $data['CELACUDIENTE'] ?? $data['CELMADRE'] ?? '0000000',
            'parentesco_acudiente' => $data['PARENTESCO'] ?? null,
            'codigo_estudiante' => str_pad($rowNumber, 6, '0', STR_PAD_LEFT),
            'estrato' => '2',
            'estado' => (isset($data['ESTADO']) && $data['ESTADO'] === 'OK') ? 'activo' : 'activo',
            'fecha_ingreso' => now()->format('Y-m-d'),
            'observaciones_generales' => $data['DIAGNOSTICO'] ?? $data['OBSERVACIONES'] ?? null,
        ];

        // Buscar sede si viene en los datos
        if (isset($data['sede']) || isset($data['Sede'])) {
            $sedeName = $data['sede'] ?? $data['Sede'];
            $sede = Sede::where('nombre', 'like', "%{$sedeName}%")->first();
            if ($sede) {
                $estudianteData['sede_id'] = $sede->id;
            }
        }

        // Validar datos mínimos
        $validator = Validator::make($estudianteData, [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'numero_documento' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date',
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        if ($dryRun) {
            $this->line("  [DRY RUN] Importaría: {$estudianteData['nombres']} {$estudianteData['apellidos']} - {$estudianteData['numero_documento']}");
            return;
        }

        // Buscar o crear estudiante
        $estudiante = Estudiante::updateOrCreate(
            ['numero_documento' => $estudianteData['numero_documento']],
            $estudianteData
        );

        // Crear matrícula si hay datos de GRADO y GRUPO
        if (!empty($data['GRADO']) && !empty($data['GRUPO'])) {
            $this->crearMatricula($estudiante, $data['GRADO'], $data['GRUPO']);
        }

        $this->line("  ✓ {$estudiante->nombre_completo}");
    }

    protected function crearMatricula($estudiante, $codigoGrado, $codigoGrupo)
    {
        try {
            // Buscar el grado por código
            $grado = Grado::where('codigo', $codigoGrado)->first();
            if (!$grado) {
                return; // No se puede crear matrícula sin grado
            }

            // Obtener sede (primera disponible si el estudiante no tiene)
            $sede = $estudiante->sede ?? Sede::first();
            if (!$sede) {
                return;
            }

            // Determinar el nombre del grupo desde el código
            // Ejemplo: 010100 -> grado 01, grupo A (primera ocurrencia)
            // 010200 -> grado 01, grupo B (segunda ocurrencia)
            // El tercer y cuarto dígito indican el grado, los últimos dos el grupo
            $grupoNumero = substr($codigoGrupo, 4, 2); // Ej: "01" de "010100"
            $grupoLetra = chr(64 + intval($grupoNumero)); // 01->A, 02->B, 03->C

            // Buscar el grupo
            $grupo = Grupo::where('grado_id', $grado->id)
                ->where('sede_id', $sede->id)
                ->where('anio', 2025)
                ->where('nombre', $grupoLetra)
                ->first();

            if (!$grupo) {
                return; // No se puede crear matrícula sin grupo
            }

            // Obtener período académico activo
            $periodo = PeriodoAcademico::where('anio', 2025)->where('activo', true)->first();
            if (!$periodo) {
                return;
            }

            // Crear la matrícula
            Matricula::firstOrCreate(
                [
                    'estudiante_id' => $estudiante->id,
                    'grupo_id' => $grupo->id,
                    'periodo_academico_id' => $periodo->id,
                ],
                [
                    'fecha_matricula' => now(),
                    'estado' => 'activa',
                    'numero_matricula' => 'MAT-2025-' . str_pad($estudiante->id, 6, '0', STR_PAD_LEFT),
                ]
            );
        } catch (\Exception $e) {
            // Silenciosamente fallar si no se puede crear la matrícula
            // El estudiante se importa de todos modos
        }
    }

    protected function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
