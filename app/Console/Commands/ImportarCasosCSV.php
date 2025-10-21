<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use App\Models\ReporteConvivencia;
use App\Models\TipoAnotacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarCasosCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casos:importar-csv {archivo?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar casos disciplinarios desde archivo CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $archivo = $this->argument('archivo') ?? storage_path('app/private/tmp/Registro de actas de atención a situaciones - Actas.csv');

        if (!file_exists($archivo)) {
            $this->error("El archivo no existe: {$archivo}");
            return 1;
        }

        $this->info("Importando casos desde: {$archivo}");

        // Obtener o crear tipo de anotación por defecto
        $tipoTipo2 = TipoAnotacion::firstOrCreate(
            ['nombre' => 'Situación Tipo II'],
            [
                'descripcion' => 'Situaciones que afectan la convivencia escolar',
                'categoria' => 'grave',
                'requiere_reporte' => true
            ]
        );

        $tipoTipo1 = TipoAnotacion::firstOrCreate(
            ['nombre' => 'Situación Tipo I'],
            [
                'descripcion' => 'Situaciones leves de convivencia',
                'categoria' => 'leve',
                'requiere_reporte' => false
            ]
        );

        $handle = fopen($archivo, 'r');

        // Leer encabezado
        $headers = fgetcsv($handle);

        $importados = 0;
        $errores = 0;
        $noEncontrados = [];

        $this->info("Procesando casos...");
        $bar = $this->output->createProgressBar();

        while (($data = fgetcsv($handle)) !== false) {
            $bar->advance();

            try {
                // Mapear datos del CSV
                $registro = array_combine($headers, $data);

                // Buscar estudiante por número de documento
                $numeroDoc = trim($registro['NUMDOCEST']);
                if (empty($numeroDoc)) {
                    $this->warn("\nCaso {$registro['ACTA']}: Sin número de documento");
                    $errores++;
                    continue;
                }

                $estudiante = Estudiante::where('numero_documento', $numeroDoc)->first();

                if (!$estudiante) {
                    $noEncontrados[] = [
                        'acta' => $registro['ACTA'],
                        'nombre' => $registro['ESTUDIANTES'],
                        'documento' => $numeroDoc
                    ];
                    $errores++;
                    continue;
                }

                // Determinar tipo de anotación
                $tipo = strtoupper(trim($registro['TIPO'] ?? ''));
                $tipoAnotacion = (strpos($tipo, 'TIPO II') !== false || strpos($tipo, 'II') !== false)
                    ? $tipoTipo2
                    : $tipoTipo1;

                // Parsear fecha
                $fecha = $this->parsearFecha($registro['FECHA']);

                // Crear caso
                $caso = ReporteConvivencia::create([
                    'estudiante_id' => $estudiante->id,
                    'tipo_anotacion_id' => $tipoAnotacion->id,
                    'reportado_por' => 1, // Usuario admin por defecto
                    'fecha_reporte' => $fecha,
                    'hora_reporte' => $this->parsearHora($registro['HORA']),
                    'descripcion_hechos' => $registro['DESARROLLO'] ?? 'Sin descripción',
                    'lugar' => null,
                    'testigos' => null,
                    'evidencias' => null,
                    'acciones_tomadas' => $registro['COMPROMISOINST'] ?? null,
                    'acudiente_notificado' => !empty($registro['ACUDIENTES']),
                    'fecha_notificacion_acudiente' => !empty($registro['ACUDIENTES']) ? $fecha : null,
                    'medio_notificacion' => !empty($registro['ACUDIENTES']) ? 'presencial' : null,
                    'respuesta_acudiente' => null,
                    'requirio_compromiso' => !empty($registro['COMPROMISOINST']),
                    'compromiso' => $registro['COMPROMISOINST'] ?? null,
                    'fecha_compromiso' => !empty($registro['COMPROMISOINST']) ? $fecha : null,
                    'compromiso_cumplido' => null,
                    'requirio_suspension' => false,
                    'dias_suspension' => 0,
                    'fecha_inicio_suspension' => null,
                    'fecha_fin_suspension' => null,
                    'remitido_psicologia' => false,
                    'fecha_remision_psicologia' => null,
                    'observaciones_psicologia' => null,
                    'estado' => !empty($registro['CIERRE']) ? 'cerrado' : 'abierto',
                    'fecha_cierre' => !empty($registro['CIERRE']) ? $fecha : null,
                    'cerrado_por' => !empty($registro['CIERRE']) ? 1 : null,
                    'observaciones_cierre' => $registro['CIERRE'] ?? null,
                    'observaciones_generales' => $registro['SEGUIMIENTO'] ?? null,
                ]);

                $importados++;

            } catch (\Exception $e) {
                $this->error("\nError en caso {$registro['ACTA']}: " . $e->getMessage());
                $errores++;
            }
        }

        fclose($handle);
        $bar->finish();

        $this->newLine(2);
        $this->info("Importación completada:");
        $this->info("- Casos importados: {$importados}");
        $this->info("- Errores: {$errores}");

        if (!empty($noEncontrados)) {
            $this->newLine();
            $this->warn("Estudiantes no encontrados en la base de datos:");
            $this->table(
                ['Acta', 'Nombre', 'Documento'],
                array_map(fn($item) => [$item['acta'], $item['nombre'], $item['documento']], $noEncontrados)
            );
        }

        return 0;
    }

    private function parsearFecha($fecha)
    {
        try {
            // Intentar varios formatos de fecha
            $formatos = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d'];

            foreach ($formatos as $formato) {
                $date = \DateTime::createFromFormat($formato, trim($fecha));
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // Si no funciona ningún formato, usar strtotime
            $timestamp = strtotime(trim($fecha));
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }

            // Si todo falla, usar fecha actual
            return date('Y-m-d');
        } catch (\Exception $e) {
            return date('Y-m-d');
        }
    }

    private function parsearHora($hora)
    {
        try {
            if (empty($hora)) {
                return null;
            }

            // Limpiar y parsear hora
            $hora = trim($hora);

            // Formato HH:MM
            if (preg_match('/^(\d{1,2}):(\d{2})/', $hora, $matches)) {
                return sprintf('%02d:%02d:00', $matches[1], $matches[2]);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
