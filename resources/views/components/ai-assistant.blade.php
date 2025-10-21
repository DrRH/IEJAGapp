@props([
    'targetTextarea' => '',
    'showContext' => true,
])

<div class="card border-primary" id="ai-assistant-card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title mb-0">
            <i class="ti ti-robot me-2"></i>
            Asistente de IA - ChatGPT
        </h3>
    </div>
    <div class="card-body">
        <!-- Estado de configuración -->
        <div id="ai-status" class="alert alert-info mb-3" style="display: none;">
            <i class="ti ti-info-circle me-2"></i>
            <span id="ai-status-message">Verificando configuración...</span>
        </div>

        @if($showContext)
        <!-- Contexto de la reunión -->
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="ti ti-bulb me-1"></i>
                Información para el asistente
            </label>
            <textarea id="ai-context-input" class="form-control" rows="4"
                      placeholder="Describe los puntos principales de la reunión, temas discutidos, acuerdos preliminares, etc.&#10;&#10;Ejemplo: Se discutió el caso de estudiante con comportamiento agresivo. Los asistentes propusieron trabajo con psicología y reunión con acudiente. Se acordó hacer seguimiento semanal."></textarea>
            <small class="form-hint">
                Proporcione información relevante sobre la reunión para que el asistente genere una sugerencia más precisa.
            </small>
        </div>
        @endif

        <!-- Botones de acción -->
        <div class="btn-group w-100 mb-3" role="group">
            <button type="button" class="btn btn-primary" id="btn-generar-sugerencia">
                <i class="ti ti-wand me-1"></i>
                Generar Sugerencia
            </button>
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="ti ti-adjustments me-1"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#" id="btn-expandir-texto">
                        <i class="ti ti-zoom-in me-2"></i>
                        Expandir texto actual
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="btn-resumir-texto">
                        <i class="ti ti-zoom-out me-2"></i>
                        Resumir texto actual
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="btn-formalizar-texto">
                        <i class="ti ti-writing me-2"></i>
                        Formalizar texto actual
                    </a>
                </li>
            </ul>
        </div>

        <!-- Loading -->
        <div id="ai-loading" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">
                <i class="ti ti-robot me-1"></i>
                El asistente está generando contenido...
            </p>
        </div>

        <!-- Resultado -->
        <div id="ai-result" style="display: none;">
            <label class="form-label fw-bold">
                <i class="ti ti-sparkles me-1"></i>
                Sugerencia del asistente
            </label>
            <div class="card bg-light">
                <div class="card-body">
                    <div id="ai-suggestion-text" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="mt-2 d-flex gap-2">
                <button type="button" class="btn btn-success btn-sm" id="btn-usar-sugerencia">
                    <i class="ti ti-check me-1"></i>
                    Usar esta sugerencia
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-copiar-sugerencia">
                    <i class="ti ti-copy me-1"></i>
                    Copiar
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" id="btn-descartar-sugerencia">
                    <i class="ti ti-x me-1"></i>
                    Descartar
                </button>
            </div>
        </div>

        <!-- Error -->
        <div id="ai-error" class="alert alert-danger" style="display: none;">
            <i class="ti ti-alert-triangle me-2"></i>
            <span id="ai-error-message"></span>
        </div>
    </div>

    <div class="card-footer text-muted small">
        <i class="ti ti-info-circle me-1"></i>
        Este asistente utiliza ChatGPT de OpenAI. Los datos no se almacenan permanentemente en OpenAI.
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetTextarea = document.querySelector('{{ $targetTextarea }}');
    const contextInput = document.getElementById('ai-context-input');
    const statusDiv = document.getElementById('ai-status');
    const statusMessage = document.getElementById('ai-status-message');
    const loadingDiv = document.getElementById('ai-loading');
    const resultDiv = document.getElementById('ai-result');
    const errorDiv = document.getElementById('ai-error');
    const errorMessage = document.getElementById('ai-error-message');
    const suggestionText = document.getElementById('ai-suggestion-text');

    // Verificar estado del asistente al cargar
    verificarEstadoAsistente();

    function verificarEstadoAsistente() {
        fetch('{{ route('ai.verificar-estado') }}')
            .then(response => response.json())
            .then(data => {
                statusDiv.style.display = 'block';
                statusMessage.textContent = data.mensaje;

                if (!data.configurado) {
                    statusDiv.classList.remove('alert-info');
                    statusDiv.classList.add('alert-warning');
                    document.getElementById('btn-generar-sugerencia').disabled = true;
                }
            })
            .catch(error => {
                console.error('Error al verificar estado:', error);
            });
    }

    // Generar sugerencia
    document.getElementById('btn-generar-sugerencia').addEventListener('click', function() {
        generarSugerencia();
    });

    function generarSugerencia() {
        const contextData = obtenerContexto();

        if (!contextData.puntos_principales && !contextData.orden_dia) {
            mostrarError('Por favor, proporcione información en el campo de contexto o complete el orden del día.');
            return;
        }

        ocultarResultados();
        loadingDiv.style.display = 'block';

        fetch('{{ route('ai.generar-sugerencia') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(contextData)
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';

            if (data.success) {
                mostrarSugerencia(data.sugerencia);
            } else {
                mostrarError(data.error || 'Error al generar sugerencia');
            }
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            mostrarError('Error de conexión: ' + error.message);
        });
    }

    // Mejorar texto (expandir, resumir, formalizar)
    document.getElementById('btn-expandir-texto').addEventListener('click', function(e) {
        e.preventDefault();
        mejorarTexto('expandir');
    });

    document.getElementById('btn-resumir-texto').addEventListener('click', function(e) {
        e.preventDefault();
        mejorarTexto('resumir');
    });

    document.getElementById('btn-formalizar-texto').addEventListener('click', function(e) {
        e.preventDefault();
        mejorarTexto('formalizar');
    });

    function mejorarTexto(tipo) {
        const textoActual = targetTextarea ? targetTextarea.value : '';

        if (!textoActual || textoActual.trim().length < 10) {
            mostrarError('Por favor, escriba algo de texto en el campo de desarrollo antes de usar esta función.');
            return;
        }

        ocultarResultados();
        loadingDiv.style.display = 'block';

        fetch('{{ route('ai.mejorar-texto') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                texto: textoActual,
                tipo: tipo
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';

            if (data.success) {
                mostrarSugerencia(data.texto_mejorado);
            } else {
                mostrarError(data.error || 'Error al mejorar texto');
            }
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            mostrarError('Error de conexión: ' + error.message);
        });
    }

    // Usar sugerencia
    document.getElementById('btn-usar-sugerencia').addEventListener('click', function() {
        if (targetTextarea) {
            targetTextarea.value = suggestionText.textContent;
            targetTextarea.focus();
            mostrarExito('Sugerencia aplicada al campo de desarrollo');
            ocultarResultados();
        }
    });

    // Copiar sugerencia
    document.getElementById('btn-copiar-sugerencia').addEventListener('click', function() {
        navigator.clipboard.writeText(suggestionText.textContent)
            .then(() => {
                mostrarExito('Sugerencia copiada al portapapeles');
            })
            .catch(err => {
                console.error('Error al copiar:', err);
            });
    });

    // Descartar sugerencia
    document.getElementById('btn-descartar-sugerencia').addEventListener('click', function() {
        ocultarResultados();
    });

    // Funciones auxiliares
    function obtenerContexto() {
        const data = {};

        // Fecha de reunión
        const fechaInput = document.querySelector('input[name="fecha_reunion"]');
        if (fechaInput) data.fecha_reunion = fechaInput.value;

        // Lugar
        const lugarInput = document.querySelector('input[name="lugar"]');
        if (lugarInput) data.lugar = lugarInput.value;

        // Asistentes
        const asistentesInput = document.querySelector('textarea[name="asistentes"]');
        if (asistentesInput) data.asistentes = asistentesInput.value;

        // Invitados
        const invitadosInput = document.querySelector('textarea[name="invitados"]');
        if (invitadosInput) data.invitados = invitadosInput.value;

        // Orden del día
        const ordenDiaInput = document.querySelector('textarea[name="orden_dia"]');
        if (ordenDiaInput) data.orden_dia = ordenDiaInput.value;

        // Casos revisados
        const casosCheckboxes = document.querySelectorAll('input[name="casos_revisados[]"]:checked');
        if (casosCheckboxes.length > 0) {
            data.casos_revisados = Array.from(casosCheckboxes).map(cb => cb.value);
        }

        // Puntos principales del contexto
        if (contextInput) {
            data.puntos_principales = contextInput.value;
        }

        return data;
    }

    function mostrarSugerencia(texto) {
        suggestionText.textContent = texto;
        resultDiv.style.display = 'block';
    }

    function mostrarError(mensaje) {
        errorMessage.textContent = mensaje;
        errorDiv.style.display = 'block';
    }

    function mostrarExito(mensaje) {
        // Crear toast temporal
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `<i class="ti ti-check me-2"></i>${mensaje}`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    function ocultarResultados() {
        resultDiv.style.display = 'none';
        errorDiv.style.display = 'none';
    }
});
</script>
@endpush
