<?php

declare(strict_types=1);

namespace App\Pricing;

/**
 * Paciente con obra social: 30% de descuento (paga el 70%).
 *
 * El porcentaje vive en UN solo lugar: el valor por defecto del constructor.
 * Si el laboratorio lo cambia al 25%: new InsuranceStrategy(0.25).
 */
final class InsuranceStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.30)
    {
    }

    public function calculate(float $amount): float
    {
        return $amount * (1 - $this->discount);
    }
}
