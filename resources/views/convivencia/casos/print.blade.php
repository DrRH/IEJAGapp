<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Acta de Atención a Situación de Convivencia N° {{ $caso->numero_acta ?? $caso->id }}</title>
    <style>
        /**
         * Plantilla oficial de Acta de Atención a Situación de Convivencia
         * Institución Educativa José Antonio Galán
         *
         * Margen superior: 0.5cm (encabezado se repite automáticamente)
         * Margen inferior: 2cm (pie de página se repite automáticamente)
         * Margen izquierdo: 2.0 cm (según directrices)
         */

        @page {
            margin: 3cm 2cm 2cm 2cm;
            size: letter;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
        }


        /* Tablas de información */
        .info-table {
            width: 100%;
            border: 2px solid #000;
            border-collapse: collapse;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9pt;
        }

        .info-label {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        /* Secciones de contenido */
        .content-section {
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .content-section.allow-break {
            page-break-inside: auto;
        }

        .section-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            text-align: center;
        }

        .content-section p {
            text-align: justify;
            margin-bottom: 6px;
            font-size: 9pt;
        }

        .content-section ul {
            margin-left: 15px;
            margin-bottom: 6px;
        }

        .content-section li {
            margin-bottom: 3px;
            font-size: 9pt;
        }

        .field-label {
            font-weight: bold;
            margin-top: 6px;
            margin-bottom: 3px;
        }

        /* Sección de firmas */
        .signatures-section {
            border: 2px solid #000;
            padding: 10px;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .signatures-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }

        .signature-space {
            height: 50px;
            border-bottom: 1px solid #999;
            margin-bottom: 5px;
        }

        .signature-label {
            font-size: 9pt;
            line-height: 1.3;
        }

        .signature-label strong {
            font-weight: bold;
        }

        .signature-label small {
            font-size: 8pt;
        }

        /* Alerts y badges */
        .alert {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            page-break-inside: avoid;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border: 1px solid #000;
            background-color: #e9ecef;
        }

        /* Utilidades */
        .text-uppercase {
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .mb-1 {
            margin-bottom: 4px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        /* Evitar saltos de página no deseados */
        h3, h4 {
            page-break-after: avoid;
        }

        table {
            page-break-inside: avoid;
        }

        /* Estilos de impresión */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            thead {
                display: table-header-group !important;
            }

            tfoot {
                display: table-footer-group !important;
            }
        }

        /* Botón de impresión - oculto en PDF */
        .print-button {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- Botón de impresión -->
    <button onclick="window.print()" class="print-button no-print">
        Imprimir Acta
    </button>

    <!-- FOOTER con numeración de páginas -->
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Arial");
            $size = 8;
            $color = array(0.4, 0.4, 0.4);
            $w = $pdf->get_width();
            $h = $pdf->get_height();
            $pdf->page_text($w / 2, $h - 30, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, $size, $color, 0, 0, "center");
        }
    </script>

    <!-- CONTENIDO PRINCIPAL -->
                    <!-- INFORMACIÓN BÁSICA -->
                    <table class="info-table">
                        <tr>
                            <td class="info-label" style="width: 12%;">Grupo:</td>
                            <td style="width: 38%;"><strong>{{ $caso->estudiante->matriculaActual->grupo->nombre ?? 'N/A' }}</strong></td>
                            <td class="info-label" style="width: 12%;">Fecha:</td>
                            <td style="width: 18%;">{{ $caso->fecha_reporte->format('d/m/Y') }}</td>
                            <td class="info-label" style="width: 8%;">Hora:</td>
                            <td style="width: 12%;">{{ $caso->hora_reporte ? \Carbon\Carbon::parse($caso->hora_reporte)->format('H:i') : $caso->created_at->format('H:i') }}</td>
                        </tr>
                    </table>

                    <!-- ESTUDIANTES Y ACUDIENTES -->
                    <table class="info-table">
                        <tr>
                            <td class="info-label" style="width: 18%; vertical-align: top;">Estudiante(s):</td>
                            <td style="width: 32%; vertical-align: top;">
                                <div class="mb-1">
                                    <strong>● {{ strtoupper($caso->estudiante->nombre_completo) }}</strong><br>
                                    <small>{{ $caso->estudiante->tipo_documento }}. {{ $caso->estudiante->numero_documento }}</small>
                                </div>
                                @if($caso->estudiantesInvolucrados && $caso->estudiantesInvolucrados->count() > 0)
                                    @foreach($caso->estudiantesInvolucrados as $involucrado)
                                        <div class="mb-1">
                                            <strong>● {{ strtoupper($involucrado->nombre_completo) }}</strong>
                                            <span class="badge">{{ ucfirst($involucrado->pivot->rol) }}</span><br>
                                            <small>{{ $involucrado->tipo_documento }}. {{ $involucrado->numero_documento }}</small>
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                            <td class="info-label" style="width: 18%; vertical-align: top;">Acudiente(s):</td>
                            <td style="width: 32%; vertical-align: top;">
                                @if($caso->estudiante->nombre_acudiente)
                                    <div class="mb-1">
                                        <strong>● {{ strtoupper($caso->estudiante->nombre_acudiente) }}</strong><br>
                                        @if($caso->estudiante->telefono_acudiente)
                                            <small>Cel: {{ $caso->estudiante->telefono_acudiente }}</small><br>
                                        @endif
                                        @if($caso->estudiante->email_acudiente)
                                            <small>Email: {{ $caso->estudiante->email_acudiente }}</small>
                                        @endif
                                    </div>
                                @else
                                    <strong>● SIN REGISTRO</strong>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <!-- RESPONSABLES -->
                    <table class="info-table">
                        <tr>
                            <td class="info-label" style="width: 22%;">Director(a) de grupo:</td>
                            <td style="width: 78%;">{{ $caso->estudiante->matriculaActual->grupo->directorGrupo->name ?? 'Sin asignar' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Coordinador(a):</td>
                            <td>{{ $caso->reportadoPor->name ?? 'Sin asignar' }}</td>
                        </tr>
                    </table>

                    <!-- DESARROLLO DE LA REUNIÓN -->
                    <div class="content-section allow-break">
                        <h3 class="section-title">DESARROLLO DE LA REUNIÓN</h3>

                        @if($caso->descripcion_hechos)
                            <p class="field-label">Descripción de los hechos:</p>
                            <p>{!! nl2br(e($caso->descripcion_hechos)) !!}</p>
                        @endif

                        @if($caso->lugar)
                            <p class="field-label">Lugar:</p>
                            <p>{{ $caso->lugar }}</p>
                        @endif

                        @if($caso->testigos)
                            <p class="field-label">Testigos:</p>
                            <p>{!! nl2br(e($caso->testigos)) !!}</p>
                        @endif

                        @if($caso->evidencias)
                            <p class="field-label">Evidencias:</p>
                            <p>{!! nl2br(e($caso->evidencias)) !!}</p>
                        @endif

                        @if($caso->contexto_situacion)
                            <p class="field-label">Contexto de la situación:</p>
                            <p>{!! nl2br(e($caso->contexto_situacion)) !!}</p>
                        @endif

                        @if($caso->analisis_institucional)
                            <p class="field-label">Análisis institucional:</p>
                            <p>{!! nl2br(e($caso->analisis_institucional)) !!}</p>
                        @endif

                        @if($caso->conclusiones)
                            <p class="field-label">Conclusiones:</p>
                            <p>{!! nl2br(e($caso->conclusiones)) !!}</p>
                        @endif

                        @if($caso->acciones_tomadas)
                            <p class="field-label">Acciones inmediatas tomadas:</p>
                            <p>{!! nl2br(e($caso->acciones_tomadas)) !!}</p>
                        @endif

                        @if($caso->acciones_pedagogicas)
                            <p class="field-label">Acciones pedagógicas y restaurativas:</p>
                            <p>{!! nl2br(e($caso->acciones_pedagogicas)) !!}</p>
                        @endif
                    </div>

                    <!-- TIPIFICACIÓN -->
                    @if($caso->tipoAnotacion)
                    <div class="content-section">
                        <p class="field-label">Tipificación según Manual de Convivencia Escolar:</p>
                        <p>
                            Esta situación está clasificada como
                            <strong>
                            @if($caso->tipoAnotacion->tipo_situacion == 'tipo_i')
                                SITUACIÓN TIPO I
                            @elseif($caso->tipoAnotacion->tipo_situacion == 'tipo_ii')
                                SITUACIÓN TIPO II
                            @elseif($caso->tipoAnotacion->tipo_situacion == 'tipo_iii')
                                SITUACIÓN TIPO III
                            @else
                                {{ strtoupper($caso->tipoAnotacion->categoria) }}
                            @endif
                            </strong>
                        </p>

                        @if($caso->tipoAnotacion->descripcion)
                            <p class="mt-2"><strong>{{ $caso->tipoAnotacion->nombre }}:</strong></p>
                            <p>{{ $caso->tipoAnotacion->descripcion }}</p>
                        @endif

                        @if($caso->numerales && $caso->numerales->count() > 0)
                            <p class="field-label mt-2">Numerales aplicables del Manual de Convivencia:</p>
                            <ul>
                                @foreach($caso->numerales as $numeral)
                                    <li><strong>Numeral {{ $numeral->nombre }}:</strong> {{ $numeral->descripcion }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    @endif

                    <!-- SUSPENSIÓN -->
                    @if($caso->requirio_suspension)
                    <div class="alert">
                        <p class="field-label">SUSPENSIÓN APLICADA:</p>
                        <p>
                            Se aplicó suspensión de <strong>{{ $caso->dias_suspension }} día(s)</strong><br>
                            Fecha inicio: {{ $caso->fecha_inicio_suspension ? $caso->fecha_inicio_suspension->format('d/m/Y') : 'N/A' }}<br>
                            Fecha fin: {{ $caso->fecha_fin_suspension ? $caso->fecha_fin_suspension->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    @endif

                    <!-- REMISIÓN A PSICOLOGÍA -->
                    @if($caso->remitido_psicologia)
                    <div class="alert">
                        <p class="field-label">REMISIÓN A PSICOLOGÍA:</p>
                        <p>
                            Fecha de remisión: {{ $caso->fecha_remision_psicologia ? $caso->fecha_remision_psicologia->format('d/m/Y') : 'N/A' }}
                            @if($caso->observaciones_psicologia)
                                <br>Observaciones: {{ $caso->observaciones_psicologia }}
                            @endif
                        </p>
                    </div>
                    @endif

                    <!-- NOTIFICACIÓN AL ACUDIENTE -->
                    @if($caso->acudiente_notificado)
                    <div class="alert">
                        <p class="field-label">NOTIFICACIÓN AL ACUDIENTE:</p>
                        <p>
                            Fecha: {{ $caso->fecha_notificacion_acudiente ? $caso->fecha_notificacion_acudiente->format('d/m/Y') : 'N/A' }}<br>
                            Medio: {{ $caso->medio_notificacion ?? 'N/A' }}
                            @if($caso->respuesta_acudiente)
                                <br>Respuesta: {!! nl2br(e($caso->respuesta_acudiente)) !!}
                            @endif
                        </p>
                    </div>
                    @endif

                    <!-- ACUERDOS Y COMPROMISOS -->
                    <div class="content-section allow-break">
                        <h3 class="section-title">ACUERDOS Y COMPROMISOS</h3>

                        @if($caso->compromiso_acudiente || $caso->compromiso)
                            <p class="field-label">DEL ACUDIENTE:</p>
                            <p>{!! nl2br(e($caso->compromiso_acudiente ?? $caso->compromiso)) !!}</p>
                        @endif

                        @if($caso->compromiso_estudiante)
                            <p class="field-label mt-2">DEL ESTUDIANTE:</p>
                            <p>{!! nl2br(e($caso->compromiso_estudiante)) !!}</p>
                        @endif

                        @if($caso->compromiso_institucion)
                            <p class="field-label mt-2">DE LA INSTITUCIÓN:</p>
                            <p>{!! nl2br(e($caso->compromiso_institucion)) !!}</p>
                        @endif
                    </div>

                    <!-- FIRMAS -->
                    <div class="signatures-section">
                        <h3 class="section-title">FIRMAS</h3>

                        <table class="signatures-table">
                            <tr>
                                <td style="width: 50%;">
                                    <div class="signature-space"></div>
                                    <div class="signature-label">
                                        <strong>ESTUDIANTE</strong><br>
                                        {{ strtoupper($caso->estudiante->nombre_completo) }}<br>
                                        <small>{{ $caso->estudiante->tipo_documento }}. {{ $caso->estudiante->numero_documento }}</small>
                                    </div>
                                </td>
                                <td style="width: 50%;">
                                    <div class="signature-space"></div>
                                    <div class="signature-label">
                                        <strong>PADRE O ACUDIENTE</strong><br>
                                        {{ strtoupper($caso->estudiante->nombre_acudiente ?? 'SIN REGISTRO') }}<br>
                                        @if($caso->estudiante->numero_documento_acudiente)
                                            <small>C.C. {{ $caso->estudiante->numero_documento_acudiente }}</small>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="signature-space"></div>
                                    <div class="signature-label">
                                        <strong>COORDINADOR(A)</strong><br>
                                        {{ strtoupper($caso->reportadoPor->name ?? 'SIN ASIGNAR') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="signature-space"></div>
                                    <div class="signature-label">
                                        <strong>DIRECTOR(A) DE GRUPO</strong><br>
                                        {{ strtoupper($caso->estudiante->matriculaActual->grupo->directorGrupo->name ?? 'SIN ASIGNAR') }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="signature-space"></div>
                                    <div class="signature-label">
                                        <strong>PROFESIONAL PEEP / ORIENTACIÓN</strong>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- OBSERVACIONES GENERALES -->
                    @if($caso->observaciones_generales)
                    <div class="content-section mt-2">
                        <p class="field-label">OBSERVACIONES GENERALES:</p>
                        <p>{!! nl2br(e($caso->observaciones_generales)) !!}</p>
                    </div>
                    @endif

                    <!-- INFORMACIÓN DE PIE DE DOCUMENTO -->
                    <div class="text-center mt-2" style="font-size: 8pt; color: #666;">
                        <p>Este documento ha sido generado oficialmente por el Sistema de Gestión Institucional - IEJAG</p>
                        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
                    </div>

</body>
</html>
