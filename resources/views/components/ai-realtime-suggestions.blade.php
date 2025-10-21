@props([
    'targetTextarea' => '',
    'enabled' => true,
])

<!-- Real-time AI Suggestions Component -->
<div class="ai-realtime-wrapper">
    <!-- Toggle para activar/desactivar -->
    <div class="mb-2">
        <label class="form-check form-switch">
            <input type="checkbox" class="form-check-input" id="ai-realtime-toggle" {{ $enabled ? 'checked' : '' }}>
            <span class="form-check-label">
                <i class="ti ti-robot me-1"></i>
                Sugerencias en tiempo real
                <span class="badge bg-purple ms-2">Experimental</span>
            </span>
        </label>
        <small class="form-hint d-block ms-4">
            Presiona <kbd>Tab</kbd> para aceptar sugerencias | <kbd>Esc</kbd> para cancelar
        </small>
    </div>
</div>

@push('styles')
<style>
/* Estilos para el ghost text */
.ai-textarea-wrapper {
    position: relative;
    width: 100%;
}

.ai-ghost-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    padding: 0.5rem 0.75rem;
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
    white-space: pre-wrap;
    word-wrap: break-word;
    overflow: hidden;
    color: transparent;
    z-index: 1;
}

.ai-ghost-text {
    color: #6c757d;
    opacity: 0.5;
    font-style: italic;
}

.ai-loading-indicator {
    position: absolute;
    right: 10px;
    top: 10px;
    z-index: 10;
    background: rgba(255, 255, 255, 0.9);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    color: #206bc4;
    display: none;
}

.ai-loading-indicator.active {
    display: flex;
    align-items: center;
    gap: 6px;
}

.ai-spinner {
    width: 12px;
    height: 12px;
    border: 2px solid #206bc4;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Badge para indicar estado */
.ai-status-badge {
    position: absolute;
    right: 10px;
    bottom: 10px;
    font-size: 10px;
    padding: 2px 6px;
    z-index: 10;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetTextarea = document.querySelector('{{ $targetTextarea }}');
    const toggle = document.getElementById('ai-realtime-toggle');

    if (!targetTextarea) {
        console.error('Target textarea not found: {{ $targetTextarea }}');
        return;
    }

    // Crear wrapper y overlay para ghost text
    const wrapper = document.createElement('div');
    wrapper.className = 'ai-textarea-wrapper';
    targetTextarea.parentNode.insertBefore(wrapper, targetTextarea);
    wrapper.appendChild(targetTextarea);

    // Crear overlay para ghost text
    const overlay = document.createElement('div');
    overlay.className = 'ai-ghost-overlay';
    wrapper.appendChild(overlay);

    // Crear indicador de loading
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'ai-loading-indicator';
    loadingIndicator.innerHTML = '<div class="ai-spinner"></div><span>IA pensando...</span>';
    wrapper.appendChild(loadingIndicator);

    // Variables de estado
    let currentSuggestion = '';
    let isLoading = false;
    let debounceTimer = null;
    let isEnabled = toggle.checked;
    let lastContext = '';

    // Toggle activar/desactivar
    toggle.addEventListener('change', function() {
        isEnabled = this.checked;
        if (!isEnabled) {
            clearSuggestion();
        }
    });

    // Escuchar cambios en el textarea
    targetTextarea.addEventListener('input', function(e) {
        if (!isEnabled) return;

        // Limpiar sugerencia anterior
        clearSuggestion();

        // Debounce para no hacer demasiadas peticiones
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            getSuggestion();
        }, 1500); // Esperar 1.5 segundos de inactividad
    });

    // Manejar teclas
    targetTextarea.addEventListener('keydown', function(e) {
        if (!isEnabled) return;

        // Tab: Aceptar sugerencia
        if (e.key === 'Tab' && currentSuggestion) {
            e.preventDefault();
            acceptSuggestion();
        }

        // Escape: Cancelar sugerencia
        if (e.key === 'Escape' && currentSuggestion) {
            e.preventDefault();
            clearSuggestion();
        }

        // Ctrl+Space: Forzar sugerencia
        if (e.ctrlKey && e.key === ' ') {
            e.preventDefault();
            getSuggestion(true);
        }
    });

    // Función para obtener sugerencia
    async function getSuggestion(force = false) {
        const context = targetTextarea.value.trim();

        // No hacer petición si el contexto es muy corto
        if (context.length < 50 && !force) {
            return;
        }

        // No hacer petición si el contexto no ha cambiado significativamente
        if (context === lastContext && !force) {
            return;
        }

        lastContext = context;
        isLoading = true;
        loadingIndicator.classList.add('active');

        try {
            const response = await fetch('{{ route('ai.sugerencia-streaming') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'text/event-stream',
                },
                body: JSON.stringify({
                    contexto: context,
                    tipo: 'completar'
                })
            });

            if (!response.ok) {
                throw new Error('Error en la petición');
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let suggestion = '';

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value);
                const lines = chunk.split('\n');

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        try {
                            const data = JSON.parse(line.slice(6));
                            if (data.choices && data.choices[0]?.delta?.content) {
                                suggestion += data.choices[0].delta.content;
                                updateGhostText(suggestion);
                            }
                        } catch (e) {
                            // Ignorar errores de parsing
                        }
                    }
                }
            }

            currentSuggestion = suggestion.trim();

        } catch (error) {
            console.error('Error al obtener sugerencia:', error);
        } finally {
            isLoading = false;
            loadingIndicator.classList.remove('active');
        }
    }

    // Actualizar ghost text
    function updateGhostText(suggestion) {
        const currentText = targetTextarea.value;
        overlay.innerHTML = escapeHtml(currentText) + '<span class="ai-ghost-text">' + escapeHtml(suggestion) + '</span>';
        currentSuggestion = suggestion;
    }

    // Aceptar sugerencia
    function acceptSuggestion() {
        if (!currentSuggestion) return;

        const cursorPos = targetTextarea.selectionStart;
        const textBefore = targetTextarea.value.substring(0, cursorPos);
        const textAfter = targetTextarea.value.substring(cursorPos);

        // Insertar sugerencia en la posición del cursor
        targetTextarea.value = textBefore + currentSuggestion + textAfter;

        // Mover cursor al final de la sugerencia
        const newCursorPos = cursorPos + currentSuggestion.length;
        targetTextarea.setSelectionRange(newCursorPos, newCursorPos);

        // Limpiar
        clearSuggestion();

        // Disparar evento de cambio
        targetTextarea.dispatchEvent(new Event('input'));
    }

    // Limpiar sugerencia
    function clearSuggestion() {
        currentSuggestion = '';
        overlay.innerHTML = '';
    }

    // Escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Actualizar overlay cuando el textarea hace scroll
    targetTextarea.addEventListener('scroll', function() {
        overlay.scrollTop = targetTextarea.scrollTop;
        overlay.scrollLeft = targetTextarea.scrollLeft;
    });
});
</script>
@endpush
