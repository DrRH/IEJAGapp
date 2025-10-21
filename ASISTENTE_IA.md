# Asistente de IA con ChatGPT

## Descripción

El sistema integra un asistente de inteligencia artificial basado en ChatGPT de OpenAI para ayudar a los usuarios a redactar el contenido de las actas del Comité Escolar de Convivencia.

## Características

### 1. 🆕 Sugerencias en Tiempo Real (Experimental)
**Nuevo:** Autocompletado inteligente tipo "GitHub Copilot" mientras escribe:
- **Ghost text**: Sugerencias aparecen como texto fantasma a la derecha del cursor
- **Streaming**: La IA genera el texto en tiempo real (palabra por palabra)
- **Atajos de teclado**:
  - `Tab`: Aceptar sugerencia
  - `Esc`: Cancelar sugerencia
  - `Ctrl+Space`: Forzar sugerencia inmediata
- **Adaptativo**: Se activa después de 1.5 segundos sin escribir
- **Configurable**: Se puede activar/desactivar con un interruptor

### 2. Generación de Sugerencias Completas
El asistente puede generar automáticamente el desarrollo completo de una reunión basándose en:
- Fecha y lugar de la reunión
- Lista de asistentes e invitados
- Orden del día
- Casos revisados (extraídos automáticamente de la base de datos)
- Puntos principales proporcionados por el usuario

### 3. Mejora de Texto
El asistente puede mejorar texto ya escrito de tres maneras:
- **Expandir**: Agrega detalles y profundiza el contenido existente
- **Resumir**: Condensa el texto manteniendo los puntos principales
- **Formalizar**: Reescribe en lenguaje más formal y profesional

### 4. Características de Seguridad
- La API Key de OpenAI se configura en el servidor (no se expone al usuario)
- Los datos no se almacenan permanentemente en OpenAI
- El sistema verifica la configuración antes de permitir el uso
- Se registra el uso de tokens para control de costos

## Configuración

### Paso 1: Obtener API Key de OpenAI

**⚠️ IMPORTANTE**: La API de OpenAI es **independiente** de la suscripción ChatGPT Plus/Pro. Aunque tenga ChatGPT Pro, necesita:
1. **Agregar créditos** a su cuenta de API
2. **Configurar método de pago** en la plataforma de API

**Pasos:**

