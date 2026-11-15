<?php

declare(strict_types=1);

namespace App\Reports;

/**
 * Agrega "firma digital" al reporte que envuelve.
 */
final class DigitalSignatureDecorator implements Report
{
    public function __construct(private Report $report)   // envuelve otro reporte
    {
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + firma digital';
    }
}
