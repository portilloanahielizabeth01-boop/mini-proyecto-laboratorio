<?php

declare(strict_types=1);

namespace App\Reports;

/**
 * ============================================================================
 *  GENERACIÓN DE REPORTES
 *  Patrón aplicado: DECORATOR (estructural)
 * ============================================================================
 *
 *  Antes: generate('Pedidos', true, false, true) -> nadie sabía qué era cada
 *  booleano ("boolean trap") y cada agregado nuevo sumaba un parámetro y un if.
 *
 *  Ahora: un reporte base que se va "envolviendo" con decoradores:
 *    $r = new WatermarkDecorator(new PdfReportDecorator(
 *             new DigitalSignatureDecorator(new BasicReport('Pedidos del dia'))));
 *
 *  ⚠️ Consecuencia negativa: el ORDEN en que se envuelve cambia el resultado
 *  (firmar y después pasar a PDF no es lo mismo que al revés).
 * ============================================================================
 */
final class BasicReport implements Report
{
    public function __construct(private string $contenido)
    {
    }

    public function generate(): string
    {
        return "Reporte: {$this->contenido}";
    }
}
