<?php
/**
 * ============================================================================
 *  GENERACIÓN DE REPORTES
 *  Patrón aplicado: DECORATOR (estructural)
 * ============================================================================
 */

/**
 * 1. Contrato común para el reporte base y sus decoradores.
 */
interface Report
{
    public function generate(): string;
}

/**
 * 2. Reporte base (concreta): define la estructura inicial del reporte.
 */
final class BasicReport implements Report
{
    public function __construct(private string $contenido) {}

    public function generate(): string
    {
        return "Reporte: {$this->contenido}";
    }
}

/**
 * 3. Decorador Base / Clase Abstracta Opcional (o decoradores independientes):
 * Aquí implementaremos cada decorador individualmente siguiendo el contrato.
 */

final class DigitalSignatureDecorator implements Report
{
    public function __construct(private Report $report) {} // Envuelve otro reporte

    public function generate(): string
    {
        return $this->report->generate() . ' + firma digital';
    }
}

final class PdfReportDecorator implements Report
{
    public function __construct(private Report $report) {} // Envuelve otro reporte

    public function generate(): string
    {
        return $this->report->generate() . ' + PDF';
    }
}

final class WatermarkDecorator implements Report
{
    public function __construct(private Report $report) {} // Envuelve otro reporte

    public function generate(): string
    {
        return $this->report->generate() . ' + marca de agua';
    }
}

/*
 * ============================================================================
 *  EJEMPLO DE USO (Encadenamiento dinámico)
 * ============================================================================
 */

// Creamos el reporte base y lo vamos envolviendo dinámicamente según lo que necesitemos:
$reporte = new BasicReport('Pedido 1');
$reporte = new DigitalSignatureDecorator($reporte);
$reporte = new PdfReportDecorator($reporte);
$reporte = new WatermarkDecorator($reporte);

echo $reporte->generate();
// Resultado: Reporte: Pedido 1 + firma digital + PDF + marca de agua
