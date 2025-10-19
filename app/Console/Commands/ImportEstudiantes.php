<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
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

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);

            try {
                $this->importRow($data, $dryRun);
                $imported++;

                if ($imported % 10 == 0) {
                    $this->info("Procesados: {$imported} estudiantes...");
                }
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'documento' => $data['numero_documento'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];

                if ($skipped <= 5) {
                    $this->warn("Error en fila {$imported}: " . $e->getMessage());
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

    protected function importRow(array $data, bool $dryRun)
    {
        // Mapear los datos según las columnas esperadas
        $estudianteData = [
            'nombres' => $data['nombres'] ?? $data['Nombres'] ?? null,
            'apellidos' => $data['apellidos'] ?? $data['Apellidos'] ?? null,
            'tipo_documento' => $data['tipo_documento'] ?? $data['Tipo Documento'] ?? 'TI',
            'numero_documento' => $data['numero_documento'] ?? $data['Número Documento'] ?? $data['Documento'] ?? null,
            'fecha_nacimiento' => $this->parseDate($data['fecha_nacimiento'] ?? $data['Fecha Nacimiento'] ?? null),
            'genero' => strtoupper(substr($data['genero'] ?? $data['Género'] ?? 'M', 0, 1)),
            'direccion' => $data['direccion'] ?? $data['Dirección'] ?? null,
            'barrio' => $data['barrio'] ?? $data['Barrio'] ?? null,
            'municipio' => $data['municipio'] ?? $data['Municipio'] ?? 'Medellín',
            'telefono' => $data['telefono'] ?? $data['Teléfono'] ?? null,
            'celular' => $data['celular'] ?? $data['Celular'] ?? null,
            'email' => $data['email'] ?? $data['Email'] ?? null,
            'nombre_acudiente' => $data['nombre_acudiente'] ?? $data['Acudiente'] ?? 'Por definir',
            'telefono_acudiente' => $data['telefono_acudiente'] ?? $data['Tel. Acudiente'] ?? '0000000',
            'email_acudiente' => $data['email_acudiente'] ?? $data['Email Acudiente'] ?? null,
            'parentesco_acudiente' => $data['parentesco_acudiente'] ?? $data['Parentesco'] ?? null,
            'codigo_estudiante' => $data['codigo_estudiante'] ?? $data['Código'] ?? null,
            'estrato' => $data['estrato'] ?? $data['Estrato'] ?? '2',
            'eps' => $data['eps'] ?? $data['EPS'] ?? null,
            'estado' => $data['estado'] ?? $data['Estado'] ?? 'activo',
            'fecha_ingreso' => $this->parseDate($data['fecha_ingreso'] ?? $data['Fecha Ingreso'] ?? now()),
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

        $this->line("  ✓ {$estudiante->nombre_completo}");
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
