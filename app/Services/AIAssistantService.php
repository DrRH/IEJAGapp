<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAssistantService
{
    protected string $apiKey;
    protected string $organization;
    protected string $model;
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->organization = config('services.openai.organization');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Genera sugerencias para el desarrollo de una reunión del Comité de Convivencia
     *
     * @param array $context Contexto de la reunión (asistentes, orden del día, casos, etc.)
     * @return array
     */
    public function generarSugerenciaDesarrollo(array $context): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'La API Key de OpenAI no está configurada. Por favor configure OPENAI_API_KEY en el archivo .env'
            ];
        }

        try {
            $systemPrompt = $this->construirSystemPrompt();
            $userPrompt = $this->construirUserPrompt($context);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'sugerencia' => $data['choices'][0]['message']['content'] ?? '',
                    'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                ];
            }

            Log::error('Error en API OpenAI', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Mensajes de error más específicos
            $errorMessage = match($response->status()) {
                401 => 'API Key inválida o no autorizada. Verifique su configuración.',
                429 => 'Límite de peticiones excedido o créditos insuficientes. Verifique su cuenta en OpenAI Platform.',
                500, 502, 503 => 'OpenAI está experimentando problemas. Intente nuevamente en unos minutos.',
                default => 'Error al comunicarse con OpenAI (código: ' . $response->status() . ')',
            };

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error('Excepción en AIAssistantService', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Error al generar sugerencia: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mejora o expande texto existente
     *
     * @param string $texto Texto a mejorar
     * @param string $tipo Tipo de mejora (expandir, resumir, formalizar)
     * @return array
     */
    public function mejorarTexto(string $texto, string $tipo = 'expandir'): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'La API Key de OpenAI no está configurada.'
            ];
        }

        try {
            $promptMap = [
                'expandir' => 'Expande el siguiente texto de manera profesional y detallada, manteniendo el contexto educativo y de convivencia escolar. Agrega detalles relevantes sin cambiar el sentido original:',
                'resumir' => 'Resume el siguiente texto de manera concisa pero completa, manteniendo los puntos más importantes:',
                'formalizar' => 'Reescribe el siguiente texto de manera más formal y profesional, apropiado para un acta institucional:',
            ];

            $prompt = ($promptMap[$tipo] ?? $promptMap['expandir']) . "\n\n" . $texto;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un asistente especializado en redacción de actas escolares para comités de convivencia en Colombia. Tu tarea es ayudar a mejorar textos de manera profesional y apropiada para documentos oficiales educativos.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'texto_mejorado' => $data['choices'][0]['message']['content'] ?? '',
                    'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                ];
            }

            // Mensajes de error más específicos
            $errorMessage = match($response->status()) {
                401 => 'API Key inválida o no autorizada. Verifique su configuración.',
                429 => 'Límite de peticiones excedido o créditos insuficientes. Verifique su cuenta en OpenAI Platform.',
                500, 502, 503 => 'OpenAI está experimentando problemas. Intente nuevamente en unos minutos.',
                default => 'Error al comunicarse con OpenAI (código: ' . $response->status() . ')',
            };

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error('Excepción en mejorarTexto', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Error al mejorar texto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Genera sugerencia en tiempo real (streaming)
     *
     * @param string $contexto Texto actual hasta el cursor
     * @param string $tipo Tipo de sugerencia (completar, reformular)
     * @return \Generator
     */
    public function generarSugerenciaStreaming(string $contexto, string $tipo = 'completar'): \Generator
    {
        if (empty($this->apiKey)) {
            yield json_encode([
                'error' => 'API Key no configurada',
                'done' => true
            ]);
            return;
        }

        try {
            $systemPrompt = $this->construirSystemPromptStreaming();
            $userPrompt = $this->construirUserPromptStreaming($contexto, $tipo);

            $ch = curl_init('https://api.openai.com/v1/chat/completions');

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt
                        ]
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

        } catch (\Exception $e) {
            yield json_encode([
                'error' => $e->getMessage(),
                'done' => true
            ]);
        }
    }

    /**
     * Construye el prompt del sistema para streaming
     */
    protected function construirSystemPromptStreaming(): string
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
     * Construye el prompt del usuario para streaming
     */
    protected function construirUserPromptStreaming(string $contexto, string $tipo): string
    {
        if ($tipo === 'reformular') {
            return "Reformula de manera más profesional el siguiente párrafo:\n\n{$contexto}";
        }

        // tipo === 'completar' (por defecto)
        return "Continúa escribiendo de manera natural y profesional:\n\n{$contexto}";
    }

    /**
     * Construye el prompt del sistema
     */
    protected function construirSystemPrompt(): string
    {
        return <<<PROMPT
Eres un asistente especializado en la redacción de actas para Comités Escolares de Convivencia en instituciones educativas de Colombia.

Tu función es ayudar a redactar el desarrollo de reuniones del comité de convivencia de manera profesional, clara y conforme a la normatividad colombiana (Ley 1620 de 2013 y Decreto 1965 de 2013).

Debes:
- Usar lenguaje formal y apropiado para documentos oficiales
- Estructurar el contenido de manera clara y organizada
- Incluir todos los elementos importantes mencionados
- Mantener objetividad y profesionalismo
- Usar terminología educativa y legal apropiada
- Respetar la confidencialidad y dignidad de los estudiantes (usar "el estudiante" o "la estudiante" en lugar de nombres cuando sea apropiado)
- Sugerir acciones pedagógicas y restaurativas cuando sea pertinente

NO debes:
- Inventar información no proporcionada
- Hacer juicios de valor personales
- Sugerir sanciones que no estén en el marco legal colombiano
- Usar lenguaje informal o coloquial
PROMPT;
    }

    /**
     * Construye el prompt del usuario con el contexto
     */
    protected function construirUserPrompt(array $context): string
    {
        $prompt = "Ayúdame a redactar el desarrollo de una reunión del Comité Escolar de Convivencia con la siguiente información:\n\n";

        if (!empty($context['fecha_reunion'])) {
            $prompt .= "**Fecha de la reunión:** {$context['fecha_reunion']}\n";
        }

        if (!empty($context['lugar'])) {
            $prompt .= "**Lugar:** {$context['lugar']}\n";
        }

        if (!empty($context['asistentes'])) {
            $prompt .= "\n**Asistentes:**\n{$context['asistentes']}\n";
        }

        if (!empty($context['invitados'])) {
            $prompt .= "\n**Invitados especiales:**\n{$context['invitados']}\n";
        }

        if (!empty($context['orden_dia'])) {
            $prompt .= "\n**Orden del día:**\n{$context['orden_dia']}\n";
        }

        if (!empty($context['casos_revisados'])) {
            $prompt .= "\n**Casos revisados en la reunión:**\n";
            foreach ($context['casos_revisados'] as $caso) {
                $prompt .= "- Caso #{$caso['id']}: {$caso['tipo']} - {$caso['descripcion']}\n";
            }
        }

        if (!empty($context['puntos_principales'])) {
            $prompt .= "\n**Puntos principales discutidos:**\n{$context['puntos_principales']}\n";
        }

        if (!empty($context['notas_adicionales'])) {
            $prompt .= "\n**Notas adicionales:**\n{$context['notas_adicionales']}\n";
        }

        $prompt .= "\nPor favor, redacta un desarrollo detallado y profesional de esta reunión que pueda ser usado como contenido oficial del acta. El texto debe ser coherente, bien estructurado y apropiado para un documento institucional.";

        return $prompt;
    }
}
