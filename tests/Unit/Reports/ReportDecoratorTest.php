<?php

declare(strict_types=1);

namespace Tests\Unit\Reports;

use App\Reports\BasicReport;
use App\Reports\DigitalSignatureDecorator;
use App\Reports\PdfReportDecorator;
use App\Reports\WatermarkDecorator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F8 — Generación de reportes (patrón Decorator).
 */
final class ReportDecoratorTest extends TestCase
{
    #[Test]
    #[TestDox('CP-27 (+) El reporte base no tiene agregados')]
    public function reporteBaseSinAgregados(): void
    {
        $this->assertSame('Reporte: Pedido 1', (new BasicReport('Pedido 1'))->generate());
    }

    #[Test]
    #[TestDox('CP-28 (+) Se pueden encadenar firma, PDF y marca de agua')]
    public function seEncadenanLosTresAgregados(): void
    {
        $reporte = new WatermarkDecorator(
            new PdfReportDecorator(
                new DigitalSignatureDecorator(new BasicReport('Pedido 1'))
            )
        );

        $this->assertSame(
            'Reporte: Pedido 1 + firma digital + PDF + marca de agua',
            $reporte->generate()
        );
    }

    #[Test]
    #[TestDox('CP-29 (-) Envolver en otro orden da un resultado distinto (consecuencia del patrón)')]
    public function elOrdenCambiaElResultado(): void
    {
        $firmarYPdf = new PdfReportDecorator(new DigitalSignatureDecorator(new BasicReport('X')));
        $pdfYFirmar = new DigitalSignatureDecorator(new PdfReportDecorator(new BasicReport('X')));

        $this->assertNotSame($firmarYPdf->generate(), $pdfYFirmar->generate());
    }
}
