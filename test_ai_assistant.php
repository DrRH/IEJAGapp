<?php

/**
 * Script de prueba del Asistente de IA
 *
 * Ejecutar: php test_ai_assistant.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AIAssistantService;

echo "=== Test del Asistente de IA ===\n\n";

// Verificar configuración
$apiKey = config('services.openai.api_key');
$model = config('services.openai.model');

echo "1. Verificando configuración...\n";
echo "   API Key configurada: " . (!empty($apiKey) ? "✓ Sí (primeros 10 chars: " . substr($apiKey, 0, 10) . "...)" : "✗ No") . "\n";
echo "   Modelo: $model\n\n";

if (empty($apiKey)) {
    echo "⚠️  ADVERTENCIA: La API Key de OpenAI no está configurada.\n";
    echo "   Configure OPENAI_API_KEY en el archivo .env para usar el asistente.\n";
    exit(1);
}

// Crear instancia del servicio
$aiService = new AIAssistantService();

// Test 1: Verificar conexión básica con mejora de texto
echo "2. Probando mejora de texto (resumir)...\n";
$textoTest = "El Comité Escolar de Convivencia se reunió el día de hoy para revisar varios casos de estudiantes que han presentado situaciones de convivencia. Se discutieron estrategias pedagógicas y se acordaron compromisos de seguimiento.";

$resultado = $aiService->mejorarTexto($textoTest, 'resumir');

if ($resultado['success']) {
    echo "   ✓ Conexión exitosa con OpenAI\n";
    echo "   Tokens usados: " . ($resultado['tokens_used'] ?? 'N/A') . "\n";
    echo "   Texto resumido:\n";
    echo "   ---\n";
    echo "   " . wordwrap($resultado['texto_mejorado'], 70, "\n   ") . "\n";
    echo "   ---\n\n";
} else {
    echo "   ✗ Error al conectar con OpenAI\n";
    echo "   Mensaje: " . ($resultado['error'] ?? 'Desconocido') . "\n\n";
    exit(1);
}

// Test 2: Generar sugerencia completa
echo "3. Probando generación de sugerencia completa...\n";
$contextoTest = [
    'fecha_reunion' => '2025-10-19',
    'lugar' => 'Sala de reuniones',
    'asistentes' => "María García - Rectora\nJuan Pérez - Coordinador\nAna López - Psicóloga",
    'orden_dia' => "1. Verificación del quórum\n2. Revisión de caso de estudiante con comportamiento agresivo\n3. Compromisos y seguimiento",
    'puntos_principales' => "Se revisó el caso de un estudiante de grado 8° que presentó comportamiento agresivo con un compañero. Se propuso trabajo con psicología y reunión con acudiente."
];

$resultado = $aiService->generarSugerenciaDesarrollo($contextoTest);

if ($resultado['success']) {
    echo "   ✓ Sugerencia generada exitosamente\n";
    echo "   Tokens usados: " . ($resultado['tokens_used'] ?? 'N/A') . "\n";
    echo "   Desarrollo sugerido:\n";
    echo "   ---\n";
    echo "   " . wordwrap($resultado['sugerencia'], 70, "\n   ") . "\n";
    echo "   ---\n\n";
} else {
    echo "   ✗ Error al generar sugerencia\n";
    echo "   Mensaje: " . ($resultado['error'] ?? 'Desconocido') . "\n\n";
    exit(1);
}

echo "=== ✓ Todas las pruebas completadas exitosamente ===\n";
echo "\nEl Asistente de IA está configurado correctamente y funcionando.\n";
echo "Puede usarlo desde la interfaz web en Actas > Comité de Convivencia.\n";
