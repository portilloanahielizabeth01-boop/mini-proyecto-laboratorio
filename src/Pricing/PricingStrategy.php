<?php

declare(strict_types=1);

namespace App\Pricing;

/**
 * Contrato común de todos los algoritmos de precio (patrón STRATEGY).
 *
 * Cada tipo de paciente es una clase que implementa esta interfaz.
 * PriceCalculator solo conoce este contrato, nunca las clases concretas.
 */
interface PricingStrategy
{
    public function calculate(float $amount): float;
}
