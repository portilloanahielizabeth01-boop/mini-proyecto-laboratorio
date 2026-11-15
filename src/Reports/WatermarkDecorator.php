<?php

declare(strict_types=1);

namespace App\Reports;

/**
 * Agrega "marca de agua" al reporte que envuelve.
 */
final class WatermarkDecorator implements Report
{
    public function __construct(private Report $report)   // envuelve otro reporte
    {
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + marca de agua';
    }
}
