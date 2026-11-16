<?php

declare(strict_types=1);

namespace App\Reports;

/**
 * Contrato común del reporte base y de sus decoradores (patrón DECORATOR).
 */
interface Report
{
    public function generate(): string;
}
