<?php

/**
 * Test detallado de conexión con OpenAI
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Test Detallado de Conexión OpenAI ===\n\n";

$apiKey = config('services.openai.api_key');
$model = config('services.openai.model');

echo "API Key: " . substr($apiKey, 0, 20) . "...\n";
echo "Modelo: $model\n\n";

echo "Enviando petición de prueba a OpenAI...\n";

try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ])
    ->timeout(30)
    ->post('https://api.openai.com/v1/chat/completions', [
        'model' => $model,
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Di hola en una palabra'
            ]
        ],
        'max_tokens' => 10,
    ]);

    echo "Status Code: " . $response->status() . "\n\n";

    if ($response->successful()) {
        echo "✓ Conexión exitosa!\n";
        $data = $response->json();
        echo "Respuesta: " . ($data['choices'][0]['message']['content'] ?? 'N/A') . "\n";
        echo "Tokens usados: " . ($data['usage']['total_tokens'] ?? 'N/A') . "\n";
    } else {
        echo "✗ Error en la petición\n";
        echo "Código: " . $response->status() . "\n";
        echo "Body:\n";
        echo $response->body() . "\n";
    }

} catch (\Exception $e) {
    echo "✗ Excepción: " . $e->getMessage() . "\n";
}
