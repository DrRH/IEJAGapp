<?php

namespace App\Http\Controllers;

use App\Services\AIAssistantService;
use App\Models\ReporteConvivencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AIAssistantController extends Controller
{
    protected AIAssistantService $aiService;

    public function __construct(AIAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Genera sugerencia para el desarrollo de la reunión
     */
    public function generarSugerencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_reunion' => 'nullable|string',
            'lugar' => 'nullable|string',
            'asistentes' => 'nullable|string',
            'invitados' => 'nullable|string',
            'orden_dia' => 'nullable|string',
            'casos_revisados' => 'nullable|array',
            'casos_revisados.*' => 'exists:reportes_convivencia,id',
            'puntos_principales' => 'nullable|string',
            'notas_adicionales' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $context = $request->only([
            'fecha_reunion',
            'lugar',
            'asistentes',
            'invitados',
            'orden_dia',
            'puntos_principales',
            'notas_adicionales'
        ]);

        // Obtener información de los casos si se proporcionaron IDs
        if ($request->has('casos_revisados') && is_array($request->casos_revisados)) {
            $casos = ReporteConvivencia::with(['estudiante', 'tipoAnotacion'])
                ->whereIn('id', $request->casos_revisados)
                ->get();

            $context['casos_revisados'] = $casos->map(function ($caso) {
                return [
                    'id' => $caso->id,
                    'tipo' => $caso->tipoAnotacion->nombre ?? 'Sin tipo',
                    'descripcion' => \Illuminate\Support\Str::limit($caso->descripcion_hechos, 150),
                ];
            })->toArray();
        }

        $resultado = $this->aiService->generarSugerenciaDesarrollo($context);

        if ($resultado['success']) {
            return response()->json([
                'success' => true,
                'sugerencia' => $resultado['sugerencia'],
                'tokens_used' => $resultado['tokens_used'] ?? 0
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $resultado['error'] ?? 'Error desconocido'
        ], 500);
    }

    /**
     * Mejora un texto existente
     */
    public function mejorarTexto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'texto' => 'required|string|min:10',
            'tipo' => 'required|in:expandir,resumir,formalizar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $resultado = $this->aiService->mejorarTexto(
            $request->input('texto'),
            $request->input('tipo')
        );

        if ($resultado['success']) {
            return response()->json([
                'success' => true,
                'texto_mejorado' => $resultado['texto_mejorado'],
                'tokens_used' => $resultado['tokens_used'] ?? 0
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $resultado['error'] ?? 'Error desconocido'
        ], 500);
    }

    /**
     * Genera sugerencia en tiempo real con streaming
     */
    public function sugerenciaStreaming(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contexto' => 'required|string|min:10',
            'tipo' => 'nullable|in:completar,reformular',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Contexto inválido o muy corto (mínimo 10 caracteres)'
            ], 422);
        }

        $contexto = $request->input('contexto');
        $tipo = $request->input('tipo', 'completar');

        // Configurar respuesta de streaming
        return response()->stream(function () use ($contexto, $tipo) {
            $ch = curl_init('https://api.openai.com/v1/chat/completions');

            $apiKey = config('services.openai.api_key');
            $model = config('services.openai.model');

            if (empty($apiKey)) {
                echo "data: " . json_encode(['error' => 'API Key no configurada']) . "\n\n";
                flush();
                return;
            }

            $systemPrompt = $this->getStreamingSystemPrompt();
            $userPrompt = $tipo === 'reformular'
                ? "Reformula de manera más profesional el siguiente párrafo:\n\n{$contexto}"
                : "Continúa escribiendo de manera natural y profesional:\n\n{$contexto}";

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 150,
                    'stream' => true,
                ]),
                CURLOPT_WRITEFUNCTION => function($curl, $data) {
                    echo $data;
                    flush();
                    return strlen($data);
                },
            ]);

            curl_exec($ch);
            curl_close($ch);

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Obtiene el prompt del sistema para streaming
     */
    protected function getStreamingSystemPrompt(): string
    {
        return <<<PROMPT
Eres un asistente de redacción especializado en actas y minutas institucionales de comités escolares de convivencia en Colombia.

Tu función es sugerir la continuación natural del texto que el usuario está escribiendo, manteniendo:
- Tono institucional y profesional
- Lenguaje claro y formal apropiado para documentos oficiales
- Español colombiano neutro
- Coherencia con el contexto ya escrito
- Conformidad con normatividad educativa colombiana

IMPORTANTE:
- Solo sugiere la siguiente frase o párrafo (máximo 2-3 oraciones)
- NO inventes datos, nombres, fechas o hechos específicos
- NO uses lenguaje informal o coloquial
- Mantén consistencia con el contenido previo
- Si el texto habla de casos de estudiantes, usa términos genéricos ("el estudiante", "la estudiante")
PROMPT;
    }

    /**
     * Verifica si la API está configurada y disponible
     */
    public function verificarEstado()
    {
        $apiKey = config('services.openai.api_key');

        return response()->json([
            'configurado' => !empty($apiKey),
            'modelo' => config('services.openai.model'),
            'mensaje' => empty($apiKey)
                ? 'El asistente de IA no está configurado. Configure OPENAI_API_KEY en el archivo .env'
                : 'Asistente de IA disponible'
        ]);
    }
}