1. Cree una cuenta en [OpenAI Platform](https://platform.openai.com/)
2. Vaya a [Billing](https://platform.openai.com/account/billing/overview)
3. **Agregue un método de pago** (tarjeta de crédito)
4. **Agregue créditos** (mínimo $5 USD recomendado)
5. Vaya a [API Keys](https://platform.openai.com/api-keys)
6. Cree una nueva API Key (Secret Key)
7. Copie la clave (solo se muestra una vez)

### Paso 2: Configurar el Sistema

Edite el archivo `.env` en la raíz del proyecto y configure las siguientes variables:

```env
# --- OPENAI API ---
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_ORGANIZATION=org-xxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4o-mini
```

**Notas:**
- `OPENAI_API_KEY`: Su clave de API de OpenAI (obligatorio)
- `OPENAI_ORGANIZATION`: ID de organización (opcional)
- `OPENAI_MODEL`: Modelo a usar (por defecto `gpt-4o-mini`, recomendado por su bajo costo)

### Modelos Disponibles

| Modelo | Descripción | Costo (aproximado) |
|--------|-------------|-------------------|
| `gpt-4o-mini` | Rápido y económico (recomendado) | $0.15 / 1M tokens |
| `gpt-4o` | Más potente pero costoso | $2.50 / 1M tokens |
| `gpt-3.5-turbo` | Versión anterior, económica | $0.50 / 1M tokens |

**Recomendación**: Use `gpt-4o-mini` para producción. Es suficientemente capaz para esta tarea.

### Paso 3: Limpiar Caché

Después de configurar, limpie el caché de Laravel:

```bash
php artisan config:clear
php artisan cache:clear
```

## Uso

### Modo 1: Sugerencias en Tiempo Real (Recomendado)

1. **Ir a** Crear/Editar Acta del Comité de Convivencia
2. **Activar** el interruptor "Sugerencias en tiempo real" (activado por defecto)
3. **Comenzar a escribir** en el campo "Desarrollo de la Reunión"
4. **Esperar 1.5 segundos** sin escribir
5. **Ver sugerencia** aparecer como texto gris (ghost text)
6. **Presionar `Tab`** para aceptar o seguir escribiendo para ignorar

**Atajos de teclado:**
- `Tab`: Aceptar sugerencia completa
- `Esc`: Cancelar/ocultar sugerencia
- `Ctrl+Space`: Forzar sugerencia inmediata

**Ejemplo de uso:**
```
Usted escribe: "El comité se reunió para revisar el caso del estudiante de grado 8"
[Pausa 1.5 seg]
IA sugiere: "°, quien presentó comportamiento disruptivo en clase. Se analizaron los antecedentes y se propuso reunión con el acudiente..."
[Presionar Tab para aceptar]
```

### Modo 2: Generación Completa con Asistente

1. **Crear o Editar Acta**: Navegue a la sección de crear/editar acta del Comité de Convivencia

2. **Complete la Información Básica**:
   - Fecha de reunión
   - Lugar
   - Asistentes
   - Orden del día
   - Seleccione casos revisados (opcional)

3. **Scroll down** hasta el panel "Asistente de IA - ChatGPT"

4. **Proporcione Contexto**:
   - En el campo "Información para el asistente", escriba los puntos principales de la reunión
   - Ejemplo: "Se discutió el caso de estudiante con comportamiento agresivo. Los asistentes propusieron trabajo con psicología y reunión con acudiente."

5. **Generar Sugerencia**:
   - Haga clic en "Generar Sugerencia"
   - El asistente procesará la información y generará un desarrollo completo
   - Revise la sugerencia generada

6. **Usar o Modificar**:
   - Haga clic en "Usar esta sugerencia" para insertarla en el campo de desarrollo
   - O copie partes específicas
   - Puede editar el texto generado según necesite

### Mejorar Texto Existente

Si ya tiene texto en el campo de desarrollo:

1. **Expandir Texto**: Agrega más detalles al texto existente
2. **Resumir Texto**: Condensa el contenido manteniendo lo esencial
3. **Formalizar Texto**: Mejora el lenguaje para hacerlo más profesional

## Prompt del Sistema

El asistente está configurado específicamente para:
- Usar lenguaje formal apropiado para documentos oficiales educativos
- Respetar la normatividad colombiana (Ley 1620 de 2013, Decreto 1965 de 2013)
- Mantener objetividad y profesionalismo
- Proteger la confidencialidad de los estudiantes
- Sugerir acciones pedagógicas y restaurativas

## Seguridad y Privacidad

### Datos que se Envían a OpenAI
- Información de la reunión (fecha, lugar, asistentes)
- Orden del día
- Descripción resumida de casos (sin nombres de estudiantes)
- Contexto proporcionado por el usuario

### Datos que NO se Envían
- Nombres completos de estudiantes
- Información confidencial de casos
- Datos personales sensibles

### Almacenamiento
- OpenAI puede retener datos por hasta 30 días para monitoreo de abuso
- Después de 30 días, los datos se eliminan
- No se usan para entrenar modelos (si usa API con configuración de empresa)

## Costos

El costo se calcula por tokens (palabras procesadas):

**Ejemplo de Uso Típico:**
- Generar un acta completa: ~500-1000 tokens
- Costo por acta: ~$0.0001 USD (usando gpt-4o-mini)
- 1000 actas: ~$0.10 USD

**Recomendaciones de Control de Costos:**
1. Use el modelo `gpt-4o-mini` (más económico)
2. Configure límites mensuales en su cuenta de OpenAI
3. Monitoree el uso desde el dashboard de OpenAI

## Solución de Problemas

### El asistente muestra "No configurado"

**Solución:**
1. Verifique que `OPENAI_API_KEY` esté en el archivo `.env`
2. Ejecute `php artisan config:clear`
3. Recargue la página

### Error: "Invalid API Key"

**Solución:**
1. Verifique que la API Key sea correcta
2. Asegúrese de que la cuenta de OpenAI esté activa
3. Verifique que tenga créditos disponibles

### Error: "Insufficient quota" o "Rate limit exceeded" (Error 429)

Este es el error **más común** y significa que su cuenta de API no tiene créditos suficientes.

**⚠️ ACLARACIÓN IMPORTANTE:**
- **ChatGPT Plus/Pro** ($20/mes) ≠ **OpenAI API** (pago por uso)
- Son sistemas de facturación **completamente separados**
- Tener ChatGPT Pro NO incluye créditos de API

**Solución:**

1. **Ir a** [OpenAI Platform Billing](https://platform.openai.com/account/billing/overview)
2. **Verificar** que tenga un método de pago configurado
3. **Agregar créditos**:
   - Haga clic en "Add to credit balance"
   - Agregue mínimo $5 USD (durará mucho tiempo con este sistema)
4. **Configurar límites mensuales** (recomendado):
   - Vaya a [Usage Limits](https://platform.openai.com/account/limits)
   - Configure un límite (ej: $10/mes) para evitar gastos inesperados
5. **Esperar** 1-2 minutos para que se active
6. **Intentar nuevamente**

**Otras causas posibles:**
- Ha excedido el límite de peticiones por minuto (RPM): Espere 1 minuto
- API Key expirada o revocada: Genere una nueva en [API Keys](https://platform.openai.com/api-keys)

### El texto generado no es apropiado

**Solución:**
1. Proporcione más contexto específico
2. Sea más detallado en el campo de información
3. Edite el texto generado según necesite

## API Endpoints

El sistema expone los siguientes endpoints (uso interno):

### POST `/api/ai-assistant/generar-sugerencia`
Genera sugerencia completa del desarrollo de la reunión.

**Parámetros:**
```json
{
  "fecha_reunion": "2025-10-19",
  "lugar": "Sala de reuniones",
  "asistentes": "Lista de asistentes...",
  "orden_dia": "1. Punto 1\n2. Punto 2",
  "casos_revisados": [1, 2, 3],
  "puntos_principales": "Descripción de lo discutido..."
}
```

### POST `/api/ai-assistant/mejorar-texto`
Mejora texto existente.

**Parámetros:**
```json
{
  "texto": "Texto a mejorar...",
  "tipo": "expandir|resumir|formalizar"
}
```

### GET `/api/ai-assistant/verificar-estado`
Verifica si la API está configurada.

**Respuesta:**
```json
{
  "configurado": true,
  "modelo": "gpt-4o-mini",
  "mensaje": "Asistente de IA disponible"
}
```

## Arquitectura Técnica

### Componentes

1. **AIAssistantService** (`app/Services/AIAssistantService.php`)
   - Servicio principal que se comunica con OpenAI
   - Construye prompts especializados
   - Maneja errores y timeouts

2. **AIAssistantController** (`app/Http/Controllers/AIAssistantController.php`)
   - Controlador que maneja peticiones HTTP
   - Valida datos de entrada
   - Retorna respuestas JSON

3. **Componente Blade** (`resources/views/components/ai-assistant.blade.php`)
   - Interfaz de usuario reutilizable
   - Maneja interacción con el usuario
   - Gestiona estados de carga y errores

### Flujo de Datos

```
Usuario completa formulario
       ↓
Hace clic en "Generar Sugerencia"
       ↓
JavaScript recopila información del formulario
       ↓
POST a /api/ai-assistant/generar-sugerencia
       ↓
AIAssistantController valida datos
       ↓
AIAssistantService construye prompt
       ↓
Petición HTTP a OpenAI API
       ↓
OpenAI procesa y genera respuesta
       ↓
Respuesta se devuelve al usuario
       ↓
Usuario puede usar, editar o descartar
```

## Mantenimiento

### Actualizar Modelo
Para usar un modelo diferente, edite `.env`:
```env
OPENAI_MODEL=gpt-4o
```

### Monitoreo de Uso
1. Visite [OpenAI Usage Dashboard](https://platform.openai.com/usage)
2. Revise el consumo diario/mensual
3. Configure alertas de presupuesto

### Logs
Los errores se registran en `storage/logs/laravel.log` con el prefijo `AIAssistantService`.

## Mejoras Futuras

Posibles mejoras al sistema:
- [ ] Cache de sugerencias similares
- [ ] Historial de sugerencias generadas
- [ ] Personalización de prompts por institución
- [ ] Integración con otros módulos (casos, estudiantes)
- [ ] Análisis de sentimiento en casos
- [ ] Sugerencias de seguimiento automático

## Soporte

Para problemas técnicos:
1. Revise los logs en `storage/logs/laravel.log`
2. Verifique la configuración en `.env`
3. Consulte la documentación de OpenAI: https://platform.openai.com/docs

---

**Última actualización**: Octubre 2025
**Versión**: 1.0.0
