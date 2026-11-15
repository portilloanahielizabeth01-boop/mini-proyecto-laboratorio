<?php

declare(strict_types=1);

namespace App\Reports;

/**
 * Agrega "PDF" al reporte que envuelve.
 */
final class PdfReportDecorator implements Report
{
    public function __construct(private Report $report)   // envuelve otro reporte
    {
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + PDF';
    }
}
